<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductImageService;
use App\Services\ProductSourceCodeService;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(
        private ProductImageService $productImageService,
        private ProductSourceCodeService $productSourceCodeService,
    ) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $query = Product::query()
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(10)->withQueryString();

        return view('admin.products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        return view('admin.products.form');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $this->prepareData($request);

        if ($request->hasFile('product_image')) {
            $data = [
                ...$data,
                ...$this->productImageService->storeUploadedImage($request->file('product_image'), $data['slug']),
            ];
        }

        if ($request->hasFile('source_code_file')) {
            $data = [
                ...$data,
                ...$this->productSourceCodeService->storeUploadedFile($request->file('source_code_file'), $data['slug']),
            ];
        }

        $product = Product::create($data);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $product->load('screenshots');

        return view('admin.products.form', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $this->prepareData($request, $product->id);

        if ($request->hasFile('product_image')) {
            $this->productImageService->deleteImageSet($product);

            $data = [
                ...$data,
                ...$this->productImageService->storeUploadedImage($request->file('product_image'), $data['slug']),
            ];
        }

        if ($request->hasFile('source_code_file')) {
            $this->productSourceCodeService->deleteFile($product->source_code_path);

            $data = [
                ...$data,
                ...$this->productSourceCodeService->storeUploadedFile($request->file('source_code_file'), $data['slug']),
            ];
        }

        $product->update($data);

        $this->deleteSelectedScreenshots($product, $request->input('delete_screenshot_ids', []));

        $this->normalizeScreenshotOrder($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function storeScreenshot(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'screenshot' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $this->storeUploadedScreenshot($product, $validated['screenshot']);

        return response()->json([
            'message' => 'Screenshot berhasil diupload.',
        ]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->load('screenshots');

        $this->productImageService->deleteImageSet($product);
        $this->productSourceCodeService->deleteFile($product->source_code_path);
        foreach ($product->screenshots as $screenshot) {
            $this->productImageService->deleteScreenshotSet($screenshot);
        }
        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function toggle(Product $product): JsonResponse
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return response()->json([
            'is_active' => $product->is_active,
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orders' => ['required', 'array'],
            'orders.*.id' => ['required', 'integer', 'exists:products,id'],
            'orders.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated): void {
            foreach ($validated['orders'] as $order) {
                Product::query()
                    ->whereKey($order['id'])
                    ->update(['sort_order' => $order['sort_order']]);
            }
        });

        return response()->json([
            'message' => 'Urutan produk berhasil diperbarui.',
        ]);
    }

    private function prepareData(Request $request, ?int $excludeId = null): array
    {
        return [
            'title' => $request->input('title'),
            'slug' => $this->resolveUniqueSlug((string) $request->input('slug', $request->input('title')), $excludeId),
            'description' => $request->input('description'),
            'price' => $request->input('price'),
            'price_numeric' => (int) $request->input('price_numeric'),
            'category' => $request->input('category'),
            'tech_stack' => $this->parseCommaSeparated((string) $request->input('tech_stack')),
            'features' => $this->parseLineSeparated((string) $request->input('features')),
            'delivery' => $request->input('delivery'),
            'updated_label' => $request->input('updated_label'),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => (int) $request->input('sort_order', 0),
        ];
    }

    private function resolveUniqueSlug(string $value, ?int $excludeId = null): string
    {
        $slug = Str::slug($value);

        if ($slug === '') {
            $slug = 'produk';
        }

        $original = $slug;
        $count = 1;

        while (
            Product::query()
                ->where('slug', $slug)
                ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $original.'-'.$count;
            $count++;
        }

        return $slug;
    }

    private function parseCommaSeparated(string $value): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }

    private function parseLineSeparated(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split("/\r\n|\r|\n/", $value) ?: [])));
    }

    private function storeUploadedScreenshot(Product $product, UploadedFile $file): void
    {
        $nextSortOrder = ((int) ($product->screenshots()->max('sort_order') ?? -1)) + 1;

        $paths = $this->productImageService->storeUploadedScreenshot($file, $product->slug);

        $product->screenshots()->create([
            ...$paths,
            'sort_order' => $nextSortOrder,
        ]);
    }

    private function deleteSelectedScreenshots(Product $product, array $ids): void
    {
        if ($ids === []) {
            return;
        }

        $screenshots = $product->screenshots()
            ->whereIn('id', $ids)
            ->get();

        foreach ($screenshots as $screenshot) {
            $this->productImageService->deleteScreenshotSet($screenshot);
            $screenshot->delete();
        }
    }

    private function normalizeScreenshotOrder(Product $product): void
    {
        $product->screenshots()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->values()
            ->each(function ($screenshot, int $index): void {
                $screenshot->update(['sort_order' => $index]);
            });
    }
}
