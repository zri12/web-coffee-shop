<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PaymentController extends Controller
{
    /**
     * Process online payment (QRIS/Card)
     */
    public function processPayment(Order $order)
    {
        try {
            // Check if Midtrans is configured
            if (!MidtransService::isConfigured()) {
                throw new \Exception('Konfigurasi Midtrans belum lengkap. Silakan setting Server Key dan Client Key di Payment Settings.');
            }

            // Get or create payment record
            $payment = $order->payment ?? Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'method' => 'qris',
                'status' => 'pending',
            ]);

            // Prepare Midtrans parameters
            $params = MidtransService::preparePaymentParams($order);

            // Generate Snap token
            $snapToken = MidtransService::createSnapToken($params);

            // Store snap token in payment record
            $payment->update([
                'midtrans_transaction_id' => $snapToken,
            ]);

            return [
                'success' => true,
                'snap_token' => $snapToken,
                'client_key' => MidtransService::getClientKey(),
            ];

        } catch (\Exception $e) {
            \Log::error('Midtrans Payment Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle payment success callback
     */
    public function handleSuccess(Request $request)
    {
        try {
            MidtransService::init();

            $orderId = $request->input('order_id');
            $order = Order::where('order_number', $orderId)->firstOrFail();
            $payment = $order->payment;

            // Get transaction status from Midtrans
            $status = MidtransService::getStatus($orderId);

            if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                // Payment successful
                $payment->update([
                    'status' => 'paid',
                    'midtrans_order_id' => $status->transaction_id,
                    'midtrans_response' => (array)$status,
                    'paid_at' => now(),
                ]);

                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'paid',
                ]);

                return redirect()->route('order.success', $order->order_number)
                    ->with('success', 'Pembayaran berhasil diproses!');
            }

            return redirect()->route('order.success', $order->order_number)
                ->with('warning', 'Status pembayaran masih menunggu');

        } catch (\Exception $e) {
            \Log::error('Payment Success Handler Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Handle payment error
     */
    public function handleError(Request $request)
    {
        $orderId = $request->input('order_id');
        
        return redirect()->route('cart')
            ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
    }

    /**
     * Handle payment pending
     */
    public function handlePending(Request $request)
    {
        $orderId = $request->input('order_id');
        
        return redirect()->back()
            ->with('info', 'Pembayaran sedang diproses. Silakan tunggu...');
    }

    /**
     * Webhook handler for Midtrans notifications
     */
    public function webhook(Request $request)
    {
        try {
            MidtransService::init();

            // Get notification data
            $notif = new \Midtrans\Notification();

            $orderId = $notif->order_id;
            $statusCode = $notif->status_code;
            $paymentType = $notif->payment_type;
            $fraud = $notif->fraud_status;

            // Get order
            $order = Order::where('order_number', $orderId)->first();
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Order not found'], 404);
            }

            $payment = $order->payment;

            $transactionStatus = strtolower((string)($notif->transaction_status ?? ''));

            // Handle based on Midtrans transaction status
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                if ($fraud === 'challenge') {
                    $payment->update(['status' => 'pending']);
                    $order->update(['payment_status' => 'pending']);
                } else {
                    $payment->markAsPaid();
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'paid',
                    ]);
                    
                    // NEW: Deduct stock after payment confirmed
                    try {
                        app(\App\Services\StockService::class)->deductStockForOrder($order);
                        \Log::info('✅ Stock deducted for order ' . $order->order_number);
                    } catch (\Exception $e) {
                        \Log::error('❌ Stock deduction failed for order ' . $order->order_number . ': ' . $e->getMessage());
                        // Don't fail payment, but log for manual review
                    }
                }
            } elseif (in_array($transactionStatus, ['pending'])) {
                $payment->update(['status' => 'pending']);
                $order->update(['payment_status' => 'pending', 'status' => 'waiting_payment']);
            } else {
                // deny/expire/cancel/failure
                $payment->markAsFailed();
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'waiting_payment',
                ]);
            }

            // Store Midtrans response
            $payment->update([
                'midtrans_order_id' => $notif->transaction_id,
                'midtrans_response' => $request->all(),
            ]);

            \Log::info('Midtrans Webhook - Order: ' . $orderId . ' Status: ' . $notif->transaction_status);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('Midtrans Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get payment status
     */
    public function getStatus(Order $order)
    {
        try {
            MidtransService::init();

            $payment = $order->payment;
            if (!$payment) {
                return response()->json(['status' => 'not_found']);
            }

            $status = MidtransService::getStatus($order->order_number);

            return response()->json([
                'status' => $status->transaction_status,
                'payment_status' => $payment->status,
                'fraud_status' => $status->fraud_status ?? null,
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Payment Status Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
