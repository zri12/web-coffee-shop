# Panduan Upload Project Laravel ke cPanel

Panduan ini akan membantu Anda meng-upload project "WEB CAFFEE" ini ke hosting cPanel hingga online.

## Persiapan Lokal (Di PC Anda)

1.  **Bersihkan Cache**
    Buka terminal di folder project Anda, lalu jalankan:
    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    php artisan view:clear
    ```
    > **PENTING**: Jika muncul error *"Failed to clear cache..."*, itu biasanya karena perintah `php artisan serve` sedang berjalan. **Matikan dulu server (Ctrl+C)** lalu coba lagi. Atau abaikan saja jika sulit, tidak fatal.

2.  **Zip Project**
    - Select semua file dan folder di dalam project Anda **KECUALI**:
        - `node_modules` (Tidak perlu di-upload)
        - `.git` (Jika ada)
        - `.env` (Nanti kita buat baru di cPanel)
    - Klik kanan -> **Sent to** -> **Compressed (zipped) folder**.
    - Beri nama file, misal `web-caffee.zip`.

3.  **Siapkan Database**
    - Pastikan Anda sudah memiliki file SQL database.
    - Gunakan file `database/database_structure.sql` yang sudah kita buat sebelumnya (hanya struktur).
    - Atau jika ingin data dummy, export dari PHPMyadmin lokal Anda.

---

## Langkah 1: Upload File ke cPanel

1.  Login ke cPanel hosting Anda.
2.  Buka **File Manager**.
3.  Masuk ke folder `public_html` (atau subdomain jika pakai subdomain).
4.  Klik **Upload** di bar atas, lalu pilih file `web-caffee.zip` Anda.
5.  Setelah selesai upload, klik kanan file zip tersebut lalu pilih **Extract**.
6.  Setelah diextract, pastikan struktur filenya benar.
    *   *Catatan: Project Laravel memiliki folder `public`. cPanel menggunakan `public_html`. Kita perlu menyesuaikan ini.*

### Metode Struktur yang Disarankan (Keamanan Terbaik):

Agar aman, file core Laravel sebaiknya **TIDAK** ditaruh langsung di dalam `public_html`.

1.  Buat folder baru di root directory cPanel (sejajar dengan public_html, BUKAN di dalamnya), beri nama misal `laravel_app`.
2.  Pindahkan semua file project hasil extract tadi ke dalam folder `laravel_app`, **KECUALI** isi folder `public`.
3.  Buka folder `public` dari hasil extract tadi, pindahkan **SEMUA ISINYA** ke dalam folder `public_html`.
4.  Sekarang edit file `index.php` yang ada di `public_html`.

**Edit `public_html/index.php`:**
Cari baris yang memuat `require` dan sesuaikan path-nya mengarah ke folder `laravel_app`:

```php
// Ubah baris ini:
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
// Menjadi:
if (file_exists($maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {

// Ubah baris ini:
require __DIR__.'/../vendor/autoload.php';
// Menjadi:
require __DIR__.'/../laravel_app/vendor/autoload.php';

// Ubah baris ini:
$app = require_once __DIR__.'/../bootstrap/app.php';
// Menjadi:
$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';
```

---

## Langkah 2: Konfigurasi Database

1.  Di cPanel, buka menu **MySQL® Database Wizard**.
2.  **Step 1:** Buat Database baru (misal: `u12345_cafe_db`).
3.  **Step 2:** Buat User Database baru (misal: `u12345_cafe_user`) dan password. **CATAT PASSWORDNYA!**
4.  **Step 3:** Berikan hak ases **ALL PRIVILEGES** ke user tersebut.
5.  Buka menu **phpMyAdmin** di cPanel.
6.  Pilih database yang baru dibuat.
7.  Klik menu **Import**.
8.  Pilih file `database_structure.sql` (atau file dump database Anda).
9.  Klik **Go** / **Import**.

---

## Langkah 3: Konfigurasi Environment (.env)

1.  Kembali ke **File Manager**.
2.  Masuk ke folder `laravel_app`.
3.  Cari file `.env.example`, copy/duplicate dan rename menjadi `.env`.
    *   *Jika file diawali titik tidak terlihat, klik "Settings" di pojok kanan atas File Manager, lalu centang "Show Hidden Files (dotfiles)".*
4.  Klik kanan `.env` -> **Edit**.
5.  Sesuaikan konfigurasi berikut:

```env
APP_NAME="Kopi Nusantara Cafe"
APP_ENV=production
APP_KEY=  <-- Copy dari .env lokal Anda atau generate baru
APP_DEBUG=false
APP_URL=https://nama-domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u12345_cafe_db      <-- Nama database cPanel
DB_USERNAME=u12345_cafe_user    <-- User database cPanel
DB_PASSWORD=password_anda       <-- Password user database
```

6.  Simpan perubahan.

---

## Langkah 4: Symlink Storage (Penting untuk Gambar)

Agar gambar yang diupload bisa muncul, kita perlu membuat shortcut (symlink) dari folder storage ke public.

**Cara 1 (Via Terminal cPanel - Jika ada):**
1.  Buka menu **Terminal** di cPanel.
2.  Masuk ke folder public_html: `cd public_html`
3.  Jalankan perintah symlink (sesuaikan path):
    `ln -s ../laravel_app/storage/app/public storage`

**Cara 2 (Via PHP Route - Jika tidak ada terminal):**
1.  Buat file baru di `public_html` bernama `symlink.php`
2.  Isi dengan kode berikut:
    ```php
    <?php
    $target = '/home/username_cpanel/laravel_app/storage/app/public';
    $shortcut = '/home/username_cpanel/public_html/storage';
    
    if(symlink($target, $shortcut)){
        echo 'Symlink berhasil dibuat!';
    } else {
        echo 'Gagal membuat symlink.';
    }
    ?>
    ```
    *(Ganti `username_cpanel` dengan username hosting Anda, bisa dilihat di File Manager sebelah kiri atas)*
3.  Akses file tersebut di browser: `namadomain.com/symlink.php`
4.  Jika berhasil, hapus file `symlink.php`.

---

## Langkah Selesai!

Coba akses domain Anda. Website seharusnya sudah muncul.

### Troubleshooting Umum:
1.  **Error 500 / Blank Page**:
    - Cek file `.env`, pastikan DB credentials benar.
    - Pastikan folder `storage` dan `bootstrap/cache` memiliki permission **775** atau **755** (bisa diubah lewat File Manager -> Klik Kanan -> Change Permissions).
2.  **Gambar tidak muncul**:
    - Cek langkah Symlink Storage di atas.
3.  **CSS/JS Berantakan**:
    - Pastikan `APP_URL` di `.env` sudah benar menggunakan `https://`.
