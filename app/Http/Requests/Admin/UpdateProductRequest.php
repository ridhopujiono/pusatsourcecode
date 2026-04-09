<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => ['required', 'string'],
            'price' => ['required', 'string', 'max:50'],
            'price_numeric' => ['required', 'integer', 'min:0'],
            'category' => ['required', 'string', 'max:100'],
            'tech_stack' => ['required', 'string'],
            'features' => ['required', 'string'],
            'delivery' => ['required', 'string', 'max:255'],
            'updated_label' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'product_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'source_code_file' => ['nullable', 'file', 'extensions:zip,rar,7z', 'max:102400'],
            'delete_screenshot_ids' => ['nullable', 'array'],
            'delete_screenshot_ids.*' => ['integer', 'exists:product_screenshots,id'],
        ];
    }
}
