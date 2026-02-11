# Midtrans Payment System Architecture

## System Architecture Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                     WEB CAFFEE PAYMENT SYSTEM                       │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐
│  Customer    │
│  Browser     │
└──────┬───────┘
       │
       │ 1. Add Items & Checkout
       ↓
┌──────────────────────────────────────────────────────────┐
│                    FRONTEND VIEWS                         │
├──────────────────────────────────────────────────────────┤
│  • cart.blade.php                                        │
│    - Customer name input                                 │
│    - Phone number input                                  │
│    - Payment method selection (Cash/QRIS)                │
│    - Submit to /checkout route                           │
│                                                          │
│  • order-success.blade.php                               │
│    - Shows order confirmation                            │
│    - If online payment: Shows Midtrans Snap button       │
│    - If cash: Shows "Pay at Counter" message             │
│    - Includes Snap.js script from CDN                    │
└──────┬───────────────────────────────────────────────────┘
       │
       │ 2. Submit Checkout
       ↓
┌──────────────────────────────────────────────────────────┐
│                   LARAVEL CONTROLLER                      │
├──────────────────────────────────────────────────────────┤
│  OrderController::checkout()                             │
│  ├─ Validate input                                       │
│  ├─ Create Order record                                  │
│  │  └─ Set payment_method ('cash' or 'qris')             │
│  │  └─ Set payment_status ('unpaid' or 'pending')        │
│  ├─ Create OrderItems                                    │
│  ├─ Create Payment record                                │
│  └─ Redirect to order.success                            │
│                                                          │
│  OrderController::success()                              │
│  ├─ Fetch Order with relationships                       │
│  ├─ If payment_method != 'cash':                         │
│  │  ├─ Call PaymentController::processPayment()          │
│  │  ├─ Generate Midtrans Snap token                      │
│  │  └─ Pass token to view                                │
│  └─ Return success view                                  │
└──────┬───────────────────────────────────────────────────┘
       │
       │ 3. Generate Payment Token
       ↓
┌──────────────────────────────────────────────────────────┐
│              PAYMENT CONTROLLER                           │
├──────────────────────────────────────────────────────────┤
│  PaymentController::processPayment()                      │
│  ├─ Call MidtransService::init()                         │
│  ├─ Prepare transaction params (Order details)           │
│  ├─ Call MidtransService::createSnapToken()              │
│  ├─ Store token in Payment record                        │
│  └─ Return token to view                                 │
└──────┬───────────────────────────────────────────────────┘
       │
       │ 4. Initialize Midtrans
       ↓
┌──────────────────────────────────────────────────────────┐
│              MIDTRANS SERVICE                             │
├──────────────────────────────────────────────────────────┤
│  MidtransService::init()                                 │
│  ├─ Load Server Key from env                             │
│  ├─ Load Client Key from env                             │
│  ├─ Load Production flag from env                        │
│  └─ Configure Midtrans SDK                               │
│                                                          │
│  MidtransService::preparePaymentParams()                 │
│  ├─ Extract Order total amount                           │
│  ├─ Extract Order items details                          │
│  ├─ Add customer name & phone                            │
│  ├─ Add callback URLs                                    │
│  └─ Return formatted params                              │
│                                                          │
│  MidtransService::createSnapToken()                      │
│  └─ Call Snap::getSnapToken() → Returns token            │
└──────┬────────────────────────────────┬───────────────────┘
       │                                │
       │ Token Generated               │ Config Loaded
       ↓                                ↓
  ┌──────────────┐           ┌──────────────────┐
  │ Browser gets │           │ .env configuration
  │ Snap Token   │           │ - Server Key      │
  └──────┬───────┘           │ - Client Key      │
         │                   │ - Production flag │
         │                   └──────────────────┘
         │ 5. Display Payment Button
         ↓
    ┌─────────────────────────────────────────┐
    │  Frontend: Show Midtrans Snap Button     │
    │  "Bayar dengan QRIS/Kartu"               │
    └──────┬──────────────────────────────────┘
           │
           │ 6. User Clicks Pay Button
           ↓
    ┌──────────────────────────────────────────┐
    │      Midtrans Snap Popup Opens           │
    │   (Loaded from CDN via Client Key)       │
    │                                          │
    │  ┌────────────────────────────────────┐  │
    │  │ Payment Methods:                    │  │
    │  │ • QRIS Code (Scan with phone)       │  │
    │  │ • Debit Card (Visa, Mastercard)     │  │
    │  │ • Credit Card                        │  │
    │  │ • Other e-wallets                    │  │
    │  └────────────────────────────────────┘  │
    └──────┬──────────────────────────────────┘
           │
           │ 7. User Completes Payment
           │    (Midtrans processes)
           ↓
    ┌──────────────────────────────────────────┐
    │        MIDTRANS SERVER                    │
    │   (Processes payment securely)            │
    │                                          │
    │  ├─ Verify payment method                 │
    │  ├─ Process transaction                   │
    │  ├─ Verify 3DS (if applicable)            │
    │  └─ Return status to user & server        │
    └──────┬───────────────────┬───────────────┘
           │                   │
           │ JavaScript        │ JSON Webhook
           │ Callback          │ Notification
           │                   │
           ↓                   ↓
    ┌────────────┐     ┌──────────────────────┐
    │  Frontend  │     │  PAYMENT WEBHOOK      │
    │  Callback  │     │                       │
    │  Handlers  │     │  POST /midtrans/webhook
    └────────────┘     │                       │
                       │  Payload:             │
                       │  - order_id           │
                       │  - transaction_id     │
                       │  - transaction_status │
                       │  - payment_type       │
                       │  - fraud_status       │
                       └──────┬────────────────┘
                              │
                              │ 8. Webhook Handler
                              ↓
                    ┌──────────────────────────┐
                    │ PaymentController::      │
                    │ webhook()                │
                    │                          │
                    │ ├─ Verify signature      │
                    │ ├─ Get notification      │
                    │ ├─ Find Order            │
                    │ ├─ Check status:         │
                    │ │  ├─ settlement →       │
                    │ │  │  Mark paid          │
                    │ │  │  Update order       │
                    │ │  │                     │
                    │ │  ├─ pending →          │
                    │ │  │  Keep pending       │
                    │ │  │                     │
                    │ │  └─ denied →           │
                    │ │     Mark failed        │
                    │ │                        │
                    │ ├─ Store Midtrans       │
                    │ │  response              │
                    │ └─ Log transaction      │
                    └──────┬───────────────────┘
                           │
                           │ 9. Update Database
                           ↓
                ┌──────────────────────────────┐
                │    DATABASE UPDATES           │
                ├──────────────────────────────┤
                │ payments table:               │
                │ • status: 'paid'              │
                │ • paid_at: timestamp          │
                │ • midtrans_order_id:  trans_id│
                │ • midtrans_response: json     │
                │                              │
                │ orders table:                 │
                │ • payment_status: 'paid'      │
                │ • status: 'confirmed'         │
                └──────┬───────────────────────┘
                       │
                       │ 10. Order To Kitchen
                       ↓
                ┌──────────────────────────────┐
                │      KITCHEN DISPLAY         │
                │                              │
                │  Order appears automatically │
                │  for preparation             │
                └──────────────────────────────┘
