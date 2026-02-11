<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Receipt - {{ $order->order_number }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            width: 280px;
            margin: 0 auto;
            padding: 10px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #000;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
            letter-spacing: 1px;
        }
        
        .header .subtitle {
            font-size: 10px;
            margin-top: 2px;
        }
        
        .header .address {
            font-size: 9px;
            margin-top: 4px;
            line-height: 1.3;
        }
        
        .order-info {
            margin-bottom: 12px;
            font-size: 11px;
        }
        
        .order-info div {
            margin-bottom: 2px;
            display: flex;
            justify-content: space-between;
        }
        
        .order-info .label {
            font-weight: normal;
        }
        
        .order-info .value {
            font-weight: bold;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .thick-divider {
            border-top: 2px solid #000;
            margin: 10px 0;
        }
        
        .items {
            margin: 12px 0;
        }
        
        .item {
            margin-bottom: 8px;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        
        .item-name {
            font-weight: bold;
        }
        
        .item-notes {
            font-size: 9px;
            margin-left: 12px;
            margin-top: 2px;
            color: #333;
            line-height: 1.5;
        }
        
        .totals {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px dashed #000;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
            font-size: 11px;
        }
        
        .total-row.grand {
            font-size: 14px;
            font-weight: bold;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 2px solid #000;
        }
        
        .payment-info {
            margin-top: 12px;
            padding: 8px;
            background: #f5f5f5;
            border: 1px solid #ddd;
        }
        
        .payment-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .payment-badge {
            display: inline-block;
            padding: 3px 8px;
            background: #000;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        
        .paid-badge {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            background: #2d5016;
            color: #fff;
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 9px;
            line-height: 1.5;
        }
        
        .footer .thank-you {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        @media print {
            body {
                width: 280px;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                margin: 0;
            }
        }
        
        .print-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #B8713E;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        
        .print-button:hover {
            background: #A05E35;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ strtoupper($systemSettings['cafe_name'] ?? 'Cafe') }}</h1>
        <div class="subtitle">Customer Receipt</div>
        <div class="address">
            {{ $systemSettings['address'] ?? '' }}<br>
            Tel: {{ $systemSettings['phone'] ?? '-' }}
        </div>
    </div>
    
    <div class="order-info">
        <div>
            <span class="label">Order Number:</span>
            <span class="value">{{ $order->order_number }}</span>
        </div>
        <div>
            <span class="label">Date/Time:</span>
            <span class="value">{{ $order->created_at->format('d M Y, H:i') }}</span>
        </div>
        @if($order->order_type === 'dine_in')
        <div>
            <span class="label">Table:</span>
            <span class="value">{{ $order->table_number }}</span>
        </div>
        @else
        <div>
            <span class="label">Type:</span>
            <span class="value">TAKEAWAY</span>
        </div>
        @endif
        @if($order->customer_name && $order->customer_name !== 'Guest')
        <div>
            <span class="label">Customer:</span>
            <span class="value">{{ $order->customer_name }}</span>
        </div>
        @endif
        <div>
            <span class="label">Cashier:</span>
            <span class="value">{{ auth()->user()->name ?? 'Staff' }}</span>
        </div>
    </div>
    
    <div class="thick-divider"></div>
    
    <div class="items">
        @foreach($order->items as $item)
        <div class="item">
            <div class="item-row">
                <div class="item-name">{{ $item->quantity }}x {{ $item->menu_name ?? $item->menu->name }}</div>
                <div>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
            </div>
            @if($item->unit_price != ($item->menu->price ?? 0))
            <div style="font-size: 9px; margin-left: 12px; color: #666;">
                @ Rp {{ number_format($item->unit_price, 0, ',', '.') }} each
            </div>
            @endif
            @if($item->notes)
            <div class="item-notes">
                @php
                    // Parse notes and make them readable
                    $notes = str_replace('|', ', ', $item->notes);
                @endphp
                {{ $notes }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    <div class="totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($order->total_amount / 1.05, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Tax (5%):</span>
            <span>Rp {{ number_format($order->total_amount - ($order->total_amount / 1.05), 0, ',', '.') }}</span>
        </div>
        <div class="total-row grand">
            <span>TOTAL:</span>
            <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>
    
    <div class="divider"></div>
    
    <div class="payment-info">
        <div>
            <span>Payment Method:</span>
            <span class="payment-badge">{{ strtoupper($order->payment_method) }}</span>
        </div>
        <div>
            <span>Status:</span>
            <span style="font-weight: bold; color: #2d5016;">{{ strtoupper($order->payment_status) }}</span>
        </div>
    </div>
    
    <div class="paid-badge">
        ✓ PAID - THANK YOU
    </div>
    
    <div class="footer">
        <div class="thank-you">Thank You!</div>
        <div>Please come again</div>
        <div>Follow us @cafearoma | cafearoma.id</div>
        <div style="margin-top: 8px;">
            This is an official receipt<br>
            No exchange/refund without receipt
        </div>
    </div>
    
    <button class="print-button no-print" onclick="window.print()">🖨️ Print Receipt</button>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            // Uncomment to enable auto-print
            // window.print();
        }
    </script>
</body>
</html>
