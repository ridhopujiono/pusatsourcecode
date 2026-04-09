<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductSourceCodeService
{
    public function storeUploadedFile(UploadedFile $file, string $slug): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'zip');
        $relativePath = 'products/source-codes/'.$this->makeBaseName($slug).'.'.$extension;

        Storage::disk('local')->putFileAs(
            dirname($relativePath),
            $file,
            basename($relativePath),
        );

        return [
            'source_code_path' => $relativePath,
            'source_code_original_name' => $file->getClientOriginalName(),
        ];
    }

    public function createPlaceholderFile(string $slug, string $title): array
    {
        $relativePath = 'products/source-codes/'.$this->makeBaseName($slug.'-demo').'.txt';

        Storage::disk('local')->put(
            $relativePath,
            implode(PHP_EOL, [
                'Pusat Source Code',
                "Produk: {$title}",
                '',
                'File ini adalah placeholder package untuk environment development.',
                'Ganti file ini dari dashboard admin dengan source code archive asli sebelum dipakai di production.',
            ]),
        );

        return [
            'source_code_path' => $relativePath,
            'source_code_original_name' => Str::slug($slug).'-demo-package.txt',
        ];
    }

    public function duplicateForOrder(?string $sourcePath, ?string $sourceName, string $orderNumber, string $productSlug): array
    {
        if (! $sourcePath || ! Storage::disk('local')->exists($sourcePath)) {
            return [
                'download_path' => null,
                'download_name' => $sourceName,
            ];
        }

        $extension = pathinfo($sourceName ?: $sourcePath, PATHINFO_EXTENSION);
        $relativePath = 'orders/'.$orderNumber.'/'.$this->makeBaseName($productSlug.'-download').($extension ? '.'.$extension : '');

        Storage::disk('local')->copy($sourcePath, $relativePath);

        return [
            'download_path' => $relativePath,
            'download_name' => $sourceName ?: basename($relativePath),
        ];
    }

    public function deleteFile(?string $path): void
    {
        if (! $path) {
            return;
        }

        Storage::disk('local')->delete($path);
    }

    public function download(string $path, string $downloadName): BinaryFileResponse
    {
        if (! Storage::disk('local')->exists($path)) {
            throw new RuntimeException('File source code tidak ditemukan di storage.');
        }

        return response()->download(Storage::disk('local')->path($path), $downloadName);
    }

    private function makeBaseName(string $slug): string
    {
        return Str::slug($slug).'-'.Str::lower(Str::random(12));
    }
}