```

## Data Flow - Technical Details

```
CONFIGURATION
──────────────────────────────────────────────────────
admin/payment-settings [Form]
    ↓
AdminController::updatePaymentSettings()
    ├─ Validate input
    ├─ Update .env file
    │  ├─ MIDTRANS_SERVER_KEY
    │  ├─ MIDTRANS_CLIENT_KEY
    │  └─ MIDTRANS_IS_PRODUCTION
    ├─ Run config:cache
    └─ Return success message


PAYMENT INITIATION
──────────────────────────────────────────────────────
POST /checkout [Form Submission]
    ├─ Validate payment_method in ['cash', 'qris']
    ├─ Create Order
    │  ├─ order_number (auto-generated)
    │  ├─ customer_name
    │  ├─ customer_phone
    │  ├─ payment_method
    │  ├─ payment_status (unpaid/pending)
    │  └─ total_amount
    │
    ├─ Create OrderItems (loop through cart)
    │  ├─ menu_id
    │  ├─ quantity
    │  ├─ unit_price
    │  └─ subtotal
    │
    ├─ Create Payment
    │  ├─ order_id
    │  ├─ method
    │  ├─ amount
    │  └─ status: 'pending'
    │
    └─ Redirect to order.success


PAYMENT TOKEN GENERATION
──────────────────────────────────────────────────────
GET /order/{orderNumber}/success
    ↓
OrderController::success()
    ├─ Fetch Order with items
    ├─ If payment_method != 'cash':
    │  ├─ Call PaymentController::processPayment()
    │  │  ├─ Load config via MidtransService::init()
    │  │  ├─ Prepare params:
    │  │  │  ├─ transaction_details (order_id, gross_amount)
    │  │  │  ├─ customer_details (name, phone)
    │  │  │  ├─ item_details (array of items)
    │  │  │  └─ callbacks (finish, error, pending)
    │  │  │
    │  │  └─ Call Snap::getSnapToken()
    │  │     └─ API Call to Midtrans
    │  │        ├─ POST to https://app.sandbox.midtrans.com/snap/v1/transactions
    │  │        ├─ Body: json encoded params
    │  │        ├─ Auth: Server Key in header
    │  │        └─ Returns: { token: "..." }
    │  │
    │  ├─ Store token in Payment.midtrans_transaction_id
    │  └─ Return token to view
    │
    └─ Render success view with token or error


FRONTEND PAYMENT POPUP
──────────────────────────────────────────────────────
Browser receives snap_token and client_key
    ├─ Load Snap.js from CDN
    │  └─ <script src="https://app.sandbox.midtrans.com/snap/snap.js"
    │       data-client-key="{{ client_key }}"></script>
    │
    ├─ On Button Click:
    │  └─ snap.pay(token, {
    │     ├─ onSuccess: handle success
    │     ├─ onError: handle error
    │     ├─ onPending: handle pending
    │     └─ onClose: handle close
    │     })
    │
    ├─ Midtrans Popup Opens
    │  ├─ Shows payment methods
    │  ├─ User selects method
    │  └─ User completes transaction
    │
    └─ Callback executed based on outcome


