# Panduan Hosting Laravel di cPanel (WEB CAFFEE)

Panduan ini khusus untuk deploy project **WEB CAFFEE** ke shared hosting cPanel.

---

## ✅ Kebutuhan Minimal

- PHP 8.2+
- MySQL 8.x
- Composer (opsional, jika hosting mendukung Terminal)
- Akses File Manager atau FTP

---

## 🔧 Persiapan di Lokal

1. Install dependency dan build asset:
```bash
composer install
npm install
npm run build
```

2. Pastikan file `public-laravel/build/` sudah ter-generate.

---

## 📦 Struktur Upload yang Disarankan

Gunakan 2 folder:

- `/home/USERNAME/WEB CAFFEE` → seluruh project Laravel (boleh di luar `public_html`)
- `/home/USERNAME/public_html/demoprojectweb.net` → document root domain (isi dari folder `public-laravel`)

---

## 🚀 Langkah Deploy di cPanel

### 1. Upload Project

Upload semua file project ke folder:

```
/home/USERNAME/WEB CAFFEE
```

### 2. Pindahkan Public

Copy seluruh isi folder `public-laravel`:

```
WEB CAFFEE/public-laravel
```

ke:

```
/home/USERNAME/public_html/demoprojectweb.net
```

Jika saat ini public Anda masih berada di:

```
/home/USERNAME/public_html/demoprojectweb.net/public-laravel
```

maka pindahkan isi folder tersebut ke atas (ke `demoprojectweb.net/`) sampai `index.php` dan `.htaccess` berada langsung di `demoprojectweb.net/`.

### 3. Edit `index.php`

Edit file berikut:

```
/home/USERNAME/public_html/demoprojectweb.net/index.php
```

Jika Anda mengikuti struktur folder persis seperti panduan ini (project di `/home/USERNAME/WEB CAFFEE`), file `index.php` sudah otomatis mencari lokasi project.

Jika struktur folder Anda berbeda (misalnya nama folder bukan `laravel-app`), sesuaikan lokasi project di `index.php` agar mengarah ke folder project Laravel Anda.

```php
require __DIR__.'/../../WEB CAFFEE/vendor/autoload.php';
$app = require_once __DIR__.'/../../WEB CAFFEE/bootstrap/app.php';
```

### 4. Set File `.env`

Copy `.env` dari lokal ke:

```
/home/USERNAME/WEB CAFFEE/.env
```

Sesuaikan database:

```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nama_db
DB_USERNAME=user_db
DB_PASSWORD=password_db
```

### 5. Import Database

Gunakan phpMyAdmin di cPanel:

- Buat database baru
- Import file `database/cafe_db.sql`

### 6. Atur Permission

Jika ada Terminal cPanel:

```bash
chmod -R 755 storage bootstrap/cache
```

Jika tidak ada Terminal, ubah permission via File Manager.

---

## ✅ Opsional (Disarankan)

Jika ada akses Terminal, jalankan:

```bash
php artisan storage:link
php artisan config:cache
php artisan route:cache
```

---

## ❗ Troubleshooting

### Error 500

- Cek `.env` sudah benar
- Pastikan permission storage dan cache benar
- Cek error_log di cPanel
- Pastikan versi PHP untuk domain `demoprojectweb.net` minimal PHP 8.2 (MultiPHP Manager)

### Asset CSS/JS tidak muncul

- Pastikan `public_html/demoprojectweb.net/build/manifest.json` ada
- Pastikan `npm run build` sudah dijalankan sebelum upload

---

*Panduan ini khusus untuk project WEB CAFFEE*
