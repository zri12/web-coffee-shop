# Midtrans Setup Checklist

Use this checklist to confirm all Midtrans payment components are properly installed and configured.

## ✅ Pre-Setup Requirements

- [ ] PHP 8.2+ installed
- [ ] Laravel 12 application running
- [ ] Composer dependencies installed (`composer install` run)
- [ ] Database connection working
- [ ] `.env` file exists and is readable/writable
- [ ] HTTP server running (Artisan or production)

## 🔧 Installation Steps

### Step 1: Database Setup
- [ ] Run migrations: `php artisan migrate`
- [ ] Verify `settings` table created: `php artisan tinker -> DB::table('settings')->count()`
- [ ] Connection successful without errors

### Step 2: Get Midtrans Credentials
- [ ] Create account at https://dashboard.sandbox.midtrans.com
- [ ] Found and copied **Server Key** (SB-Mid-server-xxxxxxxx)
- [ ] Found and copied **Client Key** (SB-Mid-client-xxxxxxxx)
- [ ] Keys are valid (no typos)

### Step 3: Configure in Admin Panel
- [ ] Login to admin dashboard
- [ ] Navigate to **Payment Settings** (http://localhost:8000/admin/payment-settings)
- [ ] Pasted **Server Key** in "Server Key" field
- [ ] Pasted **Client Key** in "Client Key" field
- [ ] Verified form shows the entered values
- [ ] Did NOT check "Mode Produksi" (keep unchecked for sandbox)
- [ ] Clicked "Simpan Pengaturan" button
- [ ] Saw success message
- [ ] Page refreshed without errors

### Step 4: Verify Configuration
- [ ] Check `.env` file contains:
  ```
  MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
  MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
  MIDTRANS_IS_PRODUCTION=false
  ```
- [ ] Run `php artisan config:cache` to refresh config
- [ ] No errors in console/logs

## 🧪 Basic Testing

### Test 1: Payment Settings Page Load
- [ ] Open http://localhost:8000/admin/payment-settings
- [ ] Page loads without errors
- [ ] Keys are visible (Server Key shows as dots, Client Key visible)
- [ ] Production checkbox visible
- [ ] Info box shows with Midtrans link
- [ ] Save button visible and clickable

### Test 2: Payment Method Selection
- [ ] Go to http://localhost:8000/menu
- [ ] Add any item to cart
- [ ] Click "Checkout" or go to cart
- [ ] Form shows:
  - [ ] Customer name field
  - [ ] Phone number field
  - [ ] Table number field (optional)
  - [ ] Payment method selection (Cash vs QRIS)
  - [ ] Submit button

### Test 3: Cash Payment (No Online Processing)
- [ ] Select **Cash** payment method
- [ ] Fill in customer info
- [ ] Click **Checkout Sekarang**
- [ ] Order created successfully
- [ ] See success page with order number
- [ ] OK button available (no payment button)
- [ ] Order appears in admin **Orders** list
- [ ] Payment status shows as **unpaid** (cash orders)

### Test 4: QRIS Payment (With Midtrans Token)
- [ ] Select **QRIS** payment method
- [ ] Fill in customer info (name, phone required)
- [ ] Click **Checkout Sekarang**
- [ ] Order created successfully
- [ ] Success page shows payment button
- [ ] Payment button text: "Bayar dengan QRIS/Kartu"
- [ ] No JavaScript errors in console
- [ ] Payment button is clickable

### Test 5: Payment Popup Opens
- [ ] Click payment button
- [ ] Midtrans payment popup opens (not 404 or blank)
- [ ] Popup has payment options visible
- [ ] Can see QRIS code option or card entry option
- [ ] Popup has close button

### Test 6: Test Card Payment
- [ ] In payment popup, select card payment option
- [ ] Fill in test card number: `4811111111111114`
- [ ] Set expiry: Any future date (e.g., 12/25)
- [ ] Set CVV: `123`
- [ ] Complete payment
- [ ] See success message
- [ ] Popup closes
- [ ] Redirected back to success page

## 🔍 Verification Tasks

### Check Database Records
```bash
# In Laravel Tinker:
php artisan tinker
> Order::latest()->first()
> Payment::latest()->first()
> Setting::all()
```

- [ ] Orders table has new records
- [ ] Payments table has new records with status
- [ ] Settings table has MIDTRANS_SERVER_KEY entry

### Check File System
- [ ] File exists: `app/Models/Setting.php`
- [ ] File exists: `app/Services/MidtransService.php`
- [ ] File exists: `app/Http/Controllers/PaymentController.php`
- [ ] File exists: `database/migrations/2025_02_10_000001_create_settings_table.php`

### Check Configuration
- [ ] File: `config/services.php` has midtrans section
- [ ] File: `routes/web.php` has payment routes
- [ ] File: `app/Http/Controllers/OrderController.php` has updated methods
- [ ] File: `resources/views/pages/order-success.blade.php` has Midtrans script

### Check Logs
```bash
# Check for errors:
tail -f storage/logs/laravel.log
```
- [ ] No  errors when creating orders
- [ ] No errors when generating tokens
- [ ] No PaymentController errors
- [ ] No MidtransService initialization errors

## 📊 Admin Dashboard Verification

### Orders Page
- [ ] Orders list visible
- [ ] New orders appear with timestamps
- [ ] Payment status column shows values:
  - [ ] "unpaid" for cash orders
  - [ ] "pending" for online orders
  - [ ] Can filter by payment status
- [ ] Click order to see details

### Order Detail Page
- [ ] Order information displays
- [ ] Customer name and phone visible
- [ ] Payment status displayed
- [ ] Payment method visible
- [ ] Order items listed with prices
- [ ] Total amount displayed
- [ ] Payment information section present

## 🚀 Advanced Verification (Optional)

### Test Webhook Receipt (if publicly accessible)
- [ ] Webhook URL: `http://your-domain/midtrans/webhook`
- [ ] Webhook is accessible (not 404)
- [ ] Midtrans can POST to it (test in Midtrans dashboard)
- [ ] Check logs for webhook notifications

### Test with Sandbox API
```php
// In tinker:
use App\Services\MidtransService;
MidtransService::init();
$status = MidtransService::getStatus('ORDER-NO-HERE');
echo $status->transaction_status;
```
- [ ] Returns valid transaction status
- [ ] No errors initializing service
- [ ] Can query Midtrans API

### Test Configuration Load
```bash
php artisan config:cache
php artisan config:clear
```
- [ ] No errors during cache operations
- [ ] Configuration still works after cache clear
- [ ] `.env` values properly loaded

## ⚠️ Common Issues to Check

- [ ] Server Key not accidentally exposed in frontend code
- [ ] Client Key visible only in frontend
- [ ] Payment button appears/disappears based on payment method
- [ ] Cash orders don't show payment button
- [ ] Online orders show payment button
- [ ] Phone number field is optional (not all orders need it)
- [ ] Order creation succeeds even if payment token fails
- [ ] Error messages are user-friendly (no PHP errors shown)

## 🔐 Security Checks

- [ ] Server Key never logged to console/frontend
- [ ] Client Key only in frontend JavaScript
- [ ] HTTPS ready (configured properly)
- [ ] Webhook signature verified (code present)
- [ ] Transaction status always verified with Midtrans
- [ ] Customer phone stored (for payment notifications)
- [ ] All payments logged for audit

## 📋 Documentation Review

- [ ] `MIDTRANS_QUICK_START.md` - Read and understood
- [ ] `MIDTRANS_SETUP.md` - Bookmarked for reference
- [ ] `IMPLEMENTATION_SUMMARY.md` - Reviewed overview
- [ ] Code comments in PaymentController understood
- [ ] MidtransService methods understood

## 🎯 Production Readiness (When Ready)

### Before Going to Production:
- [ ] Thoroughly tested all payment flows
- [ ] No unhandled errors in logs
- [ ] Webhook receiving and processing correctly
- [ ] Database backups configured
- [ ] Got production keys from Midtrans (different from sandbox)

### Production Configuration:
- [ ] Updated MIDTRANS_SERVER_KEY with production key
- [ ] Updated MIDTRANS_CLIENT_KEY with production key
- [ ] Set MIDTRANS_IS_PRODUCTION=true
- [ ] Checked "Mode Produksi" in admin settings
- [ ] Cleared all config caches
- [ ] First test with small amount transactions
- [ ] Monitored for 24hours+ successfully
- [ ] Set up payment alerts in Midtrans dashboard

## 📞 Support Resources

If you encounter issues:

1. **Check Quick Start Guide**
   - File: `MIDTRANS_QUICK_START.md`
   - Covers 80% of common issues

2. **Check Detailed Setup**
   - File: `MIDTRANS_SETUP.md`
   - In-depth documentation

3. **Review Implementation**
   - File: `IMPLEMENTATION_SUMMARY.md`
   - Technical overview

4. **Check Application Logs**
   - File: `storage/logs/laravel.log`
   - Real error messages

5. **Midtrans Resources**
   - Dashboard: https://dashboard.sandbox.midtrans.com
   - Docs: https://docs.midtrans.com
   - Support: Available in dashboard

## ✅ Final Completion

- [ ] All checklist items completed
- [ ] System is ready for payment processing
- [ ] User training completed (if applicable)
- [ ] Monitoring set up for production
- [ ] Backup and recovery plan in place
- [ ] Support documentation shared with team

---

## Sign-Off

**Completed By**: _________________  
**Date**: _________________  
**System Status**: Ready for Production ✅

---

## Notes

Use this space to add any custom notes or modifications made to the standard setup:

```
[Add notes here]
```

---

**For any issues not covered in these guides, contact Midtrans support or review application logs.**

Last Updated: February 10, 2025