PAYMENT PROCESSING (Midtrans)
──────────────────────────────────────────────────────
User submits payment → Midtrans Server
    ├─ Validates card/method
    ├─ Processes transaction
    ├─ Handles 3DS authentication (if needed)
    │
    ├─ If Successful:
    │  └─ transaction_status = 'settlement' or 'capture'
    │
    ├─ If Pending:
    │  └─ transaction_status = 'pending'
    │
    └─ If Failed:
       └─ transaction_status = 'denial'


WEBHOOK NOTIFICATION
──────────────────────────────────────────────────────
Midtrans Server → Your Server
POST /midtrans/webhook
Body:
{
  "order_id": "ORDER-123",
  "transaction_id": "12345678",
  "transaction_status": "settlement",
  "payment_type": "qris",
  "fraud_status": "accept",
  "status_code": "200",
  ...
}

PaymentController::webhook()
    ├─ Verify authenticity
    ├─ Parse notification
    ├─ Find Order by order_id
    ├─ Check transaction_status:
    │  ├─ settlement/capture:
    │  │  ├─ Payment::markAsPaid()
    │  │  │  ├─ status = 'paid'
    │  │  │  ├─ paid_at = now()
    │  │  │  └─ save()
    │  │  │
    │  │  └─ Order::update()
    │  │     ├─ payment_status = 'paid'
    │  │     ├─ status = 'confirmed'
    │  │     └─ save()
    │  │
    │  ├─ pending:
    │  │  └─ Payment::update(['status' => 'pending'])
    │  │
    │  └─ denied/failed:
    │     └─ Payment::markAsFailed()
    │
    ├─ Store full response
    ├─ Log transaction
    └─ Return 200 OK


STATUS CHECKING (Optional Polling)
──────────────────────────────────────────────────────
Frontend can poll: GET /payment/{order}/status
    ↓
PaymentController::getStatus()
    ├─ Call MidtransService::getStatus(orderNumber)
    │  └─ Transaction::status() → API call to Midtrans
    │
    └─ Return JSON:
       {
         status: "settlement|pending|denial",
         payment_status: "paid|pending|failed",
         fraud_status: "accept|challenge|deny"
       }
```

## Database Schema Relationships

```
orders
├── id (PK)
├── order_number (UNIQUE)
├── customer_name
├── customer_phone
├── payment_method ← NEW
├── payment_status ← NEW
├── total_amount
└── ... other fields

       ↓ (1:1 relationship)
       
payments
├── id (PK)
├── order_id (FK)
├── method
├── status
├── amount
├── midtrans_transaction_id
├── midtrans_order_id
├── midtrans_response (JSON)
├── paid_at
└── ... timestamps

order_items
├── id (PK)
├── order_id (FK)
├── menu_id (FK)
├── quantity
├── unit_price
└── subtotal

       ↓ (FK)
       
menu (existing)
├── id (PK)
├── name
├── price
└── ...

settings (NEW)
├── id (PK)
├── key (UNIQUE)
├── value (JSON)
└── timestamps
```

## Environment Variables Flow

```
.env file
├── MIDTRANS_SERVER_KEY (secret, server-side only)
├── MIDTRANS_CLIENT_KEY (public, frontend)
├── MIDTRANS_IS_PRODUCTION (flag)
├── MIDTRANS_IS_SANITIZED (flag)
└── MIDTRANS_IS_3DS (flag)
    ↓
config/services.php (loaded by Laravel)
    ↓
    ├─→ Backend: Config::$serverKey
    │   └─→ PaymentController uses for API calls
    │
    └─→ Frontend: config('services.midtrans.client_key')
        └─→ View uses for Snap.js initialization
```

## Error Handling Flow

```
                    Error Occurs
                        ↓
    ┌───────────────────────────────────────┐
    │  Exception/Invalid State                │
    └───────┬──────────────────────────────┘
            ↓
    ┌───────────────────────────────────────┐
    │ Where?                                  │
    ├───────────────────────────────────────┤
    │  1. Missing config                      │
    │     └─→ Check Payment Settings          │
    │                                        │
    │  2. Snap token generation fails         │
    │     └─→ Log error, show user message    │
    │                                        │
    │  3. Webhook fails                       │
    │     └─→ Log error, retry on next check  │
    │                                        │
    │  4. Database error                      │
    │     └─→ Rollback transaction            │
    │     └─→ Show error message              │
    └───────┬──────────────────────────────┘
            ↓
    All errors logged to:
    storage/logs/laravel.log
```

---

This architecture ensures:
✅ Secure payment processing
✅ Proper data flow and state management
✅ Error handling and logging
✅ Webhook verification
✅ Transaction audit trail
