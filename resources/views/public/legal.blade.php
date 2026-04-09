@extends('layouts.public')

@section('title', 'Legal | Pusat Source Code')
@section('meta_description', 'Dokumen syarat & ketentuan serta kebijakan refund Pusat Source Code.')

@section('content')
    <section class="border-b border-slate-200 bg-[radial-gradient(circle_at_top_left,_rgba(45,212,191,0.16),_transparent_30%),linear-gradient(180deg,#ffffff_0%,#f8fafc_100%)]">
        <div class="mx-auto max-w-5xl px-6 py-16 lg:px-8 lg:py-20">
            <span class="inline-flex rounded-full border border-teal-200 bg-teal-50 px-4 py-2 text-sm font-semibold text-teal-700">
                Terakhir diperbarui: 9 Maret 2026
            </span>
            <h1 class="mt-6 text-4xl font-extrabold text-slate-900 lg:text-5xl">Dokumen Legal Pusat Source Code</h1>
            <p class="mt-6 max-w-3xl text-base leading-8 text-slate-600">
                Halaman ini menjelaskan syarat penggunaan layanan, cakupan lisensi, dan kebijakan refund untuk setiap transaksi pembelian source code di Pusat Source Code.
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl space-y-8 px-6 py-12 lg:px-8 lg:py-16">
        <article id="syarat-ketentuan" class="psc-card p-8 lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-700">Syarat & Ketentuan</p>
            <p class="mt-5 text-sm leading-7 text-slate-600">
                Dengan melakukan pembayaran, pelanggan dianggap telah membaca, memahami, dan menyetujui seluruh ketentuan berikut ini.
            </p>

            <ol class="mt-6 list-decimal space-y-4 pl-5 text-sm leading-7 text-slate-700">
                <li>Seluruh produk yang dijual berupa file digital source code dan tidak berbentuk barang fisik.</li>
                <li>Pelanggan wajib memastikan spesifikasi produk, stack teknologi, dan cakupan delivery sebelum melakukan pembayaran.</li>
                <li>Hak akses yang diberikan berlaku sesuai lisensi pembelian dan tidak boleh disalahgunakan untuk distribusi ulang tanpa izin tertulis.</li>
                <li>Pusat Source Code tidak bertanggung jawab atas perubahan environment server milik pelanggan setelah file diserahkan.</li>
                <li>Dukungan teknis yang diberikan terbatas pada bantuan setup dasar sesuai dokumentasi yang tersedia.</li>
                <li>Permintaan kustomisasi, integrasi tambahan, atau perubahan fitur utama berada di luar harga produk kecuali disepakati terpisah.</li>
                <li>Pelanggan bertanggung jawab atas penggunaan source code sesuai hukum yang berlaku di Indonesia.</li>
                <li>Pusat Source Code berhak memperbarui dokumen legal sewaktu-waktu dengan mencantumkan tanggal pembaruan terbaru.</li>
            </ol>

            <div class="mt-8 rounded-[1.75rem] border border-amber-200 bg-amber-50 p-5">
                <p class="text-sm font-semibold text-amber-900">Lisensi komersial</p>
                <p class="mt-2 text-sm leading-7 text-amber-950/80">
                    Pembelian produk memberikan hak penggunaan untuk kebutuhan operasional bisnis pelanggan. Distribusi ulang, penjualan ulang, atau publikasi source code mentah tanpa izin tetap dilarang.
                </p>
            </div>
        </article>

        <article id="kebijakan-refund" class="psc-card p-8 lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-700">Kebijakan Refund</p>
            <p class="mt-5 text-sm leading-7 text-slate-600">
                Karena produk yang dijual berupa file digital yang dapat langsung diunduh dan disalin, seluruh transaksi pada dasarnya bersifat <strong>No Refund</strong>.
            </p>
            <p class="mt-4 text-sm leading-7 text-slate-600">
                Pengecualian hanya berlaku jika file yang dikirim rusak, tidak lengkap, atau tidak dapat dibuka dan dilaporkan maksimal dalam 3 x 24 jam sejak pengiriman.
            </p>

            <ul class="mt-6 list-disc space-y-4 pl-5 text-sm leading-7 text-slate-700">
                <li>File arsip korup atau gagal diekstrak setelah diverifikasi.</li>
                <li>Database atau dokumentasi utama tidak terkirim sesuai paket delivery.</li>
                <li>Produk yang dikirim berbeda total dari judul dan deskripsi pembelian.</li>
                <li>Link pengiriman rusak dan tidak dapat diakses ulang dalam batas waktu laporan.</li>
            </ul>

            <div class="mt-8 rounded-[1.75rem] border border-teal-200 bg-teal-50 p-5">
                <p class="text-sm font-semibold text-teal-900">Komitmen perbaikan</p>
                <p class="mt-2 text-sm leading-7 text-teal-950/80">
                    Jika masalah termasuk pengecualian di atas, tim kami akan mengutamakan penggantian file, perbaikan arsip, atau pengiriman ulang sebelum mempertimbangkan opsi refund.
                </p>
            </div>
        </article>

        <article class="psc-card p-8 lg:p-10">
            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-teal-700">Kontak</p>
            <div class="mt-6 grid gap-4 md:grid-cols-3">
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Email</p>
                    <p class="mt-3 text-sm font-semibold text-slate-900">hello@pusatsourcecode.site</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">WhatsApp</p>
                    <p class="mt-3 text-sm font-semibold text-slate-900">+62 822-5780-2227</p>
                </div>
                <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Alamat</p>
                    <p class="mt-3 text-sm font-semibold text-slate-900">Jakarta Selatan, Indonesia</p>
                </div>
            </div>
        </article>
    </section>
@endsection
