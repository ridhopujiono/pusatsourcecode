@extends('layouts.admin')

@php
    $isEdit = isset($product);
@endphp

@section('title', ($isEdit ? 'Edit Produk' : 'Tambah Produk').' | PSC Admin')
@section('page-title', $isEdit ? 'Edit Produk' : 'Tambah Produk')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">{{ $isEdit ? 'Perbarui data produk' : 'Buat produk baru' }}</h2>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    Lengkapi informasi produk agar tampil rapi di katalog publik dan halaman detail.
                </p>
            </div>

            <div class="flex gap-3">
                @if ($isEdit)
                    <a href="{{ route('product.show', $product->slug) }}" target="_blank" rel="noreferrer" class="psc-btn-outline">
                        Lihat Halaman Publik
                    </a>
                @endif
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-teal-200 hover:text-teal-700">
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form
            method="POST"
            action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}"
            enctype="multipart/form-data"
            class="psc-card p-6 lg:p-8"
        >
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label for="title" class="mb-2 block text-sm font-semibold text-slate-700">Judul Produk</label>
                    <input id="title" name="title" type="text" value="{{ old('title', $product->title ?? '') }}" class="psc-input" required maxlength="255">
                </div>

                <div>
                    <label for="slug" class="mb-2 block text-sm font-semibold text-slate-700">Slug</label>
                    <input id="slug" name="slug" type="text" value="{{ old('slug', $product->slug ?? '') }}" class="psc-input" required maxlength="255">
                </div>

                <div>
                    <label for="category" class="mb-2 block text-sm font-semibold text-slate-700">Kategori</label>
                    <input id="category" name="category" type="text" value="{{ old('category', $product->category ?? '') }}" class="psc-input" required maxlength="100">
                </div>

                <div class="lg:col-span-2">
                    <label for="description" class="mb-2 block text-sm font-semibold text-slate-700">Deskripsi</label>
                    <textarea id="description" name="description" class="psc-textarea" required>{{ old('description', $product->description ?? '') }}</textarea>
                </div>

                <div class="lg:col-span-2">
                    <label for="product_image" class="mb-2 block text-sm font-semibold text-slate-700">Gambar Produk</label>
                    <input
                        id="product_image"
                        name="product_image"
                        type="file"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        class="psc-input file:mr-4 file:rounded-xl file:border-0 file:bg-teal-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-teal-700"
                    >
                    <p class="mt-2 text-xs leading-6 text-slate-500">
                        Upload satu gambar utama. Sistem akan otomatis membuat 2 thumbnail: versi list untuk katalog dan versi detail untuk halaman produk.
                    </p>
                </div>

                <div class="lg:col-span-2">
                    <label for="source_code_file" class="mb-2 block text-sm font-semibold text-slate-700">File Source Code</label>
                    <input
                        id="source_code_file"
                        name="source_code_file"
                        type="file"
                        accept=".zip,.rar,.7z,application/zip,application/x-rar-compressed,application/x-7z-compressed"
                        class="psc-input file:mr-4 file:rounded-xl file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700"
                    >
                    <p class="mt-2 text-xs leading-6 text-slate-500">
                        Upload file archive yang akan diunduh pembeli setelah pesanan lunas. Format yang didukung: ZIP, RAR, dan 7Z.
                    </p>

                    @if ($isEdit && $product->source_code_original_name)
                        <div class="mt-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            File saat ini: <span class="font-semibold">{{ $product->source_code_original_name }}</span>
                        </div>
                    @endif
                </div>

                <div class="lg:col-span-2">
                    <label class="mb-2 block text-sm font-semibold text-slate-700">List Screenshot Produk</label>

                    @if ($isEdit)
                        <input
                            id="instant_screenshot_upload"
                            type="file"
                            multiple
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            data-upload-url="{{ route('admin.products.screenshots.store', $product) }}"
                            class="psc-input file:mr-4 file:rounded-xl file:border-0 file:bg-sky-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-sky-700"
                        >
                        <p id="instant-screenshot-status" class="mt-2 text-xs leading-6 text-slate-500">
                            Anda bisa pilih banyak file sekaligus. Sistem akan mengupload screenshot satu per satu secara otomatis ke produk ini.
                        </p>
                    @else
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                            Simpan produk terlebih dahulu, lalu upload screenshot satu per satu dari halaman edit produk.
                        </div>
                    @endif
                </div>

                @if ($isEdit && ($product->list_thumbnail_url || $product->detail_thumbnail_url))
                    <div class="lg:col-span-2">
                        <div class="grid gap-4 lg:grid-cols-2">
                            <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Thumbnail List</p>
                                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                    @if ($product->list_thumbnail_url)
                                        <img src="{{ $product->list_thumbnail_url }}" alt="{{ $product->title }}" class="h-48 w-full object-cover">
                                    @endif
                                </div>
                            </div>
                            <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Thumbnail Detail</p>
                                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                    @if ($product->detail_thumbnail_url)
                                        <img src="{{ $product->detail_thumbnail_url }}" alt="{{ $product->title }}" class="h-48 w-full object-cover">
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($isEdit && $product->screenshots->isNotEmpty())
                    <div class="lg:col-span-2">
                        <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Screenshot Saat Ini</p>
                                    <p class="mt-1 text-xs leading-6 text-slate-500">
                                        Upload dilakukan satu per satu. Untuk menghapus screenshot, centang item yang diinginkan lalu simpan perubahan.
                                    </p>
                                </div>
                                <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-600">
                                    {{ $product->screenshots->count() }} file
                                </span>
                            </div>

                            <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                                @foreach ($product->screenshots as $screenshot)
                                    <label class="block overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                        @if ($screenshot->thumbnail_url)
                                            <img src="{{ $screenshot->thumbnail_url }}" alt="Screenshot {{ $loop->iteration }} {{ $product->title }}" class="h-40 w-full object-cover">
                                        @endif
                                        <div class="space-y-3 p-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">
                                                    Screenshot {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                                </span>
                                                <a href="{{ $screenshot->image_url }}" target="_blank" rel="noreferrer" class="text-xs font-semibold text-teal-700 transition hover:text-teal-800">
                                                    Lihat Full
                                                </a>
                                            </div>
                                            <span class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-700">
                                                <input
                                                    type="checkbox"
                                                    name="delete_screenshot_ids[]"
                                                    value="{{ $screenshot->id }}"
                                                    class="h-4 w-4 rounded border-red-300 text-red-600 focus:ring-red-500"
                                                    @checked(in_array($screenshot->id, old('delete_screenshot_ids', [])))
                                                >
                                                Hapus screenshot ini
                                            </span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <label for="price" class="mb-2 block text-sm font-semibold text-slate-700">Harga Display</label>
                    <input id="price" name="price" type="text" value="{{ old('price', $product->price ?? '') }}" class="psc-input" required maxlength="50" placeholder="Rp 1.290.000">
                </div>

                <div>
                    <label for="price_numeric" class="mb-2 block text-sm font-semibold text-slate-700">Harga Numeric</label>
                    <input id="price_numeric" name="price_numeric" type="number" min="0" value="{{ old('price_numeric', $product->price_numeric ?? 0) }}" class="psc-input" required>
                </div>

                <div>
                    <label for="updated_label" class="mb-2 block text-sm font-semibold text-slate-700">Label Update</label>
                    <input id="updated_label" name="updated_label" type="text" value="{{ old('updated_label', $product->updated_label ?? '') }}" class="psc-input" required maxlength="100" placeholder="Maret 2026">
                </div>

                <div>
                    <label for="delivery" class="mb-2 block text-sm font-semibold text-slate-700">Paket Delivery</label>
                    <input id="delivery" name="delivery" type="text" value="{{ old('delivery', $product->delivery ?? '') }}" class="psc-input" required maxlength="255">
                </div>

                <div class="lg:col-span-2">
                    <label for="tech_stack" class="mb-2 block text-sm font-semibold text-slate-700">Tech Stack</label>
                    <input
                        id="tech_stack"
                        name="tech_stack"
                        type="text"
                        value="{{ old('tech_stack', isset($product) ? implode(', ', $product->tech_stack ?? []) : '') }}"
                        class="psc-input"
                        required
                        placeholder="Laravel, MySQL, Bootstrap"
                    >
                </div>

                <div class="lg:col-span-2">
                    <label for="features" class="mb-2 block text-sm font-semibold text-slate-700">Fitur Utama</label>
                    <textarea
                        id="features"
                        name="features"
                        class="psc-textarea"
                        required
                        placeholder="Satu fitur per baris"
                    >{{ old('features', isset($product) ? implode("\n", $product->features ?? []) : '') }}</textarea>
                </div>

                <div>
                    <label for="sort_order" class="mb-2 block text-sm font-semibold text-slate-700">Urutan Tampil</label>
                    <input id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="psc-input">
                </div>

                <div class="flex items-end">
                    <label for="is_active" class="flex w-full items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                        <input id="is_active" name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-500" @checked(old('is_active', isset($product) ? $product->is_active : true))>
                        Tampilkan produk di website publik
                    </label>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button type="submit" class="psc-btn-primary">
                    {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Produk' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="psc-btn-outline">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const titleField = document.getElementById('title');
        const slugField = document.getElementById('slug');
        const screenshotUploadInput = document.getElementById('instant_screenshot_upload');
        const screenshotStatus = document.getElementById('instant-screenshot-status');
        let slugManuallyEdited = slugField.value.trim() !== '';

        function slugify(value) {
            return value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        titleField.addEventListener('input', () => {
            if (!slugManuallyEdited) {
                slugField.value = slugify(titleField.value);
            }
        });

        slugField.addEventListener('input', () => {
            slugManuallyEdited = slugField.value.trim() !== '';
            slugField.value = slugify(slugField.value);
        });

        if (screenshotUploadInput) {
            screenshotUploadInput.addEventListener('change', async () => {
                const files = Array.from(screenshotUploadInput.files);

                if (files.length === 0) {
                    return;
                }

                screenshotUploadInput.disabled = true;
                screenshotStatus.textContent = `Menyiapkan upload ${files.length} screenshot...`;
                screenshotStatus.className = 'mt-2 text-xs leading-6 text-sky-700';

                try {
                    const failures = [];

                    for (const [index, file] of files.entries()) {
                        screenshotStatus.textContent = `Mengupload screenshot ${index + 1} dari ${files.length}: ${file.name}`;

                        const formData = new FormData();
                        formData.append('screenshot', file);

                        const response = await fetch(screenshotUploadInput.dataset.uploadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: formData,
                        });

                        const payload = await response.json().catch(() => ({}));

                        if (!response.ok) {
                            const message = payload.message || payload.errors?.screenshot?.[0] || `Upload gagal untuk ${file.name}.`;
                            failures.push(message);
                        }
                    }

                    if (failures.length > 0) {
                        throw new Error(failures.join(' | '));
                    }

                    screenshotStatus.textContent = `Semua screenshot berhasil diupload. Memuat ulang halaman...`;
                    window.location.reload();
                } catch (error) {
                    screenshotUploadInput.disabled = false;
                    screenshotUploadInput.value = '';
                    screenshotStatus.textContent = error.message;
                    screenshotStatus.className = 'mt-2 text-xs leading-6 text-red-600';
                }
            });
        }
    </script>
@endpush
