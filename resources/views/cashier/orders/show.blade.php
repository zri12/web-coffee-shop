<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700&display=swap" rel="stylesheet"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Courier New', monospace;
            background: #f5f5f5;
            padding: 20px;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .receipt-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .header {
            text-align: center;
            border-bottom: 2px dashed #ddd;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 900;
            margin-bottom: 4px;
            color: #d47311;
        }
        
        .header p {
            font-size: 12px;
            color: #666;
        }
        
        .order-info {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eee;
        }
        
        .order-number {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .order-date {
            font-size: 11px;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 8px;
        }
        
        .status-pending { background: #fff3e0; color: #e65100; }
        .status-processing { background: #e3f2fd; color: #1565c0; }
        .status-completed { background: #e8f5e9; color: #2e7d32; }
        .status-cancelled { background: #ffebee; color: #c62828; }
        
        .section {
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #eee;
        }
        
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #d47311;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        .info-label {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #333;
        }
        
        .item {
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #eee;
        }
        
        .item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 4px;
        }
        
        .item-name {
            font-weight: 700;
            font-size: 14px;
            color: #333;
            flex: 1;
        }
        
        .item-price {
            font-weight: 700;
            font-size: 14px;
            color: #333;
            white-space: nowrap;
            margin-left: 12px;
        }
        
        .item-detail {
            font-size: 11px;
            color: #666;
        }
        
        .item-note {
            font-size: 11px;
            color: #999;
            font-style: italic;
            margin-top: 4px;
            padding: 4px 8px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        
        .total-section {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 2px solid #333;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .total-label {
            font-size: 16px;
            font-weight: 700;
            color: #333;
        }
        
        .total-amount {
            font-size: 22px;
            font-weight: 900;
            color: #d47311;
        }
        
        .footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 2px dashed #ddd;
        }
        
        .actions {
            display: flex;
            gap: 8px;
            margin-top: 24px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
        }
        
        .btn-print {
            background: #d47311;
            color: white;
        }
        
        .btn-print:hover {
            background: #b85f0e;
        }
        
        .btn-back {
            background: #f5f5f5;
            color: #333;
        }
        
        .btn-back:hover {
            background: #e0e0e0;
        }
        
        .material-symbols-outlined {
            font-size: 18px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt-container {
                max-width: 80mm;
                width: 80mm;
                box-shadow: none;
                padding: 8px;
                margin: 0;
            }
            
            .actions {
                display: none !important;
            }
            
            .status-badge {
                border: 1px solid currentColor;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .section {
                page-break-inside: avoid;
            }
            
            /* Ensure colors print */
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        @page {
            size: 80mm auto;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $systemSettings['cafe_name'] ?? 'Cafe' }}</h1>
            <p>Order Receipt</p>
        </div>

        <!-- Order Info -->
        <div class="order-info">
            <div class="order-number">{{ $order->order_number }}</div>
            <div class="order-date">{{ $order->created_at->format('M d, Y • h:i A') }}</div>
            <span class="status-badge status-{{ $order->status }}">
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <!-- Customer Information -->
        <div class="section">
            <div class="section-title">Customer Information</div>
            <div class="info-grid">
                <div>
                    <div class="info-label">Customer Name</div>
                    <div class="info-value">{{ $order->customer_name }}</div>
                </div>
                <div>
                    <div class="info-label">Order Type</div>
                    <div class="info-value">
                        @if($order->order_type === 'dine_in')
                            Table {{ $order->table_number }}
                        @else
                            Takeaway
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="section">
            <div class="section-title">Order Items</div>
            @foreach($order->items as $item)
            <div class="item">
                <div class="item-header">
                    <div class="item-name">{{ $item->menu_name ?? $item->menu->name ?? 'Menu Item' }}</div>
                    <div class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                </div>
                <div class="item-detail">{{ $item->quantity }}x @ Rp {{ number_format($item->unit_price, 0, ',', '.') }}</div>
                @if($item->notes)
                <div class="item-note">Note: {{ $item->notes }}</div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Total Amount -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Total Amount</div>
                <div class="total-amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Payment Information -->
        @if($order->payment)
        <div class="section" style="border-bottom: none;">
            <div class="section-title">Payment Information</div>
            <div class="info-grid">
                <div>
                    <div class="info-label">Method</div>
                    <div class="info-value">{{ strtoupper($order->payment->method) }}</div>
                </div>
                <div>
                    <div class="info-label">Status</div>
                    <div class="info-value">{{ strtoupper($order->payment->status) }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <p style="font-size: 12px; color: #999;">Thank you for your order!</p>
            <p style="font-size: 11px; color: #ccc; margin-top: 4px;">{{ $systemSettings['cafe_name'] ?? 'Cafe' }} - Your Coffee Destination</p>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button onclick="window.print()" class="btn btn-print">
                <span class="material-symbols-outlined">print</span>
                Print Receipt
            </button>
            <a href="{{ route('cashier.incoming-orders') }}" class="btn btn-back">
                <span class="material-symbols-outlined">arrow_back</span>
                Back
            </a>
        </div>
    </div>
</body>
</html>
