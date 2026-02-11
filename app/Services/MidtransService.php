<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class MidtransService
{
    /**
     * Initialize Midtrans with current configuration
     */
    public static function init()
    {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $clientKey = env('MIDTRANS_CLIENT_KEY');
        $isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        if (!$serverKey || !$clientKey) {
            throw new \Exception('Midtrans configuration incomplete. Please set MIDTRANS_SERVER_KEY and MIDTRANS_CLIENT_KEY in .env');
        }

        Config::$serverKey = $serverKey;
        Config::$clientKey = $clientKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create a Snap payment token
     * 
     * @param array $params Transaction parameters
     * @return string Snap token
     */
    public static function createSnapToken(array $params)
    {
        self::init();
        return Snap::getSnapToken($params);
    }

    /**
     * Get payment status
     * 
     * @param string $orderId Order ID from orders table
     * @return object Transaction status object
     */
    public static function getStatus($orderId)
    {
        self::init();
        return Transaction::status($orderId);
    }

    /**
     * Check if Midtrans is properly configured
     * 
     * @return bool
     */
    public static function isConfigured()
    {
        return !empty(env('MIDTRANS_MERCHANT_ID')) && !empty(env('MIDTRANS_SERVER_KEY')) && !empty(env('MIDTRANS_CLIENT_KEY'));
    }

    /**
     * Get merchant ID
     * 
     * @return string
     */
    public static function getMerchantId()
    {
        return env('MIDTRANS_MERCHANT_ID');
    }

    /**
     * Get client key for frontend
     * 
     * @return string
     */
    public static function getClientKey()
    {
        return env('MIDTRANS_CLIENT_KEY');
    }

    /**
     * Check if production mode
     * 
     * @return bool
     */
    public static function isProduction()
    {
        return env('MIDTRANS_IS_PRODUCTION', false);
    }

    /**
     * Get Snap JS URL based on mode
     * 
     * @return string
     */
    public static function getSnapScriptUrl()
    {
        $mode = self::isProduction() ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
        return $mode . '/snap/snap.js';
    }

    /**
     * Prepare payment parameters for order
     * 
     * @param \App\Models\Order $order
     * @return array
     */
    public static function preparePaymentParams($order)
    {
        $items = [];
        foreach ($order->items as $item) {
            $items[] = [
                'id' => (string)$item->menu_id,
                'price' => (int)$item->unit_price,
                'quantity' => $item->quantity,
                'name' => substr($item->menu_name ?? $item->menu->name, 0, 50),
            ];
        }

        return [
            'transaction_details' => [
                'order_id' => $order->order_number,
                'gross_amount' => (int)$order->total_amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_phone ?? '',
            ],
            'item_details' => $items,
            'callbacks' => [
                'finish' => route('payment.success'),
                'error' => route('payment.error'),
                'pending' => route('payment.pending'),
            ],
        ];
    }
}
