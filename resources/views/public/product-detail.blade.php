@extends('layouts.public')

@section('title', $product->title.' | Pusat Source Code')
@section('meta_description', $product->description)

@section('content')
    @php
        $averageRating = round((float) ($product->reviews_avg_rating ?? 0), 1);
        $roundedAverageRating = (int) round($averageRating);
        $selectedRating = (int) old('rating', $userReview?->rating ?? 0);
        $reviewComment = old('comment', $userReview?->comment ?? '');
    @endphp

    <section class="relative overflow-hidden border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(45,212,191,0.18),_transparent_36%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
        <div class="mx-auto max-w-5xl px-6 py-16 lg:px-8 lg:py-20">
            <a href="{{ route('home') }}#katalog" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:border-teal-200 hover:text-teal-700">
                &larr; Kembali ke Katalog
            </a>

            <article class="psc-card mt-8 overflow-hidden p-8 lg:p-10">
                @if ($product->detail_thumbnail_url)
                    <div class="mb-8 overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-100">
                        <img src="{{ $product->detail_thumbnail_url }}" alt="{{ $product->title }}" class="h-[280px] w-full object-cover md:h-[420px]">
                    </div>
                @endif

                <div class="flex flex-wrap items-center gap-3">
                    <span class="rounded-full border border-amber-200 bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                        {{ $product->category }}
                    </span>
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                        Update {{ $product->updated_label }}
                    </span>
                </div>

                <h1 class="mt-6 max-w-4xl text-4xl font-extrabold leading-tight text-slate-900 lg:text-5xl">
                    {{ $product->title }}
                </h1>

                <p class="mt-6 max-w-3xl text-base leading-8 text-slate-600">
                    {{ $product->description }}
                </p>

                <div class="mt-8 grid gap-4 lg:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.9fr)]">
                    <div class="rounded-[2rem] border border-green-200 bg-green-50 p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-green-800">Harga</p>
                        <p class="mt-3 text-4xl font-extrabold text-teal-800">{{ $product->price }}</p>
                        <p class="mt-2 text-sm text-green-900/80">
                            Termasuk checkout Midtrans Snap dan akses unduh source code dari halaman pesanan setelah pembayaran lunas.
                        </p>
                    </div>

                    <div class="rounded-[2rem] border border-amber-200 bg-amber-50 p-6">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-amber-800">Ulasan Pembeli</p>
                        @if ($product->reviews_count > 0)
                            <div class="mt-3 flex items-end gap-4">
                                <p class="text-4xl font-extrabold text-slate-900">{{ number_format($averageRating, 1) }}</p>
                                <div class="pb-1">
                                    <div class="flex text-lg leading-none">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <span class="{{ $star <= $roundedAverageRating ? 'text-amber-500' : 'text-amber-200' }}">&#9733;</span>
                                        @endfor
                                    </div>
                                    <p class="mt-2 text-sm text-amber-900/80">{{ $product->reviews_count }} ulasan terverifikasi</p>
                                </div>
                            </div>
                        @else
                            <p class="mt-3 text-sm leading-7 text-amber-900/80">
                                Belum ada ulasan untuk produk ini. Pembeli yang sudah melunasi pesanan bisa jadi yang pertama memberi rating dan komentar.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="mt-10">
                    <h2 class="text-xl font-bold text-slate-900">Tech Stack</h2>
                    <div class="mt-4 flex flex-wrap gap-3">
                        @foreach ($product->tech_stack as $stack)
                            <span class="rounded-full border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700">
                                {{ $stack }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="mt-10">
                    <h2 class="text-xl font-bold text-slate-900">Fitur Utama</h2>
                    <div class="mt-5 grid gap-4 md:grid-cols-2">
                        @foreach ($product->features as $feature)
                            <div class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm font-medium leading-7 text-slate-700">{{ $feature }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($product->screenshots->isNotEmpty())
                    <div class="mt-10">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <h2 class="text-xl font-bold text-slate-900">List Screenshot</h2>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ $product->screenshots->count() }} Screenshot
                            </span>
                        </div>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            @foreach ($product->screenshots as $screenshot)
                                <a
                                    href="{{ $screenshot->image_url }}"
                                    target="_blank"
                                    rel="noreferrer"
                                    class="overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                                >
                                    @if ($screenshot->thumbnail_url)
                                        <img src="{{ $screenshot->thumbnail_url }}" alt="Screenshot {{ $loop->iteration }} {{ $product->title }}" class="h-56 w-full object-cover">
                                    @endif
                                    <div class="flex items-center justify-between gap-3 px-5 py-4">
                                        <span class="text-sm font-semibold text-slate-900">
                                            Screenshot {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-xs font-semibold uppercase tracking-[0.16em] text-teal-700">
                                            Lihat Full
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div id="ulasan-produk" class="mt-10 grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(320px,0.95fr)]">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Komentar Pembeli</h2>
                                <p class="mt-2 text-sm text-slate-500">Semua review di bawah ini berasal dari akun yang sudah menyelesaikan pembayaran.</p>
                            </div>
                            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600">
                                {{ $product->reviews_count }} Ulasan
                            </span>
                        </div>

                        @if ($product->reviews->isEmpty())
                            <div class="mt-6 rounded-[1.75rem] border border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center">
                                <p class="text-base font-semibold text-slate-900">Belum ada komentar untuk produk ini.</p>
                                <p class="mt-2 text-sm leading-7 text-slate-500">Setelah ada pembeli yang memberi rating, komentar mereka akan muncul di sini.</p>
                            </div>
                        @else
                            <div class="mt-6 space-y-4">
                                @foreach ($product->reviews as $review)
                                    <article class="rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                                        <div class="flex flex-wrap items-start justify-between gap-3">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-base font-bold text-slate-900">{{ $review->user->name }}</p>
                                                    <span class="rounded-full border border-green-200 bg-green-100 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em] text-green-800">
                                                        Pembeli
                                                    </span>
                                                </div>
                                                <p class="mt-2 text-xs font-medium uppercase tracking-[0.18em] text-slate-400">
                                                    {{ $review->created_at->format('d M Y') }}
                                                </p>
                                            </div>

                                            <div class="flex text-lg leading-none">
                                                @for ($star = 1; $star <= 5; $star++)
                                                    <span class="{{ $star <= $review->rating ? 'text-amber-500' : 'text-slate-300' }}">&#9733;</span>
                                                @endfor
                                            </div>
                                        </div>

                                        <p class="mt-4 text-sm leading-7 text-slate-700">{{ $review->comment }}</p>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-xl font-bold text-slate-900">Tulis Ulasan</h2>
                        <p class="mt-2 text-sm leading-7 text-slate-500">
                            Rating dan komentar Anda akan tampil di halaman produk ini setelah disimpan.
                        </p>

                        @auth
                            @if ($canSubmitReview)
                                @if ($errors->any())
                                    <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                                        <p class="font-semibold">Ulasan belum bisa disimpan.</p>
                                        <ul class="mt-2 space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('product.reviews.store', ['product' => $product->slug]) }}" class="mt-6 space-y-5">
                                    @csrf

                                    <div>
                                        <label class="text-sm font-semibold text-slate-900">Rating Bintang</label>
                                        <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                            @for ($rating = 5; $rating >= 1; $rating--)
                                                <input
                                                    type="radio"
                                                    name="rating"
                                                    id="rating-{{ $rating }}"
                                                    value="{{ $rating }}"
                                                    class="peer sr-only"
                                                    @checked($selectedRating === $rating)
                                                >
                                                <label for="rating-{{ $rating }}" class="flex cursor-pointer items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-amber-300 hover:bg-amber-50 peer-checked:border-amber-300 peer-checked:bg-amber-50 peer-checked:text-amber-800">
                                                    <span>{{ $rating }} Bintang</span>
                                                    <span>
                                                        @for ($star = 1; $star <= 5; $star++)
                                                            <span class="{{ $star <= $rating ? 'text-amber-500' : 'text-slate-300' }}">&#9733;</span>
                                                        @endfor
                                                    </span>
                                                </label>
                                            @endfor
                                        </div>
                                    </div>

                                    <div>
                                        <label for="comment" class="text-sm font-semibold text-slate-900">Komentar</label>
                                        <textarea
                                            id="comment"
                                            name="comment"
                                            rows="5"
                                            maxlength="2000"
                                            class="mt-3 w-full rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition focus:border-teal-400 focus:bg-white focus:ring-0"
                                            placeholder="Ceritakan pengalaman Anda memakai produk ini."
                                        >{{ $reviewComment }}</textarea>
                                    </div>

                                    <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                                        @if ($userReview)
                                            Anda sudah pernah memberi ulasan. Kirim ulang form ini untuk memperbarui rating atau komentar.
                                        @else
                                            Ulasan hanya bisa dikirim satu kali per akun, tetapi tetap bisa diperbarui kapan saja dari form ini.
                                        @endif
                                    </div>

                                    <button type="submit" class="psc-btn-primary w-full justify-center">
                                        {{ $userReview ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                                    </button>
                                </form>
                            @else
                                <div class="mt-6 rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                                    <p class="text-sm font-semibold text-slate-900">Ulasan dikunci sampai pesanan lunas.</p>
                                    <p class="mt-2 text-sm leading-7 text-slate-600">
                                        Sistem hanya membuka form review untuk akun yang sudah membeli dan menyelesaikan pembayaran produk ini.
                                    </p>
                                </div>
                            @endif
                        @else
                            <div class="mt-6 rounded-[1.75rem] border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-900">Masuk dulu untuk memberi ulasan.</p>
                                <p class="mt-2 text-sm leading-7 text-slate-600">
                                    Setelah login dengan akun pembeli dan pesanan berstatus lunas, Anda bisa memberi rating dan komentar di sini.
                                </p>
                                <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('public.login') }}" class="psc-btn-primary justify-center">
                                        Masuk
                                    </a>
                                    <a href="{{ route('public.register') }}" class="psc-btn-outline justify-center">
                                        Daftar
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-2">
                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Kategori</p>
                        <p class="mt-3 text-lg font-bold text-slate-900">{{ $product->category }}</p>
                    </div>
                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-slate-500">Paket Delivery</p>
                        <p class="mt-3 text-lg font-bold text-slate-900">{{ $product->delivery }}</p>
                    </div>
                </div>

                <div id="product-feedback" class="mt-8 hidden rounded-2xl border px-5 py-4 text-sm font-medium"></div>

                <div class="mt-10 flex flex-col gap-4 sm:flex-row sm:flex-wrap">
                    @if ($product->has_source_code_file)
                        <button
                            type="button"
                            id="product-add-to-cart"
                            data-product-id="{{ $product->id }}"
                            class="psc-btn-primary"
                        >
                            Tambah ke Keranjang
                        </button>
                    @else
                        <span class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-400">
                            Belum Ready Checkout
                        </span>
                    @endif

                    <a href="{{ route('cart.index') }}" class="psc-btn-outline">
                        Lihat Keranjang
                    </a>
                    <a href="{{ $product->whatsapp_url }}" target="_blank" rel="noreferrer" class="psc-btn-success">
                        Konsultasi via WhatsApp
                    </a>
                    <a href="{{ route('home') }}#katalog" class="psc-btn-outline">
                        Lihat Produk Lain
                    </a>
                </div>
            </article>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        const addToCartButton = document.getElementById('product-add-to-cart');
        const productFeedback = document.getElementById('product-feedback');

        function setProductFeedback(message, tone = 'success') {
            if (!productFeedback) {
                return;
            }

            productFeedback.textContent = message;
            productFeedback.classList.remove('hidden', 'border-green-200', 'bg-green-50', 'text-green-800', 'border-red-200', 'bg-red-50', 'text-red-700');

            if (tone === 'success') {
                productFeedback.classList.add('border-green-200', 'bg-green-50', 'text-green-800');
            } else {
                productFeedback.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
            }
        }

        if (addToCartButton) {
            addToCartButton.addEventListener('click', async () => {
                const originalText = addToCartButton.textContent;
                addToCartButton.disabled = true;
                addToCartButton.textContent = 'Memproses...';

                try {
                    const response = await fetch(`{{ url('/keranjang') }}/${addToCartButton.dataset.productId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
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

                    addToCartButton.textContent = 'Sudah di Keranjang';
                    setProductFeedback(`${payload.message} Anda bisa lanjut checkout dari keranjang.`, 'success');
                } catch (error) {
                    addToCartButton.disabled = false;
                    addToCartButton.textContent = originalText;
                    setProductFeedback(error.message, 'error');
                }
            });
        }
    </script>
@endpush
