# Cafe Web Ordering System - Tutorial Setup

## 🏗️ Struktur Proyek

Proyek ini adalah **Full Laravel** dengan Blade templates:

```
WEB CAFFEE/
├── app/                    # Laravel application code
│   ├── Http/Controllers/   # Controllers
│   └── Models/             # Eloquent models
├── resources/views/        # Blade templates
│   ├── layouts/           # Base layouts
│   ├── pages/             # Public pages
│   ├── dashboard/         # Admin dashboard
│   └── auth/              # Authentication
├── routes/web.php          # Web routes
├── public-laravel/         # Public assets
├── database/cafe_db.sql    # Database schema
└── .env                    # Environment config
```

---

## ⚙️ Requirements

- **PHP** 8.2+
- **Composer** 2.x
- **Node.js** 18+ & npm
- **MySQL** 8.x

---

## 🚀 Quick Start

### 1. Setup Database

```bash
# Buat database di MySQL
mysql -u root -p -e "CREATE DATABASE cafe_db;"

# Import schema
mysql -u root -p cafe_db < database/cafe_db.sql
```

### 2. Configure Environment

Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cafe_db
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Install Dependencies

```bash
# PHP dependencies
composer install

# Node dependencies (untuk Tailwind CSS)
npm install

# Generate autoload
composer dump-autoload
```

### 4. Build Assets

```bash
# Development (dengan hot reload)
npm run dev

# ATAU Production build
npm run build
```

### 5. Start Server

```bash
php artisan serve --port=8000
```

Buka browser: **http://localhost:8000**

---

## 👤 Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@cafe.com | password |
| Cashier | cashier@cafe.com | password |
| Manager | manager@cafe.com | password |

---

## 📱 Pages Overview

### Public Pages
- `/` - Home page
- `/menu` - Menu list
- `/cart` - Shopping cart
- `/track` - Track order

### Staff Dashboard
- `/dashboard` - Overview
- `/dashboard/orders` - Manage orders
- `/dashboard/menus` - Menu CRUD
- `/dashboard/categories` - Category CRUD
- `/dashboard/users` - User management
- `/dashboard/reports` - Sales reports

---

## ❗ Troubleshooting

### npm install error dengan OneDrive

Jika ada error saat `npm install`:

```bash
# Pindahkan folder ke lokasi tanpa spasi
# Contoh: C:\Projects\cafe-web\

# Atau gunakan short path:
cd "C:\Users\asus\ONEDRI~1\DESKTO~1\PROJEC~1\WEBCAF~1\WEBCAF~1"
npm install
```

### php artisan error

```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Regenerate autoload
composer dump-autoload
```

### Assets tidak muncul

Pastikan sudah menjalankan:
```bash
npm run dev
# atau
npm run build
```

---

## 🌐 Hosting

### Shared Hosting (cPanel)

1. Upload semua file ke `public_html/`
2. Pindahkan isi `public-laravel/` ke root
3. Edit `index.php` untuk path yang benar
4. Import `cafe_db.sql` ke MySQL
5. Update `.env` dengan kredensial database

### VPS/Cloud

```bash
# Install dependencies
composer install --no-dev
npm run build

# Set permissions
chmod -R 755 storage bootstrap/cache

# Configure nginx/apache
# Point document root ke public-laravel/
```

---

*Tutorial untuk Kopi Nusantara Cafe System*
