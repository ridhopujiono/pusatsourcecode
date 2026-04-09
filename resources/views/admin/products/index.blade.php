@extends('layouts.admin')

@section('title', 'Manajemen Produk | PSC Admin')
@section('page-title', 'Manajemen Produk')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Kelola seluruh katalog produk</h2>
                <p class="mt-2 text-sm leading-7 text-slate-600">
                    Cari produk, ubah status tayang, perbarui urutan katalog, dan lakukan edit cepat dari satu halaman.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                @if ($products->count() > 0)
                    <button type="button" id="save-order-btn" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-teal-200 hover:text-teal-700">
                        Simpan Urutan
                    </button>
                @endif
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-teal-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-teal-700">
                    + Tambah Produk
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" class="psc-card p-5">
            <div class="flex flex-col gap-4 md:flex-row">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Cari berdasarkan judul atau kategori..."
                    class="psc-input"
                >
                <button type="submit" class="psc-btn-primary md:w-auto">
                    Cari
                </button>
            </div>
        </form>

        <div class="psc-card overflow-hidden">
            @if ($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-slate-500">
                            <tr>
                                <th class="px-5 py-4 font-semibold">No.</th>
                                <th class="px-5 py-4 font-semibold">Judul</th>
                                <th class="px-5 py-4 font-semibold">Kategori</th>
                                <th class="px-5 py-4 font-semibold">Tech Stack</th>
                                <th class="px-5 py-4 font-semibold">Harga</th>
                                <th class="px-5 py-4 font-semibold">Status</th>
                                <th class="px-5 py-4 font-semibold">Urutan</th>
                                <th class="px-5 py-4 font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach ($products as $product)
                                <tr class="odd:bg-white even:bg-slate-50/70">
                                    <td class="px-5 py-4 align-top text-slate-500">
                                        {{ ($products->currentPage() - 1) * $products->perPage() + $loop->iteration }}
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex min-w-[18rem] items-start gap-4">
                                            @if ($product->list_thumbnail_url)
                                                <img src="{{ $product->list_thumbnail_url }}" alt="{{ $product->title }}" class="h-16 w-24 rounded-2xl object-cover shadow-sm">
                                            @else
                                                <div class="flex h-16 w-24 items-center justify-center rounded-2xl bg-slate-100 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-400">
                                                    No Image
                                                </div>
                                            @endif

                                            <div>
                                                <a href="{{ route('admin.products.edit', $product) }}" class="font-semibold text-slate-900 transition hover:text-teal-700">
                                                    {{ $product->title }}
                                                </a>
                                                <p class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-400">{{ $product->slug }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <span class="rounded-full border border-amber-200 bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                                            {{ $product->category }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex max-w-xs flex-wrap gap-2">
                                            @foreach (collect($product->tech_stack)->take(3) as $stack)
                                                <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                                    {{ $stack }}
                                                </span>
                                            @endforeach
                                            @if (count($product->tech_stack) > 3)
                                                <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-500">
                                                    +{{ count($product->tech_stack) - 3 }} lagi
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 align-top font-semibold text-slate-900">
                                        {{ $product->price }}
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex items-center gap-3">
                                            <button
                                                type="button"
                                                onclick="toggleProduct({{ $product->id }}, this)"
                                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $product->is_active ? 'bg-teal-500' : 'bg-slate-300' }}"
                                                data-active="{{ $product->is_active ? 'true' : 'false' }}"
                                            >
                                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $product->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                            </button>
                                            <span class="status-label text-xs font-semibold {{ $product->is_active ? 'text-teal-700' : 'text-slate-500' }}">
                                                {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <input
                                            type="number"
                                            min="0"
                                            value="{{ $product->sort_order }}"
                                            data-id="{{ $product->id }}"
                                            class="sort-order-input w-24 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm shadow-sm outline-none transition focus:border-teal-500 focus:ring-4 focus:ring-teal-100"
                                        >
                                    </td>
                                    <td class="px-5 py-4 align-top">
                                        <div class="flex flex-wrap gap-3">
                                            <a href="{{ route('admin.products.edit', $product) }}" class="rounded-xl bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.products.destroy', $product) }}"
                                                onsubmit="return confirm('Hapus produk ini? Tindakan tidak dapat dibatalkan.')"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-xl bg-red-50 px-4 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-5 py-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="px-8 py-16 text-center">
                    <p class="text-lg font-semibold text-slate-900">Belum ada produk</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Tambahkan produk pertama untuk mulai mengisi katalog publik.
                    </p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function toggleProduct(id, button) {
            const response = await fetch(`/admin/products/${id}/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                alert('Gagal memperbarui status produk.');
                return;
            }

            const data = await response.json();
            const isActive = Boolean(data.is_active);
            const handle = button.querySelector('span');
            const label = button.parentElement.querySelector('.status-label');

            button.classList.toggle('bg-teal-500', isActive);
            button.classList.toggle('bg-slate-300', !isActive);
            handle.classList.toggle('translate-x-6', isActive);
            handle.classList.toggle('translate-x-1', !isActive);
            label.textContent = isActive ? 'Aktif' : 'Nonaktif';
            label.classList.toggle('text-teal-700', isActive);
            label.classList.toggle('text-slate-500', !isActive);
        }

        const saveOrderButton = document.getElementById('save-order-btn');

        if (saveOrderButton) {
            saveOrderButton.addEventListener('click', async () => {
                const orders = Array.from(document.querySelectorAll('.sort-order-input')).map((input) => ({
                    id: Number(input.dataset.id),
                    sort_order: Number(input.value),
                }));

                saveOrderButton.disabled = true;
                saveOrderButton.textContent = 'Menyimpan...';

                const response = await fetch(@json(route('admin.products.reorder')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ orders }),
                });

                saveOrderButton.disabled = false;
                saveOrderButton.textContent = 'Simpan Urutan';

                if (!response.ok) {
                    alert('Gagal menyimpan urutan produk.');
                    return;
                }

                window.location.reload();
            });
        }
    </script>
@endpush
