<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductScreenshot;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class ProductImageService
{
    public function storeUploadedImage(UploadedFile $file, string $slug): array
    {
        $baseName = $this->makeBaseName($slug);

        return $this->persistProductImageSet($file->getRealPath(), $baseName);
    }

    public function createPlaceholderSet(string $slug, string $title, string $category, array $techStacks = []): array
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'psc-product-');

        if ($temporaryPath === false) {
            throw new RuntimeException('Gagal membuat file sementara untuk placeholder produk.');
        }

        $this->generatePlaceholderGraphic(
            $temporaryPath,
            $title,
            $category,
            $techStacks,
            'COVER PRODUK',
        );

        $paths = $this->persistProductImageSet($temporaryPath, $this->makeBaseName($slug.'-seed'));

        @unlink($temporaryPath);

        return $paths;
    }

    public function storeUploadedScreenshot(UploadedFile $file, string $slug): array
    {
        $baseName = $this->makeBaseName($slug.'-screenshot');

        return $this->persistScreenshotSet($file->getRealPath(), $baseName);
    }

    public function createScreenshotPlaceholderSet(string $slug, string $title, string $category, int $sequence): array
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'psc-screenshot-');

        if ($temporaryPath === false) {
            throw new RuntimeException('Gagal membuat file sementara untuk screenshot produk.');
        }

        $this->generatePlaceholderGraphic(
            $temporaryPath,
            $title,
            $category,
            [],
            'SCREENSHOT '.str_pad((string) $sequence, 2, '0', STR_PAD_LEFT),
        );

        $paths = $this->persistScreenshotSet($temporaryPath, $this->makeBaseName($slug."-screen-{$sequence}-seed"));

        @unlink($temporaryPath);

        return $paths;
    }

    public function deleteImageSet(Product $product): void
    {
        $this->deletePaths([
            $product->image_path,
            $product->list_thumbnail_path,
            $product->detail_thumbnail_path,
        ]);
    }

    public function deleteScreenshotSet(ProductScreenshot $screenshot): void
    {
        $this->deletePaths([
            $screenshot->image_path,
            $screenshot->thumbnail_path,
        ]);
    }

    private function persistProductImageSet(string $sourcePath, string $baseName): array
    {
        $imagePath = "products/originals/{$baseName}.webp";
        $listThumbnailPath = "products/list/{$baseName}.webp";
        $detailThumbnailPath = "products/detail/{$baseName}.webp";

        $this->createContainedVersion($sourcePath, Storage::disk('public')->path($imagePath), 1600, 1200);
        $this->createCoverVersion($sourcePath, Storage::disk('public')->path($listThumbnailPath), 720, 460);
        $this->createCoverVersion($sourcePath, Storage::disk('public')->path($detailThumbnailPath), 1440, 900);

        return [
            'image_path' => $imagePath,
            'list_thumbnail_path' => $listThumbnailPath,
            'detail_thumbnail_path' => $detailThumbnailPath,
        ];
    }

    private function persistScreenshotSet(string $sourcePath, string $baseName): array
    {
        $imagePath = "products/screenshots/originals/{$baseName}.webp";
        $thumbnailPath = "products/screenshots/thumbs/{$baseName}.webp";

        $this->createContainedVersion($sourcePath, Storage::disk('public')->path($imagePath), 1600, 1200);
        $this->createCoverVersion($sourcePath, Storage::disk('public')->path($thumbnailPath), 760, 460);

        return [
            'image_path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
        ];
    }

    private function createContainedVersion(string $sourcePath, string $destinationPath, int $maxWidth, int $maxHeight): void
    {
        [$sourceImage, $sourceWidth, $sourceHeight] = $this->openSourceImage($sourcePath);

        $scale = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight, 1);
        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));

        $destinationImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($destinationImage, false);
        imagesavealpha($destinationImage, true);

        $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
        imagefilledrectangle($destinationImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        );

        $this->writeWebp($destinationImage, $destinationPath);

        imagedestroy($sourceImage);
        imagedestroy($destinationImage);
    }

    private function createCoverVersion(string $sourcePath, string $destinationPath, int $targetWidth, int $targetHeight): void
    {
        [$sourceImage, $sourceWidth, $sourceHeight] = $this->openSourceImage($sourcePath);

        $sourceRatio = $sourceWidth / $sourceHeight;
        $targetRatio = $targetWidth / $targetHeight;

        if ($sourceRatio > $targetRatio) {
            $cropHeight = $sourceHeight;
            $cropWidth = (int) round($sourceHeight * $targetRatio);
            $cropX = (int) round(($sourceWidth - $cropWidth) / 2);
            $cropY = 0;
        } else {
            $cropWidth = $sourceWidth;
            $cropHeight = (int) round($sourceWidth / $targetRatio);
            $cropX = 0;
            $cropY = (int) round(($sourceHeight - $cropHeight) / 2);
        }

        $destinationImage = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($destinationImage, false);
        imagesavealpha($destinationImage, true);

        $transparent = imagecolorallocatealpha($destinationImage, 255, 255, 255, 127);
        imagefilledrectangle($destinationImage, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $destinationImage,
            $sourceImage,
            0,
            0,
            $cropX,
            $cropY,
            $targetWidth,
            $targetHeight,
            $cropWidth,
            $cropHeight,
        );

        $this->writeWebp($destinationImage, $destinationPath);

        imagedestroy($sourceImage);
        imagedestroy($destinationImage);
    }

    private function generatePlaceholderGraphic(
        string $destinationPath,
        string $title,
        string $category,
        array $techStacks = [],
        string $badge = 'PUSAT SOURCE CODE',
    ): void
    {
        $width = 1600;
        $height = 900;

        $image = imagecreatetruecolor($width, $height);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $background = imagecolorallocate($image, 12, 74, 110);
        imagefilledrectangle($image, 0, 0, $width, $height, $background);

        $teal = imagecolorallocate($image, 45, 212, 191);
        $amber = imagecolorallocate($image, 245, 158, 11);
        $sky = imagecolorallocate($image, 56, 189, 248);
        $white = imagecolorallocate($image, 255, 255, 255);
        $muted = imagecolorallocate($image, 218, 244, 238);
        $panel = imagecolorallocate($image, 255, 255, 255);

        imagefilledellipse($image, 240, 180, 340, 340, $teal);
        imagefilledellipse($image, 1380, 120, 260, 260, $amber);
        imagefilledellipse($image, 1340, 760, 420, 420, $sky);

        imagefilledrectangle($image, 90, 90, 1510, 810, $panel);
        imagefilledrectangle($image, 90, 90, 1510, 250, $teal);

        imagestring($image, 5, 130, 130, strtoupper($category), $white);
        imagestring($image, 4, 130, 176, $badge, $muted);

        $wrappedTitle = $this->wrapText($title, 28);
        $titleY = 320;
        foreach ($wrappedTitle as $line) {
            imagestring($image, 5, 130, $titleY, Str::upper($line), imagecolorallocate($image, 15, 23, 42));
            $titleY += 34;
        }

        imagestring($image, 4, 130, 520, 'Thumbnail otomatis untuk katalog dan detail produk', imagecolorallocate($image, 71, 85, 105));

        $techY = 610;
        foreach (array_slice($techStacks, 0, 3) as $stack) {
            imagefilledrectangle($image, 130, $techY, 460, $techY + 48, imagecolorallocate($image, 239, 246, 255));
            imagestring($image, 5, 150, $techY + 15, $stack, imagecolorallocate($image, 29, 78, 216));
            $techY += 70;
        }

        $this->writeWebp($image, $destinationPath);

        imagedestroy($image);
    }

    private function deletePaths(array $paths): void
    {
        collect($paths)
            ->filter()
            ->unique()
            ->each(fn (string $path) => Storage::disk('public')->delete($path));
    }

    private function openSourceImage(string $sourcePath): array
    {
        $binary = file_get_contents($sourcePath);

        if ($binary === false) {
            throw new RuntimeException('Gagal membaca file gambar sumber.');
        }

        $image = @imagecreatefromstring($binary);

        if (! $image) {
            throw new RuntimeException('Format gambar tidak didukung untuk diproses.');
        }

        $imageSize = getimagesize($sourcePath);

        if ($imageSize === false) {
            imagedestroy($image);
            throw new RuntimeException('Gagal membaca dimensi gambar sumber.');
        }

        return [$image, $imageSize[0], $imageSize[1]];
    }

    private function writeWebp(\GdImage $image, string $destinationPath): void
    {
        File::ensureDirectoryExists(dirname($destinationPath));

        if (! imagewebp($image, $destinationPath, 86)) {
            throw new RuntimeException('Gagal menyimpan file thumbnail produk.');
        }
    }

    private function makeBaseName(string $slug): string
    {
        $baseSlug = Str::slug($slug);

        if ($baseSlug === '') {
            $baseSlug = 'produk';
        }

        return $baseSlug.'-'.Str::lower(Str::random(10));
    }

    private function wrapText(string $value, int $lineLength): array
    {
        return preg_split("/\r\n|\r|\n/", wordwrap($value, $lineLength, "\n", true)) ?: [$value];
    }
}
