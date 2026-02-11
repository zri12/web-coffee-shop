# Instruksi Setup Manual Order Feature

## ⚠️ ERROR: "An error occurred. Please try again."

Error ini terjadi karena database belum diupdate dengan kolom-kolom yang diperlukan untuk fitur Manual Order.

## 🔧 Cara Memperbaiki:

### Langkah 1: Buka phpMyAdmin atau MySQL Workbench

1. Buka browser dan akses phpMyAdmin (biasanya di http://localhost/phpmyadmin)
2. Login dengan username dan password MySQL Anda
3. Pilih database `cafe_db` di sidebar kiri

### Langkah 2: Jalankan SQL Script

1. Klik tab **SQL** di bagian atas
2. Buka file: `database/setup_manual_order.sql`
3. **Copy seluruh isi file** dan paste ke SQL editor
4. Klik tombol **Go** atau **Execute** untuk menjalankan script

### Langkah 3: Verifikasi

Setelah script berhasil dijalankan, Anda akan melihat pesan:
- ✅ "Setup completed successfully!"
- Informasi struktur tabel orders dan order_items
- Total orders dan order items di database

### Langkah 4: Test Fitur

1. Refresh halaman website (tekan F5 atau Ctrl+R)
2. Buka console browser (F12 > Console tab) untuk melihat debug log
3. Coba buat order baru:
   - Pilih table
   - Tambah item dengan klik menu
   - Pilih customization (temperature, size, add-ons, dll)
   - Klik Add to Order
   - Pilih payment method (CASH/CARD/QRIS)
   - Klik Place Order
4. Order harus berhasil dan redirect ke halaman Incoming Orders

## 📋 Perubahan Database yang Dilakukan:

### Table `orders`:
- ✅ Update `order_type` ENUM: dari `('qr', 'manual')` → `('dine_in', 'takeaway')`
- ✅ Tambah kolom `payment_method` ENUM('cash', 'qris', 'card')
- ✅ Tambah kolom `payment_status` ENUM('unpaid', 'paid')
- ✅ Tambah index untuk `payment_status`

### Table `order_items`:
- ✅ Tambah kolom `menu_name` VARCHAR(255)
- ✅ Tambah kolom `price` DECIMAL(12,2) (untuk support custom pricing dengan add-ons)
- ✅ Update existing records: populate `price` dari `unit_price`

## 🐛 Debugging Tips:

### Jika masih error setelah menjalankan SQL:

1. **Clear Browser Cache**
   - Tekan Ctrl+Shift+Delete
   - Pilih "Cached images and files"
   - Clear data

2. **Check Browser Console**
   - Tekan F12
   - Klik tab Console
   - Lihat error messages (warna merah)
   - Screenshot dan share jika perlu bantuan

3. **Check Network Tab**
   - Di Developer Tools, klik tab Network
   - Click Place Order
   - Cari request ke `/cashier/manual-order` (warna merah = error)
   - Klik request tersebut
   - Lihat tab Response untuk pesan error detail

4. **Check Laravel Logs**
   - Buka file: `storage/logs/laravel.log`
   - Scroll ke bagian paling bawah
   - Lihat error message terbaru

## ✅ Test Checklist:

Setelah setup, pastikan hal-hal berikut berfungsi:

- [ ] Modal customization muncul dengan options (temperature, ice, sugar, size, dll)
- [ ] Price calculation otomatis dengan add-ons
- [ ] Cart menampilkan customization notes dengan format yang bagus
- [ ] Remove item dari cart berfungsi
- [ ] Subtotal dan Tax (5%) dihitung dengan benar
- [ ] Payment method selection (CASH/CARD/QRIS) berfungsi
- [ ] Place Order berhasil tanpa error
- [ ] Redirect ke Incoming Orders page
- [ ] Order muncul di New Order section dengan status "pending"
- [ ] Order items menampilkan customization notes

## 📞 Bantuan Lebih Lanjut:

Jika masih ada masalah setelah mengikuti semua langkah:

1. Check console browser untuk error message
2. Check network tab untuk response error
3. Check Laravel logs di `storage/logs/laravel.log`
4. Share screenshot error untuk bantuan lebih lanjut

## 🎉 Setelah Berhasil:

Fitur yang sekarang berfungsi:
- ✅ Manual order creation dari cashier
- ✅ Product customization (temperature, ice, sugar, size, add-ons)
- ✅ Dynamic price calculation dengan add-ons
- ✅ Cart management
- ✅ Multiple payment methods
- ✅ Order tracking di incoming orders
- ✅ Formatted customization notes
