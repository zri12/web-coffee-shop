# Midtrans Payment Integration Guide

## Overview
This document explains how to set up and use the Midtrans payment integration for the Web Caffee system to process both QR code (QRIS) and direct web card payments.

## Prerequisites
- Composer (included in the project)
- Midtrans PHP SDK (already installed)
- Midtrans account (https://midtrans.com)

## Setup Steps

### 1. Get Your Midtrans Keys
1. Sign up at https://dashboard.midtrans.com
2. Create a new Merchant account
3. Go to Settings → Access Keys
4. Copy your **Server Key** and **Client Key**
   - **Server Key**: Used for server-side transactions (keep it secret)
   - **Client Key**: Used for client-side payment UI (can be public)

### 2. Configure Environment Variables
Add the following to your `.env` file:

```env
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
```

**Notes:**
- Replace the keys with your actual Midtrans keys
- For sandbox testing, use the provided sandbox keys
- Set `MIDTRANS_IS_PRODUCTION=true` when ready for production
- Keep the server key secret and never commit it to version control

### 3. Configure via Admin Panel
1. Login to the admin dashboard
2. Navigate to **Payment Settings**
3. Enter your Midtrans Server Key and Client Key
4. Check "Mode Produksi" if using production keys
5. Click "Simpan Pengaturan"

The system will automatically update your `.env` file.

### 4. Run Database Migrations
```bash
php artisan migrate
```

This creates the `settings` table to store payment configurations.

## How It Works

### Payment Flow

#### For Web Customers (Direct Purchase):
1. Customer adds items to cart
2. Customer chooses payment method:
   - **Cash**: Pay at counter
   - **QRIS/Card**: Pay online via Midtrans
3. Customer completes checkout
4. If online payment:
   - System creates QRIS code via Midtrans
   - Customer scans code or enters card details
   - Payment gateway processes transaction
   - System receives webhook notification
5. Order status updates automatically

#### For QR Table Orders:
1. Customer scans table QR code
2. Adds items from menu
3. Chooses payment method
4. Completes checkout
5. If online payment:
   - System generates Midtrans Snap token
   - Customer completes payment
   - Payment confirmed, order goes to kitchen

### Database Tables

**orders** table fields:
- `payment_method`: 'cash', 'qris', 'card'
- `payment_status`: 'unpaid', 'pending', 'paid', 'failed'

**payments** table:
- `method`: Payment method used
- `status`: 'pending', 'paid', 'failed', 'challenge'
- `midtrans_transaction_id`: Snap token
- `midtrans_order_id`: Transaction ID from Midtrans
- `midtrans_response`: Full response from Midtrans API
- `paid_at`: Payment completion timestamp

## Files Created/Modified

### New Files:
- `app/Models/Setting.php` - Settings model for storing config
- `app/Http/Controllers/PaymentController.php` - Payment processing logic
- `database/migrations/2025_02_10_000001_create_settings_table.php` - Settings table

### Modified Files:
- `config/services.php` - Added Midtrans configuration
- `routes/web.php` - Added payment routes and webhook endpoint
- `app/Http/Controllers/OrderController.php` - Updated checkout to handle online payments
- `app/Http/Controllers/Dashboard/DashboardController.php` - Enhanced payment settings
- `resources/views/admin/payment-settings.blade.php` - Updated form with actual config
- `resources/views/pages/order-success.blade.php` - Added Midtrans Snap payment UI

## API Endpoints

### Payment Processing
- **POST** `/payment/{order}/process` - Generate payment token
- **GET** `/payment/{order}/status` - Check payment status
- **GET** `/payment/success` - Payment success callback
- **GET** `/payment/error` - Payment error callback
- **GET** `/payment/pending` - Payment pending callback

### Webhook
- **POST** `/midtrans/webhook` - Midtrans transaction notifications

## Testing

### Sandbox Testing
1. Keep `MIDTRANS_IS_PRODUCTION=false` in .env
2. Use sandbox Server Key and Client Key
3. Test with card numbers:
   - Visa: `4811111111111114`
   - MasterCard: `5105105105105100`
   - Expiry: Any future date
   - CVV: Any 3 digits

### Testing Payment Status
```bash
# Check a specific order's payment status
curl -X GET "http://localhost:8000/payment/ORDER_ID/status"
```

## Monitoring Payments

### Admin Dashboard
- Go to **Orders** section
- Filter by Payment Status (Paid, Unpaid, Pending)
- View payment details in order detail page
- See payment method and transaction ID

### Midtrans Dashboard
1. Login to https://dashboard.midtrans.com
2. Go to **Transactions**
3. Search for order by Order ID
4. View transaction status and details

## Troubleshooting

### Issue: "Midtrans configuration is incomplete"
**Solution:** 
- Check that both `MIDTRANS_SERVER_KEY` and `MIDTRANS_CLIENT_KEY` are set in .env
- Clear config cache: `php artisan config:cache`

### Issue: Payment button doesn't appear
**Solution:**
- Check browser console for errors
- Verify `MIDTRANS_CLIENT_KEY` is correctly configured
- Check that Snap script is loading from CDN

### Issue: Webhook not received
**Solution:**
- Verify webhook URL is accessible from internet
- Check Midtrans webhook logs in dashboard
- Ensure `MIDTRANS_SERVER_KEY` is correct for webhook verification

### Issue: Payment status not updating
**Solution:**
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database connection
- Check if webhook payload is being received

## Security Considerations

1. **Never commit sensitive keys** to version control
2. **Use environment variables** for all production keys
3. **Enable 3DS** for credit card security (already default)
4. **Verify webhook signatures** (implemented in PaymentController)
5. **Use HTTPS** in production (required by Midtrans)
6. **Log all transactions** for audit trail

## API Documentation

### PaymentController Methods

**processPayment($order)**
- Generates initial transaction token
- Creates/updates payment record
- Returns snap token for checkout

**handleSuccess(Request $request)**
- Callback after successful payment
- Updates payment status to 'paid'
- Redirects to order success page

**handleError(Request $request)**
- Callback for payment errors
- Logs error details
- Redirects to cart for retry

**webhook(Request $request)**
- Receives Midtrans notifications
- Updates payment records
- Confirms orders automatically
- Handles fraud challenges

**getStatus(Order $order)**
- Returns current payment status
- Used for polling payment updates
- JSON response

## Future Enhancements

- [ ] Multiple currency support
- [ ] Payment installment plans
- [ ] Recurring payments for subscriptions
- [ ] Payment history and reporting
- [ ] Manual payment entry (for manual verification)
- [ ] Refund processing interface
- [ ] Multi-gateway support (Stripe, PayPal)

## Support

For issues with:
- **Midtrans integration**: Check Midtrans documentation at https://docs.midtrans.com
- **Laravel setup**: Refer to Laravel documentation
- **System issues**: Check application logs in `storage/logs/`

## References

- Midtrans Documentation: https://docs.midtrans.com
- Sandbox Dashboard: https://dashboard.sandbox.midtrans.com
- Production Dashboard: https://dashboard.midtrans.com
