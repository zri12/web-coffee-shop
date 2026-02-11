# Midtrans Payment Setup - Quick Start Guide

## 🚀 Quick Start (5 Minutes)

### Step 1: Run Database Migration
```bash
php artisan migrate
```

This creates the `settings` table needed for storing payment configuration.

### Step 2: Get Midtrans Keys
1. Go to https://dashboard.sandbox.midtrans.com (for testing)
2. Sign up or login
3. Navigate to **Settings** → **Access Keys**
4. Copy your:
   - **Server Key** (e.g., `SB-Mid-server-xxxxxxxx`)
   - **Client Key** (e.g., `SB-Mid-client-xxxxxxxx`)

### Step 3: Configure in Admin Panel
1. Open your app in browser
2. Login to admin dashboard
3. Go to **Payment Settings**
4. Paste the keys:
   - **Server Key**: `SB-Mid-server-xxxxxxxx`
   - **Client Key**: `SB-Mid-client-xxxxxxxx`
5. Leave "Mode Produksi" unchecked for sandbox testing
6. Click **Simpan Pengaturan**

Done! 🎉 Your Midtrans payment system is now configured.

---

## 🧪 Testing

### Test Payment Flow
1. Go to menu page on your site
2. Add items to cart
3. Click **Checkout**
4. Choose payment method: **QRIS**
5. Complete form and click **Checkout Sekarang**
6. You'll see **Bayar dengan QRIS/Kartu** button
7. Click to open Midtrans payment popup

### Use These Test Card Numbers
| Type | Number | Exp | CVV |
|------|--------|-----|-----|
| Visa | 4811111111111114 | Any future | 123 |
| Mastercard | 5105105105105100 | Any future | 123 |

### Test OTP
When prompted for OTP, use: **123456**

---

## 📱 Payment Methods

Your system now supports:
- ✅ **Cash** - Pay at counter (no online processing)
- ✅ **QRIS** - Scan QR code or use mobile payment (Gopay, OVO, Dana, etc.)
- ✅ **Card** - Debit/Credit card payment (Visa, Mastercard, etc.)

---

## 🔧 What Was Set Up

### New Files Created:
- `app/Models/Setting.php` - Stores payment configurations
- `app/Services/MidtransService.php` - Midtrans helper utilities
- `app/Http/Controllers/PaymentController.php` - Payment processing logic
- `database/migrations/2025_02_10_000001_create_settings_table.php` - Settings table
- `MIDTRANS_SETUP.md` - Comprehensive documentation

### Files Modified:
- `routes/web.php` - Added payment routes
- `app/Http/Controllers/OrderController.php` - Enhanced checkout
- `app/Http/Controllers/Dashboard/DashboardController.php` - Payment settings
- `resources/views/admin/payment-settings.blade.php` - Config form
- `resources/views/pages/order-success.blade.php` - Payment UI
- `resources/views/pages/cart.blade.php` - Added phone number field

### New Routes:
```
POST   /payment/{order}/process         → Process payment (internal)
GET    /payment/success                 → Success callback
GET    /payment/error                   → Error callback
GET    /payment/pending                 → Pending callback
GET    /payment/{order}/status          → Check status (JSON)
POST   /midtrans/webhook                → Webhook handler
```

---

## 📊 Monitoring Payments

### In Your Admin Dashboard
1. Go to **Orders** page
2. See payment status in order list
3. Click order to see payment details
4. Check payment method, status, and transaction ID

### In Midtrans Dashboard
1. Login to https://dashboard.sandbox.midtrans.com
2. Go to **Transactions**
3. See all payment attempts
4. Check status, amount, and payment method

---

## 🆘 Troubleshooting

### "Konfigurasi Midtrans belum lengkap"
**Problem**: Payment button doesn't work  
**Solution**:
- Check Payment Settings page
- Make sure Server Key and Client Key are filled
- Click Save again
- Refresh page

### "QRIS button doesn't appear"
**Problem**: No payment button on success page  
**Solution**:
- Open browser console (F12)
- Check for JavaScript errors
- Verify client key in Payment Settings
- Check server logs: `tail -f storage/logs/laravel.log`

### Payment not showing in orders
**Problem**: After paying, order still shows "unpaid"  
**Solution**:
- Check webhook is receiving (check Midtrans dashboard)
- May take a few seconds to update
- Refresh page
- Check database: `orders.payment_status` field

### "Mode Produksi" not working
**Problem**: Production keys don't work  
**Solution**:
- Verify you're using production Server Key and Client Key
- Make sure "Mode Produksi" is **checked**
- Check Midtrans production dashboard
- Ensure SSL/HTTPS is enabled

---

## 🎯 Next Steps

1. **Test thoroughly** with sandbox keys first
2. **Get production keys** when ready
3. **Update settings** with production keys
4. **Check "Mode Produksi"** in Payment Settings
5. **Monitor first transactions** to ensure success

---

## 📞 Support Resources

- **Midtrans Docs**: https://docs.midtrans.com
- **Midtrans Chat**: Available in Midtrans Dashboard
- **Laravel Docs**: https://laravel.com/docs
- **Your app logs**: `storage/logs/laravel.log`

---

## 🔐 Security Checklist

- ✅ Server Key is kept secret (never in frontend)
- ✅ All transactions logged for audit
- ✅ 3DS fraud protection enabled
- ✅ HTTPS enabled in production
- ✅ Webhook verification active
- ✅ Customer phone stored for payment notifications

---

## 💡 Key Features Included

| Feature | Status |
|---------|--------|
| QRIS Payment | ✅ Active |
| Card Payment | ✅ Active |
| Cash on Delivery | ✅ Active |
| Payment Status Tracking | ✅ Active |
| Order Confirmation | ✅ Automatic |
| Transaction Logging | ✅ Active |
| Webhook Notifications | ✅ Active |
| Refund Processing | ❌ Manual (future) |
| Payment Installments | ❌ Future enhancement |

---

## 🎓 For Developers

### Check Configuration
```php
// In your code
use App\Services\MidtransService;

if (MidtransService::isConfigured()) {
    // Proceed with payments
}
```

### Create Payment
```php
$params = MidtransService::preparePaymentParams($order);
$snapToken = MidtransService::createSnapToken($params);
```

### Check Payment Status
```php
$status = MidtransService::getStatus($orderId);
echo $status->transaction_status; // 'settlement', 'capture', 'pending', etc.
```

---

**Setup complete!** Your payment system is ready to accept payments. 🎉

For detailed information, see `MIDTRANS_SETUP.md`
