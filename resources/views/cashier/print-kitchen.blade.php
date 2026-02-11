<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen Order - {{ $order->order_number }}</title>
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
            font-size: 12px;
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
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 2px;
        }
        
        .header .subtitle {
            font-size: 11px;
            margin-top: 3px;
        }
        
        .order-info {
            margin-bottom: 15px;
            font-size: 13px;
            font-weight: bold;
        }
        
        .order-info div {
            margin-bottom: 3px;
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
            margin: 15px 0;
        }
        
        .item {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dotted #ccc;
        }
        
        .item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .item-options {
            font-size: 11px;
            margin-left: 10px;
            line-height: 1.6;
        }
        
        .item-notes {
            font-size: 11px;
            margin-left: 10px;
            margin-top: 4px;
            font-style: italic;
            background: #f0f0f0;
            padding: 4px 6px;
            border-left: 3px solid #000;
        }
        
        .status-badge {
            text-align: center;
            margin: 15px 0;
            padding: 8px;
            background: #000;
            color: #fff;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 10px;
        }
        
        .time {
            font-size: 11px;
            text-align: right;
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
        <div class="subtitle">KITCHEN ORDER</div>
    </div>
    
    <div class="order-info">
        <div>ORDER: {{ $order->order_number }}</div>
        <div>
            @if($order->order_type === 'dine_in')
                TABLE: {{ $order->table_number }}
            @else
                TAKEAWAY
            @endif
        </div>
        <div class="time">{{ $order->created_at->format('d M Y, H:i') }}</div>
    </div>
    
    <div class="thick-divider"></div>
    
    <div class="items">
        @foreach($order->items as $item)
        <div class="item">
            <div class="item-name">{{ $item->quantity }}x {{ $item->menu_name ?? $item->menu->name }}</div>
            
            @if($item->notes)
            <div class="item-options">
                @php
                    // Parse notes to extract options
                    $notes = $item->notes;
                    $lines = explode(',', $notes);
                @endphp
                @foreach($lines as $line)
                    <div>- {{ trim($line) }}</div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    <div class="thick-divider"></div>
    
    <div class="status-badge">
        STATUS: {{ strtoupper($order->payment_status) }}
    </div>
    
    <div class="thick-divider"></div>
    
    <div class="footer">
        <div>Dicetak: {{ now()->format('d M Y, H:i:s') }}</div>
        <div style="margin-top: 5px;">Terima Kasih!</div>
    </div>
    
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Print Sekarang
    </button>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            // Give time for the page to render
            setTimeout(function() {
                // Uncomment to enable auto-print
                // window.print();
            }, 500);
        };
        
        // Close window after print
        window.onafterprint = function() {
            // Uncomment to auto-close after print
            // window.close();
        };
    </script>
</body>
</html>
