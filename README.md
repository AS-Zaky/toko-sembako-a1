# Toko Sembako A1 — Sistem Pencatatan Stok

Web app untuk mencatat stok masuk, keluar, dan penyesuaian di Toko Sembako A1.
Dibangun dengan **PHP Native + PostgreSQL (PDO pdo_pgsql)**, HTML/CSS/JS tanpa framework.

## Fitur

- Login dengan peran **admin** dan **penjaga**
- CRUD produk, kategori, dan pengguna (admin)
- Barang masuk (dengan pemasok), barang keluar (terjual/rusak/kedaluwarsa), penyesuaian stok
- Stok diperbarui otomatis oleh trigger PostgreSQL — stok dan riwayat tidak pernah berbeda
- Peringatan stok menipis (`stok <= stok_minimum`) di dashboard dan cek harga
- Cek harga & stok cepat, riwayat pergerakan, laporan inventaris per periode
- Katalog publik (tanpa login) yang hanya menampilkan status ketersediaan

## Struktur

Hanya `public/` yang menjadi web root. `app/`, `database/`, `storage/`, dan `.env` tidak bisa diakses browser.

```
public/     halaman + assets (css/js/img)
app/        config, core (Database/Auth/Csrf), models, helpers, views
database/   schema.sql, triggers.sql, seed.sql
storage/    logs
```

## Persyaratan

- PHP 8.1+ dengan ekstensi `pdo_pgsql` dan `pgsql`
- PostgreSQL 13+
- Web server (Apache/Nginx) atau PHP built-in server untuk pengembangan

## Instalasi

1. **Buat database**

   ```sql
   CREATE DATABASE toko_sembako_a1;
   ```

2. **Impor skema, trigger, dan seed**

   ```bash
   psql -U postgres -d toko_sembako_a1 -f database/schema.sql
   psql -U postgres -d toko_sembako_a1 -f database/triggers.sql
   psql -U postgres -d toko_sembako_a1 -f database/seed.sql
   ```

3. **Salin `.env.example` menjadi `.env`** dan isi kredensial database Anda.

4. **Jalankan**

   Pengembangan (dari root proyek):

   ```bash
   php -S localhost:8000 -t public
   ```

   Produksi: arahkan document root web server ke folder `public/`.

5. **Login pertama**

   - Username: `admin`
   - Password: `admin123`

   > **Penting:** seed memakai hash contoh. Setelah impor, ganti kata sandi admin lewat menu Pengguna, atau jalankan skrip di bawah untuk membuat hash baru.

## Mengatur ulang kata sandi admin

Karena `password_hash()` menghasilkan hash berbeda setiap kali, buat hash baru lalu perbarui di database:

```bash
php -r "echo password_hash('admin123', PASSWORD_BCRYPT), PHP_EOL;"
```

```sql
UPDATE users SET password = '<hash-dari-perintah-di-atas>' WHERE username = 'admin';
```

## Keamanan

- Semua query memakai prepared statement (PDO)
- Kata sandi di-hash dengan bcrypt (`password_hash()`)
- Token CSRF pada semua form POST
- Pemeriksaan sesi & peran di awal setiap halaman
- Output di-escape dengan `htmlspecialchars()`
- `.env` dikecualikan dari git (`.gitignore`)

## Backup

Lakukan backup berkala agar catatan tidak hilang seperti buku kertas:

```bash
pg_dump -U postgres toko_sembako_a1 > backup-%date%.sql
```

## Pengujian fungsional

1. Login sebagai admin dan penjaga — menu menyesuaikan peran.
2. Tambah produk, lalu catat barang masuk — stok bertambah otomatis.
3. Catat barang keluar melebihi stok — entri ditolak.
4. Lakukan penyesuaian stok — selisih tercatat di riwayat.
5. Turunkan stok sampai di bawah minimum — produk ditandai di dashboard.
6. Buka `katalog.php` tanpa login — hanya status yang tampil, tanpa harga beli.
