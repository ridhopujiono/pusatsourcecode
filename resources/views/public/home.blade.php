@extends('layouts.public')

@section('title', 'Pusat Source Code | Katalog Source Code Siap Deploy')
@section('meta_description', 'Temukan source code siap deploy untuk retail, e-commerce, klinik, edukasi, dan UMKM, lalu checkout langsung dengan Midtrans Snap.')

@section('content')
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(45,212,191,0.28),_transparent_36%),radial-gradient(circle_at_top_right,_rgba(251,191,36,0.24),_transparent_30%),linear-gradient(180deg,#f8fafc_0%,#ecfeff_45%,#f8fafc_100%)]"></div>

        <div class="relative mx-auto max-w-7xl px-6 py-20 lg:px-8 lg:py-24">
            <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <div>
                    <span class="inline-flex items-center rounded-full border border-teal-200 bg-white/80 px-4 py-2 text-sm font-semibold text-teal-800 shadow-sm backdrop-blur">
                        Katalog Source Code Siap Deploy
                    </span>
                    <h1 class="mt-6 max-w-3xl text-5xl font-extrabold leading-tight text-slate-900 sm:text-6xl">
                        Pusat Source Code
                    </h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600">
                        Marketplace source code digital untuk bisnis, startup, dan UMKM Indonesia. Pilih produk, masukkan ke keranjang, bayar lewat Midtrans Snap, lalu unduh file source code dari akun Anda.
                    </p>

                    <div class="mt-8 flex flex-col gap-4 sm:flex-row">
                        <a href="#katalog" class="psc-btn-primary">
                            Lihat Katalog Produk
                        </a>
                        <a href="{{ route('cart.index') }}" class="psc-btn-outline">
                            Buka Keranjang
                        </a>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3 lg:grid-cols-1">
                    <div class="psc-soft-panel p-6 shadow-lg shadow-teal-100/60">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-teal-700">Produk Aktif</p>
                        <p class="mt-3 text-4xl font-extrabold text-slate-900">{{ $products->count() }}</p>
                        <p class="mt-2 text-sm text-slate-600">Solusi siap deploy untuk berbagai kebutuhan bisnis.</p>
                    </div>
                    <div class="psc-soft-panel p-6 shadow-lg shadow-amber-100/60">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-700">Kategori</p>
                        <p class="mt-3 text-4xl font-extrabold text-slate-900">{{ $categories->count() }}</p>
                        <p class="mt-2 text-sm text-slate-600">Pilihan dari retail, kesehatan, edukasi, hingga analitik.</p>
                    </div>
                    <div class="psc-soft-panel p-6 shadow-lg shadow-sky-100/60">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-sky-700">Tech Stack</p>
                        <p class="mt-3 text-4xl font-extrabold text-slate-900">{{ $techStacks->count() }}</p>
                        <p class="mt-2 text-sm text-slate-600">Laravel, React, Next.js, Vue, PostgreSQL, dan stack modern lain.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="katalog" class="mx-auto max-w-7xl px-6 py-16 lg:px-8">
        <div id="catalog-feedback" class="mb-6 hidden rounded-2xl border px-5 py-4 text-sm font-medium"></div>

        <div class="mb-8 flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">Katalog Produk</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <h2 class="text-3xl font-bold text-slate-900">Temukan produk yang cocok untuk bisnis Anda</h2>
                    <span id="product-count-badge" class="rounded-full border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700">
                        {{ $products->count() }} Produk
                    </span>
                </div>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                    Filter produk berdasarkan kata kunci, kategori, atau tech stack. Semua hasil diperbarui secara real-time tanpa reload halaman.
                </p>
            </div>

            <div class="w-full max-w-xl">
                <label for="product-search" class="mb-2 block text-sm font-semibold text-slate-700">Cari produk</label>
                <input
                    id="product-search"
                    type="search"
                    placeholder="Cari judul, deskripsi, kategori, atau tech stack..."
                    class="psc-input"
                >
            </div>
        </div>

        <div class="mb-8 flex flex-wrap gap-3">
            <button type="button" class="filter-chip rounded-full border border-teal-200 bg-teal-700 px-4 py-2 text-sm font-semibold text-white" data-stack="Semua">
                Semua
            </button>
            @foreach ($techStacks as $stack)
                <button type="button" class="filter-chip rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-teal-200 hover:text-teal-700" data-stack="{{ $stack }}">
                    {{ $stack }}
                </button>
            @endforeach
        </div>

        <div id="products-grid" class="grid gap-6 md:grid-cols-2 xl:grid-cols-3"></div>

        <div id="empty-state" class="hidden rounded-[2rem] border border-dashed border-slate-300 bg-white px-8 py-14 text-center shadow-sm">
            <p class="text-lg font-semibold text-slate-900">Produk tidak ditemukan</p>
            <p class="mt-3 text-sm leading-7 text-slate-600">
                Coba ubah kata kunci pencarian atau pilih tech stack lain untuk melihat hasil yang tersedia.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-6 pb-20 lg:px-8">
        <div class="rounded-[2rem] bg-slate-900 px-6 py-12 text-white sm:px-8 lg:px-12">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-200">Cara Pembelian</p>
                <h2 class="mt-3 text-3xl font-bold">Langkah pembelian yang sederhana dan cepat</h2>
                <p class="mt-4 text-sm leading-7 text-slate-300">
                    Seluruh proses dibuat ringkas agar Anda bisa fokus pada implementasi bisnis, bukan repot menyiapkan produk dari nol.
                </p>
            </div>

            <div class="mt-10 grid gap-5 md:grid-cols-3">
                <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-teal-400 text-lg font-bold text-teal-950">1</span>
                    <h3 class="mt-5 text-xl font-semibold">Pilih Produk & Masuk Keranjang</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        Telusuri katalog, baca detail produk, lalu tambahkan solusi yang paling sesuai ke keranjang belanja Anda.
                    </p>
                </div>
                <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-300 text-lg font-bold text-amber-950">2</span>
                    <h3 class="mt-5 text-xl font-semibold">Checkout via Midtrans Snap</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        Login, verifikasi email, lalu selesaikan pembayaran dari popup Snap tanpa meninggalkan halaman checkout.
                    </p>
                </div>
                <div class="rounded-[1.75rem] border border-white/10 bg-white/5 p-6">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-sky-300 text-lg font-bold text-sky-950">3</span>
                    <h3 class="mt-5 text-xl font-semibold">Lihat Pesanan & Unduh File</h3>
                    <p class="mt-3 text-sm leading-7 text-slate-300">
                        Setelah pembayaran lunas, buka halaman pesanan Anda untuk mengunduh source code yang sudah dibeli.
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const allProducts = @json($productsData);
        const productGrid = document.getElementById('products-grid');
        const emptyState = document.getElementById('empty-state');
        const countBadge = document.getElementById('product-count-badge');
        const searchInput = document.getElementById('product-search');
        const chips = document.querySelectorAll('.filter-chip');
        const feedback = document.getElementById('catalog-feedback');
        const detailBaseUrl = @json(url('/produk'));
        const cartBaseUrl = @json(url('/keranjang'));
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        let searchTerm = '';
        let selectedStack = 'Semua';

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function truncateText(value, length = 120) {
            if (value.length <= length) {
                return value;
            }

            return `${value.slice(0, length).trim()}...`;
        }

        function setFeedback(message, tone = 'success') {
            feedback.textContent = message;
            feedback.classList.remove('hidden', 'border-green-200', 'bg-green-50', 'text-green-800', 'border-red-200', 'bg-red-50', 'text-red-700');

            if (tone === 'success') {
                feedback.classList.add('border-green-200', 'bg-green-50', 'text-green-800');
            } else {
                feedback.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
            }
        }

        function renderProductCard(product) {
            const techTags = product.tech_stack
                .map((stack) => `
                    <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                        ${escapeHtml(stack)}
                    </span>
                `)
                .join('');

            const imageSection = product.list_thumbnail_url
                ? `
                    <div class="mb-6 overflow-hidden rounded-[1.75rem] border border-slate-200 bg-slate-100">
                        <img src="${escapeHtml(product.list_thumbnail_url)}" alt="${escapeHtml(product.title)}" class="h-52 w-full object-cover">
                    </div>
                `
                : `
                    <div class="mb-6 flex h-52 items-center justify-center rounded-[1.75rem] border border-dashed border-slate-300 bg-slate-50 text-xs font-semibold uppercase tracking-[0.18em] text-slate-400">
                        Preview Produk
                    </div>
                `;

            const cartButton = product.has_source_code_file
                ? `
                    <button type="button" class="add-to-cart-button psc-btn-primary w-full" data-product-id="${product.id}">
                        Tambah ke Keranjang
                    </button>
                `
                : `
                    <button type="button" class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-200 bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-400" disabled>
                        Belum Ready Checkout
                    </button>
                `;

            return `
                <article class="psc-card flex h-full flex-col overflow-hidden p-6 transition duration-300 hover:-translate-y-1 hover:shadow-xl">
                    ${imageSection}
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <span class="rounded-full border border-amber-200 bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                            ${escapeHtml(product.category)}
                        </span>
                        <span class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">
                            Update ${escapeHtml(product.updated_label)}
                        </span>
                    </div>

                    <div class="mt-5 flex-1">
                        <h3 class="text-xl font-bold leading-tight text-slate-900">
                            ${escapeHtml(product.title)}
                        </h3>
                        <p class="mt-4 text-sm leading-7 text-slate-600" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            ${escapeHtml(truncateText(product.description, 150))}
                        </p>

                        <div class="mt-5 flex flex-wrap gap-2">
                            ${techTags}
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-2xl font-extrabold text-teal-800">${escapeHtml(product.price)}</p>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        <a href="${detailBaseUrl}/${encodeURIComponent(product.slug)}" class="psc-btn-outline">
                            Lihat Detail
                        </a>
                        ${cartButton}
                    </div>

                    <a href="${product.whatsapp_url}" target="_blank" rel="noreferrer" class="mt-3 text-center text-sm font-semibold text-green-700 transition hover:text-green-800">
                        Konsultasi via WhatsApp
                    </a>
                </article>
            `;
        }

        async function addToCart(productId, button) {
            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Memproses...';

            try {
                const response = await fetch(`${cartBaseUrl}/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(payload.message || 'Gagal menambahkan produk ke keranjang.');
                }

                const cartCount = document.getElementById('header-cart-count');
                if (cartCount) {
                    cartCount.textContent = payload.cart_count ?? cartCount.textContent;
                }

                button.textContent = 'Sudah di Keranjang';
                setFeedback(`${payload.message} Buka keranjang untuk lanjut checkout.`, 'success');
            } catch (error) {
                button.disabled = false;
                button.textContent = originalText;
                setFeedback(error.message, 'error');
            }
        }

        function getFilteredProducts() {
            return allProducts.filter((product) => {
                const haystack = [
                    product.title,
                    product.description,
                    product.category,
                    ...product.tech_stack,
                ]
                    .join(' ')
                    .toLowerCase();

                const matchesSearch = searchTerm === '' || haystack.includes(searchTerm);
                const matchesStack = selectedStack === 'Semua' || product.tech_stack.includes(selectedStack);

                return matchesSearch && matchesStack;
            });
        }

        function renderProducts() {
            const filteredProducts = getFilteredProducts();

            countBadge.textContent = `${filteredProducts.length} Produk`;
            productGrid.innerHTML = filteredProducts.map(renderProductCard).join('');

            const hasProducts = filteredProducts.length > 0;
            productGrid.classList.toggle('hidden', !hasProducts);
            emptyState.classList.toggle('hidden', hasProducts);
        }

        searchInput.addEventListener('input', (event) => {
            searchTerm = event.target.value.trim().toLowerCase();
            renderProducts();
        });

        chips.forEach((chip) => {
            chip.addEventListener('click', () => {
                selectedStack = chip.dataset.stack;

                chips.forEach((button) => {
                    const isActive = button.dataset.stack === selectedStack;
                    button.classList.toggle('bg-teal-700', isActive);
                    button.classList.toggle('border-teal-200', isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('bg-white', !isActive);
                    button.classList.toggle('border-slate-200', !isActive);
                    button.classList.toggle('text-slate-700', !isActive);
                });

                renderProducts();
            });
        });

        productGrid.addEventListener('click', (event) => {
            const button = event.target.closest('.add-to-cart-button');

            if (!button) {
                return;
            }

            addToCart(button.dataset.productId, button);
        });

        renderProducts();
    </script>
@endpush
