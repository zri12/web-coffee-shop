# Panduan Deploy Laravel ke Vercel

Panduan ini akan membantu Anda men-deploy project "WEB CAFFEE" ke Vercel. 
Karena Vercel aslinya untuk frontend/static, kita menggunakan teknik khusus agar Laravel bisa berjalan.

## Prasyarat Penting

1.  **Database External**: Vercel **TIDAK** menyediakan database MySQL.
    - Anda harus punya database MySQL yang bisa diakses dari luar (Remote MySQL).
    - Bisa pakai: **Supabase**, **PlanetScale**, **Railway** (Database only), atau **Remote MySQL dari Hosting cPanel Anda** (jika diizinkan).
2.  **Akun Vercel & GitHub**: Pastikan sudah terhubung.

---

## Langkah 1: Persiapan Project (Sudah Saya Buatkan)

Saya sudah menambahkan file penting yang dibutuhkan:
1.  **`vercel.json`**: Konfigurasi agar Vercel tahu cara menjalankan PHP/Laravel.
2.  **`api/index.php`**: Entry point tambahan (opsional tapi disarankan).

### Yang Perlu Anda Lakukan:
1.  **Push perubahan terbaru** ke GitHub Anda (termasuk file `vercel.json` yang baru saya buat).
    ```bash
    git add .
    git commit -m "Add Vercel configuration"
    git push origin main
    ```

---

## Langkah 2: Setup di Dashboard Vercel

1.  Buka [vercel.com](https://vercel.com) dan Login.
2.  Klik **"Add New..."** -> **"Project"**.
3.  Pilih repository GitHub `WEB CAFFEE` Anda, klik **Import**.
4.  **Configure Project**:
    *   **Framework Preset**: Pilih `Other`.
    *   **Root Directory**: Biarkan `./` (kosong).
    *   **Environment Variables**: Masukkan settingan berikut (Buka tab Environment Variables):

| Key | Value (Contoh) |
| :--- | :--- |
| `APP_key` | (Copy dari .env lokal Anda, `base64:...`) |
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `DB_CONNECTION`| `mysql` |
| `DB_HOST` | (Host database remote Anda, misal: `ep-xyz.aws.neon.tech`) |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | (Nama database Anda) |
| `DB_USERNAME` | (User database Anda) |
| `DB_PASSWORD` | (Password database Anda) |

> **Catatan Penting Database**:
> Pastikan database Anda mengizinkan koneksi eksternal. Jika pakai cPanel, cari menu "Remote MySQL" dan tambahkan IP Vercel (atau `%` untuk allow all IPs, tapi hati-hati keamanan). Saran terbaik gunakan layanan Cloud Database gratisan seperti **Railway** atau **Supabase**.

5.  Klik **Deploy**.

---

## Langkah 3: Troubleshooting

### Error: "500 Server Error"
Biasanya karena database tidak connect. Cek Log di Vercel:
1.  Buka Project Anda di Vercel.
2.  Klik tab **Logs**.
3.  Lihat errornya. Jika "Connection refused", berarti Database tidak bisa diakses oleh Vercel.

### Error: "404 Not Found" pada Asset (CSS/JS)
Pastikan file `public/` ter-upload dengan benar. Di Laravel Vercel, kadang perlu run build asset sebelum deploy, tapi di `vercel.json` kita sudah arahkan root ke public.

### Session Hilang
Karena Vercel itu "Serverless" (bisa berubah-ubah server), session file driver default Laravel tidak akan jalan sempurna.
**Solusi**: Ubah `SESSION_DRIVER` di Environment Variable Vercel menjadi `cookie` (sudah saya set default) atau gunakan Redis jika punya.

---

## Alternatif Hosting Gratis Lain (Lebih Mudah untuk Laravel)
Jika Vercel terlalu ribet urus databasenya, saya sarankan pakai:
1.  **Railway.app**: Bisa host Laravel + Database sekaligus.
2.  **Fly.io**: Mirip Railway.
3.  **Render.com**: Ada free tier untuk web services.
