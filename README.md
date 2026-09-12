# Setup — Marketplace Katering

Aplikasi marketplace katering (merchant ↔ customer) — Laravel 13, MySQL, Tailwind + DaisyUI.

## Kebutuhan

- PHP 8.4 + Composer
- MySQL (punya Laragon: MariaDB di `127.0.0.1:3306`)
- Node.js 22 + npm

## Langkah

1. **Clone & install dependency**

   ```bash
   git clone <url-repo> marketplace-katering
   cd marketplace-katering
   composer install
   npm install
   ```

2. **Konfigurasi environment**

   ```bash
   copy .env.example .env        # (Linux/macOS: cp .env.example .env)
   ```

   Edit `.env`, ubah bagian ini:

   ```env
   APP_NAME="Marketplace Katering"
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=marketplace-katering
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Siapkan database** — buat database kosong bernama `marketplace-katering`
   (mis. buka phpMyAdmin → Databases → create, atau `mysql -u root -e "CREATE DATABASE \`marketplace-katering\`"`)

4. **Generate key, migrasi + seed, link storage**

   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```

   Seed mengisi: 1 admin, 2 katering (profil + menu), 2 kantor, 3 order contoh.

5. **Build aset & jalankan**

   ```bash
   npm run build
   php artisan serve
   ```

   Buka http://localhost:8000

   > Saat pengembangan frontend, jalankan `npm run dev` sebagai gantinya `npm run build`.

## Akun demo (password: `password`)

| Role     | Email                |
| -------- | -------------------- |
| Admin    | `admin@market.test`  |
| Merchant | `sari@catering.test` |
| Merchant | `dewa@catering.test` |
| Customer | `kantor@office.test` |
| Customer | `hrd@company.test`   |

## Alur singkat

- **Register** pilih role (Katering/Kantor) → otomatis masuk portal sesuai role.
- **Merchant**: lengkapi Profil Katering (wajib: nama perusahaan + kota agar muncul di pencarian) → kelola Menu (foto, harga, kategori, aktif/nonaktif) → Daftar Order (terima/tolak/selesaikan).
- **Customer**: Cari Katering (kata kunci/kota/jenis makanan) → Pesan Sekarang → pilih porsi per menu + tanggal kirim → sistem membuat invoice `INV-YYYYMM-XXXX` → Pesanan Saya → Cetak invoice.
- **Admin**: Olah Users (tambah/edit/nonaktifkan — user nonaktif langsung ter-logout).

## Catatan

- Foto menu tersimpan di `storage/app/public` — `php artisan storage:link` wajib agar tampil.
- Database cukup lewat migration + seeder di atas; dump SQL final tersedia di `database/marketplace-katering.sql` bila diperlukan.
