<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        @media print {
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #C8A17D;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #C8A17D;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .print-btn {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-btn button {
            background-color: #C8A17D;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #C8A17D;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-processing { background-color: #e2d4f5; color: #5a1f7c; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
            border-top: 2px solid #C8A17D;
        }
    </style>
</head>
<body>
    <div class="print-btn no-print">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>

    <div class="header">
        <h1>Orders Report</h1>
        <p>Generated on {{ date('F d, Y H:i:s') }}</p>
        <p>Total Orders: {{ $orders->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Table</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @php $totalAmount = 0; @endphp
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->customer_name }}</td>
                <td>
                    @if($order->table_number && $order->order_type === 'dine_in')
                        Table {{ $order->table_number }}
                    @else
                        Takeaway
                    @endif
                </td>
                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td>
                    <span class="status status-{{ $order->status }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
            </tr>
            @php $totalAmount += $order->total_amount; @endphp
            @endforeach
            <tr class="total-row">
                <td colspan="3">TOTAL</td>
                <td colspan="3">Rp {{ number_format($totalAmount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>&copy; {{ date('Y') }} Cafe Management System. All rights reserved.</p>
    </div>

    <script>
        // Auto-print when opened (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
