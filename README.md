<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">
</p>

<h1 align="center">💄 Kosmetik E-Commerce</h1>

<p align="center">
  Platform e-commerce lengkap untuk produk kosmetik dan kecantikan, dibangun dengan Laravel 12.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white" alt="PHP Version">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" alt="Laravel Version">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-06B6D4?logo=tailwindcss&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/Midtrans-Payment-00AEEF" alt="Midtrans">
  <img src="https://img.shields.io/badge/RajaOngkir-Shipping-FF6B35" alt="RajaOngkir">
  <img src="https://img.shields.io/badge/License-MIT-green" alt="License">
</p>

---

## 📖 Daftar Isi

- [Tentang Proyek](#-tentang-proyek)
- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Arsitektur Sistem](#-arsitektur-sistem)
- [Persyaratan Sistem](#-persyaratan-sistem)
- [Instalasi & Setup](#-instalasi--setup)
- [Konfigurasi Environment](#-konfigurasi-environment)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Akun Default](#-akun-default)
- [Panduan Penggunaan](#-panduan-penggunaan)
- [Struktur Proyek](#-struktur-proyek)
- [Database Schema](#-database-schema)
- [API & Webhook](#-api--webhook)
- [Queue Jobs](#-queue-jobs)
- [Caching](#-caching)
- [Keamanan](#-keamanan)
- [Testing](#-testing)
- [Deployment Produksi](#-deployment-produksi)
- [Dokumentasi Tambahan](#-dokumentasi-tambahan)
- [Lisensi](#-lisensi)

---

## 🎯 Tentang Proyek

**Kosmetik E-Commerce** adalah platform belanja online yang dirancang khusus untuk produk kosmetik dan kecantikan. Aplikasi ini menyediakan pengalaman belanja yang lengkap mulai dari browsing katalog produk, keranjang belanja, checkout, hingga pembayaran online dan pelacakan pesanan.

Sistem ini memiliki dua peran pengguna utama:

| Peran | Deskripsi |
|-------|-----------|
| **Customer** | Pengguna yang dapat menjelajahi produk, menambah ke keranjang, melakukan checkout, dan melacak pesanan |
| **Admin** | Pengelola yang memiliki akses penuh ke panel admin untuk mengelola produk, pesanan, pengguna, dan laporan |

---

## ✨ Fitur Utama

### 🛒 Fitur Customer

| Fitur | Deskripsi |
|-------|-----------|
| **Registrasi & Login** | Registrasi dengan verifikasi email, login dengan proteksi brute-force (lock setelah 5x gagal) |
| **Profil Pengguna** | Kelola profil, alamat pengiriman (multi-alamat), nomor telepon |
| **Katalog Produk** | Browsing produk dengan filter (kategori, brand, harga), sorting, dan pencarian keyword |
| **Detail Produk** | Galeri gambar, varian produk, deskripsi lengkap, rating & review |
| **Keranjang Belanja** | Tambah/hapus item, ubah jumlah, validasi stok real-time |
| **Wishlist** | Simpan produk favorit, pindahkan ke keranjang |
| **Checkout** | Pilih alamat pengiriman, pilih kurir, terapkan voucher diskon |
| **Ongkos Kirim** | Kalkulasi ongkir otomatis via RajaOngkir (JNE, POS, TIKI) |
| **Pembayaran Online** | Integrasi Midtrans (transfer bank, e-wallet, kartu kredit) |
| **Riwayat Pesanan** | Lihat semua pesanan, detail pesanan, tracking pengiriman |
| **Konfirmasi Penerimaan** | Konfirmasi barang sudah diterima |
| **Pengajuan Refund** | Ajukan pengembalian dana untuk pesanan bermasalah |
| **Review & Rating** | Berikan ulasan dan rating (1–5 bintang) untuk produk yang sudah dibeli |

### 🔧 Fitur Admin

| Fitur | Deskripsi |
|-------|-----------|
| **Dashboard** | Ringkasan statistik: total pesanan, pendapatan, produk, pengguna |
| **Manajemen Produk** | CRUD produk dengan multi-gambar, varian, SKU, berat, status aktif/nonaktif |
| **Manajemen Brand** | CRUD brand dengan logo, status aktif/nonaktif |
| **Manajemen Kategori** | CRUD kategori produk |
| **Manajemen Voucher** | Buat & kelola voucher diskon (persentase/nominal, min. pembelian, kuota) |
| **Manajemen Pesanan** | Lihat, ubah status pesanan, input resi pengiriman |
| **Manajemen Pengguna** | Lihat daftar pengguna, aktifkan/nonaktifkan akun |
| **Moderasi Review** | Hapus review yang tidak sesuai |
| **Activity Log** | Riwayat aktivitas admin untuk audit trail |
| **Laporan** | Laporan penjualan dan statistik bisnis |

### 📧 Notifikasi Email

| Email | Trigger |
|-------|---------|
| Verifikasi Email | Setelah registrasi |
| Konfirmasi Pesanan | Setelah checkout berhasil |
| Konfirmasi Pembayaran | Setelah pembayaran dikonfirmasi via Midtrans |
| Update Status Pesanan | Saat admin mengubah status/menginput resi |

---

## 🛠 Tech Stack

### Backend
- **PHP 8.2+** — Bahasa pemrograman
- **Laravel 12** — Framework utama
- **Laravel Breeze** — Autentikasi (login, register, reset password, verifikasi email)
- **Eloquent ORM** — Object-Relational Mapping untuk database
- **Laravel Queue** — Background job processing (database driver)

### Frontend
- **Blade Templates** — Template engine Laravel
- **TailwindCSS 3** — Utility-first CSS framework
- **Alpine.js 3** — Lightweight JavaScript framework
- **Vite** — Build tool & dev server

### Integrasi Pihak Ketiga
- **Midtrans** — Payment gateway (transfer bank, e-wallet, kartu kredit, dll.)
- **RajaOngkir** — Kalkulasi ongkos kirim (JNE, POS Indonesia, TIKI)

### Database
- **MySQL** (rekomendasi produksi) / **SQLite** (development)

### Dev Tools
- **PHPUnit** — Unit & feature testing
- **Laravel Pint** — Code style fixer
- **Laravel Pail** — Real-time log viewer
- **Laravel Sail** — Docker development environment (opsional)

---

## 🏗 Arsitektur Sistem

Proyek ini menggunakan arsitektur berlapis (**Layered Architecture**) untuk memisahkan tanggung jawab:

```
┌──────────────────────────────────────────────────────────┐
│                      Routes (web.php, api.php)           │
├──────────────────────────────────────────────────────────┤
│                      Middleware                          │
│  AdminMiddleware │ CheckAccountActive │ ForceHttps │ ... │
├──────────────────────────────────────────────────────────┤
│                      Controllers                         │
│  Customer/  │  Admin/  │  Auth/  │  Webhook              │
├──────────────────────────────────────────────────────────┤
│                      Form Requests                       │
│  Validasi input server-side                              │
├──────────────────────────────────────────────────────────┤
│                      Services                            │
│  CartService │ OrderService │ PaymentService │ ...       │
├──────────────────────────────────────────────────────────┤
│                      Repositories                        │
│  ProductRepository (dengan caching)                      │
├──────────────────────────────────────────────────────────┤
│                      Models + Observers                  │
│  User │ Product │ Order │ Payment │ Review │ ...         │
├──────────────────────────────────────────────────────────┤
│                      Jobs (Queue)                        │
│  SendEmailVerification │ SendOrderConfirmation │ ...     │
├──────────────────────────────────────────────────────────┤
│                      Database (MySQL/SQLite)             │
└──────────────────────────────────────────────────────────┘
```

### Pola Desain yang Digunakan

- **Repository Pattern** — Abstraksi akses data dengan caching (`ProductRepository`)
- **Service Pattern** — Logika bisnis terisolasi (`CartService`, `OrderService`, `PaymentService`, dll.)
- **Observer Pattern** — Reaksi otomatis terhadap event model (cache invalidation, admin logging)
- **Job/Queue Pattern** — Pengiriman email secara asinkron

---

## 📋 Persyaratan Sistem

| Komponen | Versi Minimum |
|----------|--------------|
| PHP | 8.2 atau lebih baru |
| Composer | 2.x |
| Node.js | 18.x atau lebih baru |
| NPM | 9.x atau lebih baru |
| MySQL | 8.0 (atau SQLite untuk development) |
| Git | 2.x |

### Ekstensi PHP yang Diperlukan

- `php-mbstring`
- `php-xml`
- `php-curl`
- `php-mysql` (atau `php-sqlite3` untuk development)
- `php-zip`
- `php-bcmath`
- `php-gd` (untuk manipulasi gambar)

---

## 🚀 Instalasi & Setup

### Cara Cepat (One-Command Setup)

```bash
# 1. Clone repository
git clone https://github.com/your-username/kosmetik-ecommerce.git
cd kosmetik-ecommerce

# 2. Jalankan setup otomatis
composer setup
```

Perintah `composer setup` akan secara otomatis:
- Install dependensi PHP (`composer install`)
- Menyalin `.env.example` ke `.env`
- Generate application key
- Menjalankan migrasi database
- Install dependensi Node.js (`npm install`)
- Build aset frontend (`npm run build`)

### Cara Manual (Step-by-Step)

```bash
# 1. Clone repository
git clone https://github.com/your-username/kosmetik-ecommerce.git
cd kosmetik-ecommerce

# 2. Install dependensi PHP
composer install

# 3. Salin file konfigurasi environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di file .env (lihat bagian Konfigurasi Environment)

# 6. Jalankan migrasi database
php artisan migrate

# 7. (Opsional) Isi data awal (admin, kategori, brand, produk contoh)
php artisan db:seed

# 8. Buat symbolic link untuk storage
php artisan storage:link

# 9. Install dependensi frontend
npm install

# 10. Build aset frontend
npm run build
```

---

## ⚙ Konfigurasi Environment

Salin `.env.example` ke `.env` dan sesuaikan konfigurasi berikut:

### Database

```env
# MySQL (rekomendasi untuk produksi)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kosmetik_ecommerce
DB_USERNAME=root
DB_PASSWORD=your_password

# SQLite (alternatif untuk development cepat)
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite
```

### Midtrans (Payment Gateway)

Dapatkan API key dari [Midtrans Dashboard](https://dashboard.midtrans.com):

```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false          # Ubah ke true untuk produksi
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

> **Catatan:** Untuk mode sandbox/testing, gunakan key dari Midtrans Sandbox. Untuk produksi, gunakan key dari Midtrans Production.

### RajaOngkir (Ongkos Kirim)

Dapatkan API key dari [RajaOngkir](https://rajaongkir.com):

```env
RAJAONGKIR_API_KEY=your_api_key
RAJAONGKIR_BASE_URL=https://api.rajaongkir.com/starter
RAJAONGKIR_ORIGIN_CITY_ID=city_id     # ID kota asal pengiriman
```

> **Catatan:** RajaOngkir tidak memiliki environment sandbox terpisah. Perbedaan hanya pada tipe API key (free/trial vs production).

### Email (SMTP)

```env
# Development — email ditulis ke log file
MAIL_MAILER=log

# Produksi — gunakan SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@kosmetik.example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue

```env
QUEUE_CONNECTION=database              # Menggunakan database sebagai queue driver
```

### Cache

```env
# Development
CACHE_STORE=file

# Produksi (rekomendasi)
CACHE_STORE=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## ▶ Menjalankan Aplikasi

### Mode Development (Semua Service Sekaligus)

```bash
composer dev
```

Perintah ini menjalankan 4 service secara bersamaan dengan `concurrently`:

| Service | Deskripsi | Warna Log |
|---------|-----------|-----------|
| 🌐 **server** | `php artisan serve` — Web server di `http://localhost:8000` | Biru |
| 📨 **queue** | `php artisan queue:listen` — Worker untuk memproses email & job | Ungu |
| 📋 **logs** | `php artisan pail` — Real-time log viewer di terminal | Pink |
| ⚡ **vite** | `npm run dev` — Hot-reload untuk perubahan frontend | Oranye |

### Mode Manual (Jalankan Terpisah)

Buka 4 terminal terpisah:

```bash
# Terminal 1 — Web server
php artisan serve

# Terminal 2 — Queue worker (untuk email)
php artisan queue:work

# Terminal 3 — Frontend dev server (hot-reload)
npm run dev

# Terminal 4 (opsional) — Real-time log viewer
php artisan pail
```

Akses aplikasi di: **http://localhost:8000**

---

## 👤 Akun Default

Setelah menjalankan `php artisan db:seed`, akun-akun berikut akan tersedia:

### Admin

| Field | Value |
|-------|-------|
| Email | `admin@kosmetik.example.com` |
| Password | `Admin@12345` |
| Role | Admin |

### Customer

Buat akun customer baru melalui halaman **Register** di aplikasi.

### Data Seed Lainnya

Seeder juga akan membuat data contoh berikut:

| Data | Deskripsi |
|------|-----------|
| **Kategori** | Beberapa kategori produk kosmetik |
| **Brand** | Beberapa brand kosmetik dengan logo |
| **Produk** | Contoh produk dengan gambar, harga, dan deskripsi |

---

## 📚 Panduan Penggunaan

### Alur Customer

```
Registrasi → Verifikasi Email → Login → Jelajahi Katalog → Tambah ke Keranjang
    → Checkout → Pilih Alamat → Pilih Kurir → Terapkan Voucher (opsional)
    → Buat Pesanan → Bayar via Midtrans → Terima Konfirmasi Email
    → Pantau Status Pesanan → Konfirmasi Penerimaan → Beri Review
```

1. **Registrasi & Login**
   - Buka halaman Register dan buat akun baru
   - Cek email untuk verifikasi (di development, cek `storage/logs/laravel.log`)
   - Login setelah email terverifikasi

2. **Browsing & Pencarian**
   - Jelajahi produk di halaman Katalog (`/catalog`)
   - Gunakan filter: kategori, brand, rentang harga
   - Gunakan sorting: terbaru, harga terendah/tertinggi, terlaris
   - Cari produk spesifik melalui fitur pencarian (`/search`)

3. **Keranjang & Checkout**
   - Tambahkan produk ke keranjang dari halaman detail produk
   - Kelola item di keranjang (ubah jumlah, hapus)
   - Lanjutkan ke checkout, pilih/tambah alamat pengiriman
   - Pilih layanan kurir dan lihat estimasi ongkir
   - Terapkan kode voucher untuk diskon (jika ada)
   - Konfirmasi pesanan

4. **Pembayaran**
   - Setelah checkout, halaman pembayaran Midtrans akan muncul
   - Pilih metode pembayaran (transfer bank, e-wallet, kartu kredit, dll.)
   - Selesaikan pembayaran sesuai instruksi
   - Status pembayaran diperbarui otomatis via webhook Midtrans

5. **Pesanan & Review**
   - Pantau status pesanan di halaman Riwayat Pesanan (`/orders`)
   - Konfirmasi penerimaan barang setelah diterima
   - Berikan rating dan review untuk produk yang sudah dibeli

### Alur Admin

```
Login (admin) → Dashboard → Kelola Produk/Brand/Kategori/Voucher
    → Proses Pesanan → Update Status & Resi → Monitor Pengguna → Lihat Laporan
```

1. **Dashboard** (`/admin/dashboard`)
   - Lihat ringkasan: total pesanan, pendapatan, jumlah produk, jumlah pengguna

2. **Kelola Produk** (`/admin/products`)
   - Tambah produk baru dengan gambar, varian, SKU, berat
   - Edit/hapus produk, toggle status aktif/nonaktif

3. **Kelola Pesanan** (`/admin/orders`)
   - Lihat semua pesanan masuk
   - Ubah status pesanan: `pending` → `processing` → `shipped` → `delivered`
   - Input nomor resi pengiriman

4. **Kelola Voucher** (`/admin/vouchers`)
   - Buat voucher diskon (persentase atau nominal tetap)
   - Atur minimal pembelian, kuota penggunaan, periode berlaku

5. **Laporan** (`/admin/reports`)
   - Lihat laporan penjualan dan statistik bisnis

---

## 📁 Struktur Proyek

```
kosmetik-ecommerce/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/                    # Controller panel admin
│   │   │   │   ├── DashboardController   # Dashboard admin
│   │   │   │   ├── ProductController     # CRUD produk
│   │   │   │   ├── BrandController       # CRUD brand
│   │   │   │   ├── CategoryController    # CRUD kategori
│   │   │   │   ├── VoucherController     # CRUD voucher
│   │   │   │   ├── OrderController       # Manajemen pesanan
│   │   │   │   ├── UserController        # Manajemen pengguna
│   │   │   │   ├── ReviewController      # Moderasi review
│   │   │   │   ├── AdminLogController    # Log aktivitas admin
│   │   │   │   └── ReportController      # Laporan penjualan
│   │   │   ├── Customer/                 # Controller sisi customer
│   │   │   │   ├── ProductController     # Katalog & detail produk
│   │   │   │   ├── CartController        # Keranjang belanja
│   │   │   │   ├── CheckoutController    # Proses checkout
│   │   │   │   ├── PaymentController     # Halaman pembayaran
│   │   │   │   ├── OrderController       # Riwayat & detail pesanan
│   │   │   │   ├── WishlistController    # Wishlist
│   │   │   │   ├── ReviewController      # Submit review
│   │   │   │   ├── ProfileController     # Profil pengguna
│   │   │   │   ├── AddressController     # Manajemen alamat
│   │   │   │   ├── ShippingController    # Kalkulasi ongkir
│   │   │   │   ├── VoucherController     # Validasi voucher
│   │   │   │   ├── RajaOngkirController  # API helper RajaOngkir
│   │   │   │   └── DashboardController   # Dashboard customer
│   │   │   ├── Auth/                     # Controller autentikasi (Breeze)
│   │   │   ├── HomeController            # Halaman utama
│   │   │   └── MidtransWebhookController # Webhook Midtrans
│   │   ├── Middleware/
│   │   │   ├── AdminMiddleware           # Proteksi akses admin
│   │   │   ├── CheckAccountActive        # Cek akun aktif/nonaktif
│   │   │   ├── ForceHttps                # Redirect HTTP → HTTPS (produksi)
│   │   │   └── RedirectDashboardByRole   # Redirect dashboard sesuai role
│   │   └── Requests/                     # Form Request validation
│   ├── Jobs/
│   │   ├── SendEmailVerificationJob      # Email verifikasi
│   │   ├── SendOrderConfirmationJob      # Email konfirmasi pesanan
│   │   ├── SendPaymentConfirmationJob    # Email konfirmasi pembayaran
│   │   └── SendOrderStatusUpdateJob      # Email update status pesanan
│   ├── Models/
│   │   ├── User                          # Pengguna (customer/admin)
│   │   ├── Product                       # Produk kosmetik
│   │   ├── ProductImage                  # Galeri gambar produk
│   │   ├── ProductVariant                # Varian produk (ukuran, warna)
│   │   ├── Brand                         # Brand/merek
│   │   ├── Category                      # Kategori produk
│   │   ├── CartItem                      # Item keranjang belanja
│   │   ├── Order                         # Pesanan
│   │   ├── OrderItem                     # Item dalam pesanan
│   │   ├── Payment                       # Data pembayaran
│   │   ├── Voucher                       # Voucher diskon
│   │   ├── Review                        # Ulasan produk
│   │   ├── Wishlist                      # Wishlist/favorit
│   │   ├── Address                       # Alamat pengiriman
│   │   └── AdminLog                      # Log aktivitas admin
│   ├── Observers/                        # Model observers
│   │   ├── ProductObserver               # Cache invalidation produk
│   │   ├── ProductImageObserver          # Cleanup gambar
│   │   ├── OrderObserver                 # Auto-generate order number
│   │   ├── OrderItemObserver             # Update stok produk
│   │   ├── ReviewObserver                # Recalculate average rating
│   │   └── UserObserver                  # Log aktivitas user
│   ├── Repositories/
│   │   └── ProductRepository             # Akses data produk + caching
│   ├── Services/
│   │   ├── CartService                   # Logika keranjang belanja
│   │   ├── OrderService                  # Logika pemesanan
│   │   ├── PaymentService                # Integrasi Midtrans
│   │   ├── ShippingService               # Kalkulasi ongkir
│   │   ├── RajaOngkirClient              # HTTP client RajaOngkir
│   │   ├── ReviewService                 # Logika review & rating
│   │   └── VoucherService                # Logika voucher diskon
│   ├── Providers/                        # Service providers
│   └── View/                             # View composers/components
├── config/
│   ├── midtrans.php                      # Konfigurasi Midtrans
│   ├── rajaongkir.php                    # Konfigurasi RajaOngkir
│   └── ...                               # Konfigurasi Laravel standar
├── database/
│   ├── migrations/                       # 20 file migrasi database
│   ├── seeders/
│   │   ├── DatabaseSeeder                # Seeder utama
│   │   ├── AdminSeeder                   # Seed akun admin
│   │   ├── CategorySeeder                # Seed kategori
│   │   ├── BrandSeeder                   # Seed brand
│   │   └── ProductSeeder                 # Seed produk contoh
│   └── factories/                        # Model factories untuk testing
├── resources/
│   ├── views/
│   │   ├── home.blade.php                # Halaman utama
│   │   ├── welcome.blade.php             # Landing page
│   │   ├── admin/                        # Views panel admin
│   │   │   ├── dashboard.blade.php
│   │   │   ├── products/                 # CRUD produk
│   │   │   ├── brands/                   # CRUD brand
│   │   │   ├── categories/               # CRUD kategori
│   │   │   ├── vouchers/                 # CRUD voucher
│   │   │   ├── orders/                   # Manajemen pesanan
│   │   │   ├── users/                    # Manajemen user
│   │   │   ├── logs/                     # Activity log
│   │   │   └── reports/                  # Laporan
│   │   ├── customer/                     # Views sisi customer
│   │   │   ├── catalog/                  # Katalog & detail produk
│   │   │   ├── cart/                     # Keranjang belanja
│   │   │   ├── checkout/                 # Halaman checkout
│   │   │   ├── payment/                  # Halaman pembayaran
│   │   │   ├── orders/                   # Riwayat pesanan
│   │   │   ├── wishlist/                 # Wishlist
│   │   │   └── profile/                  # Profil pengguna
│   │   ├── auth/                         # Halaman autentikasi
│   │   ├── emails/                       # Template email
│   │   ├── layouts/                      # Layout utama
│   │   ├── components/                   # Blade components
│   │   └── partials/                     # Partial views
│   └── css/
│       └── app.css                       # Stylesheet utama
├── routes/
│   ├── web.php                           # Route web (customer + admin)
│   ├── api.php                           # Route API (webhook Midtrans)
│   └── auth.php                          # Route autentikasi (Breeze)
├── tests/
│   ├── Feature/                          # Feature tests
│   │   ├── Admin/                        # Test panel admin
│   │   ├── Auth/                         # Test autentikasi
│   │   ├── Customer/                     # Test fitur customer
│   │   ├── SecurityTest.php              # Test keamanan
│   │   ├── CachingTest.php               # Test caching
│   │   ├── PerformanceTest.php           # Test performa
│   │   ├── QueueJobsTest.php             # Test queue/email
│   │   ├── MidtransWebhookTest.php       # Test webhook Midtrans
│   │   └── ShippingCostTest.php          # Test ongkir
│   └── Unit/                             # Unit tests
│       ├── PaymentServiceTest.php        # Test payment service
│       ├── RajaOngkirClientTest.php       # Test RajaOngkir client
│       ├── RajaOngkirControllerTest.php  # Test RajaOngkir controller
│       ├── ShippingServiceTest.php       # Test shipping service
│       └── Middleware/                   # Test middleware
├── docs/                                 # Dokumentasi tambahan
├── CACHING_STRATEGY.md                   # Dokumentasi strategi caching
├── QUEUE_SETUP.md                        # Dokumentasi setup queue/email
├── SECURITY_AUDIT.md                     # Laporan audit keamanan
└── ...
```

---

## 🗃 Database Schema

Aplikasi menggunakan 15 tabel utama:

```
┌─────────────┐    ┌──────────────┐    ┌────────────────┐
│    users     │───▶│   addresses  │    │   categories   │
│  (customer/  │    │  (multi-addr)│    │                │
│    admin)    │    └──────────────┘    └───────┬────────┘
│              │                                │
│              │    ┌──────────────┐    ┌───────▼────────┐
│              │───▶│  cart_items   │───▶│   products     │◀── brands
│              │    └──────────────┘    │  (slug, sku,   │
│              │                       │   weight, stock)│
│              │    ┌──────────────┐    └──┬──────┬──────┘
│              │───▶│  wishlists   │───────┘      │
│              │    └──────────────┘         ┌────┴──────────┐
│              │                            │                │
│              │    ┌──────────────┐    ┌────▼───────┐  ┌────▼──────────┐
│              │───▶│   orders     │    │ product_   │  │ product_      │
│              │    │ (status,     │    │ images     │  │ variants      │
│              │    │  tracking)   │    │ (multi-img)│  │ (size, color) │
│              │    └──┬───┬───────┘    └────────────┘  └───────────────┘
│              │       │   │
│              │  ┌────▼┐ ┌▼──────────┐
│              │  │order│ │ payments   │
│              │──│items│ │ (midtrans) │
│              │  └─────┘ └───────────┘
│              │
│              │──▶ reviews (rating 1-5, comment)
│              │──▶ admin_logs (audit trail)
└─────────────┘

┌──────────────┐
│   vouchers   │ (code, discount_type, amount, quota, validity)
└──────────────┘
```

### Status Pesanan (Order Flow)

```
pending → processing → shipped → delivered
                                    ↓
                              (customer confirm)
                                    ↓
                              completed / refund_requested
```

---

## 🔌 API & Webhook

### Midtrans Webhook

Endpoint untuk menerima notifikasi pembayaran dari Midtrans:

```
POST /api/webhook/midtrans
```

| Aspek | Detail |
|-------|--------|
| **URL** | `{APP_URL}/api/webhook/midtrans` |
| **Method** | `POST` |
| **Auth** | Signature verification (bukan CSRF) |
| **Controller** | `MidtransWebhookController` |
| **Fungsi** | Update status pembayaran & pesanan secara otomatis |

> **Setup di Midtrans Dashboard:** Masukkan URL webhook di Settings → Configuration → Notification URL

Dokumentasi lengkap: [docs/MIDTRANS_WEBHOOK.md](docs/MIDTRANS_WEBHOOK.md)

### Internal AJAX Endpoints

| Endpoint | Method | Fungsi |
|----------|--------|--------|
| `/voucher/validate` | POST | Validasi kode voucher |
| `/shipping/cost` | GET | Kalkulasi ongkos kirim |
| `/customer/rajaongkir/provinces` | GET | Daftar provinsi |
| `/customer/rajaongkir/cities` | GET | Daftar kota/kabupaten |
| `/customer/rajaongkir/subdistricts` | GET | Daftar kecamatan |
| `/customer/rajaongkir/postal-codes` | GET | Daftar kode pos |
| `/customer/addresses` | POST | Tambah alamat baru |
| `/customer/addresses/{id}` | PUT | Update alamat |
| `/customer/addresses/{id}` | DELETE | Hapus alamat |

---

## 📨 Queue Jobs

Semua email dikirim secara asinkron melalui Laravel Queue (database driver):

| Job | Trigger | Retry |
|-----|---------|-------|
| `SendEmailVerificationJob` | Registrasi user / request resend | 3x, backoff 60s |
| `SendOrderConfirmationJob` | Pesanan dibuat | 3x, backoff 60s |
| `SendPaymentConfirmationJob` | Pembayaran dikonfirmasi (webhook) | 3x, backoff 60s |
| `SendOrderStatusUpdateJob` | Admin update status / input resi | 3x, backoff 60s |

### Monitoring Queue

```bash
# Lihat job yang tertunda
php artisan queue:monitor

# Lihat job yang gagal
php artisan queue:failed

# Retry semua job gagal
php artisan queue:retry all
```

Dokumentasi lengkap: [QUEUE_SETUP.md](QUEUE_SETUP.md)

---

## ⚡ Caching

Sistem caching diimplementasikan untuk memenuhi target performa:

| Data | Cache Key | TTL | Invalidasi |
|------|-----------|-----|------------|
| Kategori | `categories_all` | 60 menit | `ProductObserver` |
| Brand aktif | `brands_all_active` | 60 menit | `ProductObserver` |
| Produk terlaris | `best_sellers` | 60 menit | `ProductObserver`, `ReviewObserver` |
| Produk terbaru | `latest_products` | 30 menit | `ProductObserver` |
| Brand unggulan | `featured_brands` | 60 menit | `ProductObserver` |
| Rating produk | `product_avg_rating_{id}` | 60 menit | `ReviewObserver` |

### Hasil Performa

| Metrik | Target | Hasil |
|--------|--------|-------|
| Halaman katalog (cold cache) | < 3 detik | ~0.55 detik ✅ |
| Halaman katalog (warm cache) | < 3 detik | ~0.09 detik ✅ |
| Pencarian produk | < 500ms | ~0.12 detik ✅ |

Dokumentasi lengkap: [CACHING_STRATEGY.md](CACHING_STRATEGY.md)

---

## 🔒 Keamanan

| Aspek | Implementasi | Status |
|-------|-------------|--------|
| **Password** | Bcrypt hashing (12 rounds) | ✅ |
| **HTTPS** | ForceHttps middleware (produksi) | ✅ |
| **SQL Injection** | Eloquent ORM + parameterized queries | ✅ |
| **XSS** | Blade auto-escaping (`{{ }}`) | ✅ |
| **CSRF** | `@csrf` pada semua form POST/PUT/PATCH/DELETE | ✅ |
| **Admin Access** | `AdminMiddleware` pada semua route `/admin/*` | ✅ |
| **Rate Limiting** | 5 percobaan login, lock 15 menit | ✅ |
| **Account Lock** | Otomatis setelah 5 gagal login berturut-turut | ✅ |
| **Email Verification** | Wajib untuk operasi sensitif | ✅ |
| **File Upload** | Validasi tipe (jpg, png, webp) & ukuran (max 2MB) | ✅ |
| **Session** | Database driver, 7 hari lifetime | ✅ |
| **Webhook** | Signature verification (Midtrans) | ✅ |

Dokumentasi lengkap: [SECURITY_AUDIT.md](SECURITY_AUDIT.md)

---

## 🧪 Testing

### Menjalankan Semua Test

```bash
# Menggunakan composer script
composer test

# Atau langsung
php artisan test
```

### Menjalankan Test Spesifik

```bash
# Test keamanan
php artisan test --filter=SecurityTest

# Test caching & performa
php artisan test --filter=CachingTest
php artisan test --filter=PerformanceTest

# Test queue/email
php artisan test --filter=QueueJobsTest

# Test webhook Midtrans
php artisan test --filter=MidtransWebhookTest

# Test ongkos kirim
php artisan test --filter=ShippingCostTest

# Test activity log admin
php artisan test --filter=AdminActivityLogTest

# Unit test payment service
php artisan test --filter=PaymentServiceTest

# Unit test shipping service
php artisan test --filter=ShippingServiceTest
```

### Cakupan Test

| Kategori | File Test | Jumlah |
|----------|-----------|--------|
| Feature Tests | Security, Caching, Performance, Queue, Webhook, Shipping, Admin Log | 7 |
| Unit Tests | PaymentService, RajaOngkirClient, RajaOngkirController, ShippingService, Middleware | 5 |
| Auth Tests | Login, Register, Password Reset, dll. (Laravel Breeze) | Bawaan |

---

## 🚢 Deployment Produksi

### Checklist Konfigurasi

```env
# Wajib diubah untuk produksi
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Generate key baru
php artisan key:generate

# Midtrans production
MIDTRANS_IS_PRODUCTION=true
MIDTRANS_SERVER_KEY=production_server_key
MIDTRANS_CLIENT_KEY=production_client_key

# Email production
MAIL_MAILER=smtp
```

### Langkah Deployment

```bash
# 1. Install dependensi tanpa dev packages
composer install --optimize-autoloader --no-dev

# 2. Build aset frontend
npm run build

# 3. Jalankan migrasi database
php artisan migrate --force

# 4. Cache konfigurasi, route, dan views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Buat symbolic link storage
php artisan storage:link

# 6. Setup queue worker dengan Supervisor (lihat QUEUE_SETUP.md)
```

### Supervisor (Queue Worker Produksi)

```ini
[program:kosmetik-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/kosmetik-ecommerce/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/kosmetik-ecommerce/storage/logs/worker.log
stopwaitsecs=3600
```

### Keamanan Produksi

- [ ] Install sertifikat SSL/TLS
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_URL` dengan `https://`
- [ ] Konfigurasi firewall
- [ ] Aktifkan HSTS (HTTP Strict Transport Security)
- [ ] Setup monitoring error & failed jobs
- [ ] Backup database secara berkala

---

## 📄 Dokumentasi Tambahan

| Dokumen | Deskripsi |
|---------|-----------|
| [CACHING_STRATEGY.md](CACHING_STRATEGY.md) | Strategi caching, TTL, invalidation, monitoring |
| [QUEUE_SETUP.md](QUEUE_SETUP.md) | Setup queue worker, Supervisor, monitoring job |
| [SECURITY_AUDIT.md](SECURITY_AUDIT.md) | Audit keamanan lengkap, checklist deployment |
| [docs/MIDTRANS_WEBHOOK.md](docs/MIDTRANS_WEBHOOK.md) | Setup & konfigurasi webhook Midtrans |
| [docs/admin-activity-logging.md](docs/admin-activity-logging.md) | Sistem logging aktivitas admin |

---

## 📝 Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).
