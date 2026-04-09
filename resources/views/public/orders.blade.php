@extends('layouts.public')

@section('title', 'Pesanan Saya | Pusat Source Code')
@section('meta_description', 'Lihat status pembayaran pesanan Anda dan unduh source code setelah transaksi lunas.')

@section('content')
    <section class="relative overflow-hidden border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(45,212,191,0.18),_transparent_36%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
        <div class="mx-auto max-w-7xl px-6 py-16 lg:px-8 lg:py-20">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-teal-700">Pesanan Saya</p>
                    <h1 class="mt-3 text-4xl font-extrabold text-slate-900">Kelola pembayaran dan unduh source code</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-7 text-slate-600">
                        Semua transaksi Anda tersimpan di sini. Untuk pesanan yang sudah lunas, tombol unduh akan otomatis aktif per produk.
                    </p>
                </div>
            </div>

            <div id="order-page-feedback" class="mt-8 hidden rounded-2xl border px-5 py-4 text-sm font-medium"></div>

            @if ($orders->count() === 0)
                <div class="psc-card mt-10 px-8 py-14 text-center">
                    <p class="text-2xl font-bold text-slate-900">Belum ada pesanan</p>
                    <p class="mt-3 text-sm leading-7 text-slate-600">
                        Pesanan Anda akan muncul di halaman ini setelah checkout berhasil dibuat dari keranjang.
                    </p>
                    <a href="{{ route('home') }}#katalog" class="psc-btn-primary mt-8">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="mt-10 space-y-6">
                    @foreach ($orders as $order)
                        <article class="psc-card overflow-hidden">
                            <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <div class="flex flex-wrap items-center gap-3 py-3">
                                            <h2 class="text-xl font-bold text-slate-900">{{ $order->order_number }}</h2>
                                            <span class="rounded-full border px-3 py-1 text-xs font-semibold {{ $order->status_badge_classes }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </div>
                                        <p class="mt-2 text-sm text-slate-500">
                                            Dibuat {{ $order->created_at->translatedFormat('d M Y, H:i') }}
                                            @if ($order->midtrans_payment_type)
                                                • Pembayaran {{ strtoupper($order->midtrans_payment_type) }}
                                            @endif
                                            @if ($order->paid_at)
                                                • Lunas {{ $order->paid_at->translatedFormat('d M Y, H:i') }}
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center">
                                        <div class="text-left sm:text-right">
                                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Total</p>
                                            <p class="mt-1 text-2xl font-extrabold text-teal-800">{{ $order->formatted_total }}</p>
                                        </div>

                                        @if ($midtransConfigured && $order->can_retry_payment && ! $order->is_paid)
                                            <button
                                                type="button"
                                                class="retry-payment-button psc-btn-primary"
                                                data-pay-url="{{ route('orders.pay', $order) }}"
                                            >
                                                Bayar Sekarang
                                            </button>
                                        @endif

                                        @if (! $order->is_paid)
                                            <button
                                                type="button"
                                                class="refresh-payment-button psc-btn-outline"
                                                data-refresh-url="{{ route('orders.refresh', $order) }}"
                                            >
                                                Cek Status Pembayaran
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 p-6">
                                @foreach ($order->items as $item)
                                    <div class="rounded-[1.75rem] border border-slate-200 bg-white p-5">
                                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                            <div>
                                                <p class="text-lg font-bold text-slate-900">{{ $item->product_title }}</p>
                                                <p class="mt-2 text-sm text-slate-500">
                                                    {{ $item->price_label }}
                                                    @if ($item->product_slug)
                                                        • <a href="{{ route('product.show', $item->product_slug) }}" class="font-semibold text-teal-700 transition hover:text-teal-800">Lihat detail produk</a>
                                                    @endif
                                                </p>
                                            </div>

                                            <div class="flex flex-col items-start gap-3 sm:flex-row sm:items-center">
                                                <p class="text-base font-semibold text-slate-900">{{ $item->formatted_total }}</p>

                                                @if ($order->is_paid && $item->download_available)
                                                    <a href="{{ route('orders.download', [$order, $item]) }}" class="psc-btn-primary">
                                                        Unduh Source Code
                                                    </a>
                                                @elseif ($order->is_paid)
                                                    <span class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-slate-100 px-5 py-3 text-sm font-semibold text-slate-500">
                                                        File Belum Tersedia
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center justify-center rounded-2xl border border-amber-200 bg-amber-50 px-5 py-3 text-sm font-semibold text-amber-800">
                                                        Menunggu Pembayaran
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        @if ($order->is_paid && $item->resolveDownloadName())
                                            <p class="mt-3 text-xs text-slate-500">
                                                File unduhan: {{ $item->resolveDownloadName() }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    @if ($midtransConfigured && $orders->getCollection()->contains(fn ($order) => $order->can_retry_payment && ! $order->is_paid))
        <script src="{{ $midtransSnapJsUrl }}" data-client-key="{{ $midtransClientKey }}"></script>
    @endif

    <script>
        const orderFeedback = document.getElementById('order-page-feedback');

        function setOrderFeedback(message, tone = 'success') {
            orderFeedback.textContent = message;
            orderFeedback.classList.remove('hidden', 'border-green-200', 'bg-green-50', 'text-green-800', 'border-red-200', 'bg-red-50', 'text-red-700', 'border-amber-200', 'bg-amber-50', 'text-amber-800');

            if (tone === 'success') {
                orderFeedback.classList.add('border-green-200', 'bg-green-50', 'text-green-800');
            } else if (tone === 'warning') {
                orderFeedback.classList.add('border-amber-200', 'bg-amber-50', 'text-amber-800');
            } else {
                orderFeedback.classList.add('border-red-200', 'bg-red-50', 'text-red-700');
            }
        }

        async function requestJson(url) {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            const payload = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(payload.message || 'Permintaan tidak dapat diproses.');
            }

            return payload;
        }

        document.querySelectorAll('.refresh-payment-button').forEach((button) => {
            button.addEventListener('click', async () => {
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Memeriksa...';

                try {
                    const payload = await requestJson(button.dataset.refreshUrl);
                    setOrderFeedback(payload.message, payload.is_paid ? 'success' : 'warning');
                    window.location.reload();
                } catch (error) {
                    setOrderFeedback(error.message, 'error');
                } finally {
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
        });

        document.querySelectorAll('.retry-payment-button').forEach((button) => {
            button.addEventListener('click', async () => {
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Menyiapkan...';

                try {
                    const payload = await requestJson(button.dataset.payUrl);

                    window.snap.pay(payload.snap_token, {
                        onSuccess: async function () {
                            await requestJson(payload.refresh_url);
                            window.location.reload();
                        },
                        onPending: function () {
                            window.location.reload();
                        },
                        onError: async function () {
                            await requestJson(payload.refresh_url);
                            window.location.reload();
                        },
                        onClose: function () {
                            button.disabled = false;
                            button.textContent = originalText;
                            setOrderFeedback('Popup pembayaran ditutup. Anda bisa lanjutkan pembayaran kapan saja dari halaman ini.', 'warning');
                        },
                    });
                } catch (error) {
                    button.disabled = false;
                    button.textContent = originalText;
                    setOrderFeedback(error.message, 'error');
                }
            });
        });
    </script>
@endpush
