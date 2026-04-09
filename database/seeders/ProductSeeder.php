<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductScreenshot;
use App\Services\ProductImageService;
use App\Services\ProductSourceCodeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $productImageService = app(ProductImageService::class);
        $productSourceCodeService = app(ProductSourceCodeService::class);

        $products = [
            [
                'id' => 101,
                'title' => 'POS Toko + Inventory Multi Cabang',
                'description' => 'Sistem kasir lengkap dengan manajemen stok realtime, transfer antar gudang, dan laporan laba rugi.',
                'price' => 'Rp 1.290.000',
                'price_numeric' => 1290000,
                'category' => 'Retail',
                'tech_stack' => ['Laravel', 'MySQL', 'Bootstrap'],
                'features' => ['Manajemen produk, varian, dan barcode', 'Stok masuk/keluar + notifikasi minimum stok', 'Laporan harian, mingguan, dan bulanan', 'Role admin, kasir, owner'],
                'delivery' => 'Full source code, database, dokumentasi instalasi',
                'updated_label' => 'Februari 2026',
            ],
            [
                'id' => 102,
                'title' => 'E-Commerce Fullstack + Midtrans',
                'description' => 'Aplikasi toko online modern dengan checkout, pembayaran gateway, kupon promo, dan dashboard analitik.',
                'price' => 'Rp 1.850.000',
                'price_numeric' => 1850000,
                'category' => 'E-Commerce',
                'tech_stack' => ['React', 'Node.js', 'MongoDB'],
                'features' => ['Katalog produk, wishlist, keranjang', 'Integrasi Midtrans dan ongkir', 'Tracking order dan status pembayaran', 'Dashboard admin dengan grafik penjualan'],
                'delivery' => 'Source code frontend+backend, API docs, seed data',
                'updated_label' => 'Januari 2026',
            ],
            [
                'id' => 103,
                'title' => 'Sistem Klinik & Rekam Medis',
                'description' => 'Platform manajemen klinik untuk pendaftaran pasien, antrian, rekam medis, dan billing.',
                'price' => 'Rp 2.250.000',
                'price_numeric' => 2250000,
                'category' => 'Kesehatan',
                'tech_stack' => ['PHP', 'CodeIgniter', 'MySQL'],
                'features' => ['Master data dokter, pasien, dan tindakan', 'Antrian pasien + jadwal dokter', 'Riwayat diagnosa dan resep', 'Invoice dan laporan pendapatan'],
                'delivery' => 'Full source code, SQL file, panduan deployment',
                'updated_label' => 'Maret 2026',
            ],
            [
                'id' => 104,
                'title' => 'Booking Service + Kalender Otomatis',
                'description' => 'Sistem booking untuk salon, barbershop, atau konsultasi dengan reminder otomatis via WhatsApp.',
                'price' => 'Rp 1.150.000',
                'price_numeric' => 1150000,
                'category' => 'Layanan',
                'tech_stack' => ['Vue', 'Firebase', 'Tailwind'],
                'features' => ['Pilih jadwal dan durasi layanan', 'Panel staf dan slot ketersediaan', 'Notifikasi booking dan pembatalan', 'Riwayat transaksi pelanggan'],
                'delivery' => 'Source code + konfigurasi Firebase + dokumentasi',
                'updated_label' => 'Desember 2025',
            ],
            [
                'id' => 105,
                'title' => 'Dashboard BI & KPI Perusahaan',
                'description' => 'Dashboard interaktif untuk monitoring KPI perusahaan dengan grafik performa dan export PDF/Excel.',
                'price' => 'Rp 1.690.000',
                'price_numeric' => 1690000,
                'category' => 'Analitik',
                'tech_stack' => ['Python', 'FastAPI', 'Chart.js'],
                'features' => ['Widget KPI realtime', 'Filter lintas divisi dan rentang tanggal', 'Export laporan PDF dan Excel', 'Autentikasi user multi-level'],
                'delivery' => 'Source code backend+frontend, dokumentasi API, sample dataset',
                'updated_label' => 'Februari 2026',
            ],
            [
                'id' => 106,
                'title' => 'LMS Kursus Online + Sertifikat',
                'description' => 'Platform pembelajaran online lengkap dengan video course, kuis, progress belajar, dan sertifikat.',
                'price' => 'Rp 2.490.000',
                'price_numeric' => 2490000,
                'category' => 'Edukasi',
                'tech_stack' => ['Next.js', 'PostgreSQL', 'Prisma'],
                'features' => ['Manajemen kelas, modul, dan materi', 'Quiz engine dan penilaian otomatis', 'Sertifikat digital dengan verifikasi', 'Dashboard mentor dan siswa'],
                'delivery' => 'Full source code, ERD, setup guide production',
                'updated_label' => 'Maret 2026',
            ],
        ];

        foreach ($products as $index => $product) {
            $productModel = Product::query()->updateOrCreate(
                ['id' => $product['id']],
                [
                    ...$product,
                    'slug' => Str::slug($product['title']),
                    'is_active' => true,
                    'sort_order' => $index,
                ],
            );

            if (! $productModel->image_path || ! $productModel->list_thumbnail_path || ! $productModel->detail_thumbnail_path) {
                $productModel->update(
                    $productImageService->createPlaceholderSet(
                        $productModel->slug,
                        $productModel->title,
                        $productModel->category,
                        $productModel->tech_stack ?? [],
                    ),
                );
            }

            if (! ProductScreenshot::query()->where('product_id', $productModel->id)->exists()) {
                foreach ([1, 2, 3] as $sequence) {
                    $productModel->screenshots()->create([
                        ...$productImageService->createScreenshotPlaceholderSet(
                            $productModel->slug,
                            $productModel->title,
                            $productModel->category,
                            $sequence,
                        ),
                        'sort_order' => $sequence - 1,
                    ]);
                }
            }

            if (! $productModel->source_code_path) {
                $productModel->update(
                    $productSourceCodeService->createPlaceholderFile(
                        $productModel->slug,
                        $productModel->title,
                    ),
                );
            }
        }
    }
}
