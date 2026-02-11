<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Customer Name',
            'Table',
            'Total Amount',
            'Status',
            'Payment Method',
            'Payment Status',
            'Date',
        ];
    }

    public function map($order): array
    {
        $table = 'Takeaway';
        if ($order->table_number && $order->order_type === 'dine_in') {
            $table = 'Table ' . $order->table_number;
        }

        return [
            $order->order_number,
            $order->customer_name,
            $table,
            'Rp ' . number_format($order->total_amount, 0, ',', '.'),
            ucfirst($order->status),
            $order->payment ? ucfirst($order->payment->method) : '-',
            $order->payment ? ucfirst($order->payment->status) : '-',
            $order->created_at->format('M d, Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'C8A17D'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Orders Report';
    }
}
