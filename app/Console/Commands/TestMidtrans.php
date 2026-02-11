<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Midtrans\Config;
use Midtrans\Snap;

class TestMidtrans extends Command
{
    protected $signature = 'midtrans:test';
    protected $description = 'Test Midtrans configuration and token generation';

    public function handle()
    {
        $this->info('=== MIDTRANS CONFIGURATION TEST ===');
        $this->newLine();

        // 1. Check .env values
        $this->info('1. Checking .env configuration...');
        $merchantId = env('MIDTRANS_MERCHANT_ID');
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $clientKey = env('MIDTRANS_CLIENT_KEY');
        $isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        $this->table(
            ['Config Key', 'Value', 'Status'],
            [
                ['MIDTRANS_MERCHANT_ID', $merchantId ?: 'NULL', $merchantId ? '✅' : '❌'],
                ['MIDTRANS_SERVER_KEY', $serverKey ? substr($serverKey, 0, 20) . '...' : 'NULL', $serverKey ? '✅' : '❌'],
                ['MIDTRANS_CLIENT_KEY', $clientKey ? substr($clientKey, 0, 20) . '...' : 'NULL', $clientKey ? '✅' : '❌'],
                ['MIDTRANS_IS_PRODUCTION', $isProduction ? 'true' : 'false', '✅'],
            ]
        );
        $this->newLine();

        // 2. Check config file
        $this->info('2. Checking config/midtrans.php...');
        $configServerKey = config('midtrans.server_key');
        $configClientKey = config('midtrans.client_key');
        
        $this->table(
            ['Config Method', 'Value', 'Status'],
            [
                ['config("midtrans.server_key")', $configServerKey ? substr($configServerKey, 0, 20) . '...' : 'NULL', $configServerKey ? '✅' : '❌'],
                ['config("midtrans.client_key")', $configClientKey ? substr($configClientKey, 0, 20) . '...' : 'NULL', $configClientKey ? '✅' : '❌'],
            ]
        );
        $this->newLine();

        if (!$serverKey || !$clientKey) {
            $this->error('❌ Midtrans configuration is incomplete!');
            $this->warn('Please check your .env file and ensure MIDTRANS_SERVER_KEY and MIDTRANS_CLIENT_KEY are set.');
            return 1;
        }

        // 3. Test token generation
        $this->info('3. Testing Snap token generation...');
        
        try {
            // Configure Midtrans
            Config::$serverKey = $serverKey;
            Config::$clientKey = $clientKey;
            Config::$isProduction = $isProduction;
            Config::$isSanitized = true;
            Config::$is3ds = true;
            
            // DEVELOPMENT ONLY: Disable SSL verification for sandbox
            if (!$isProduction) {
                // Set curlOptions with HTTPHEADER to prevent undefined array key error
                Config::$curlOptions = array(
                    CURLOPT_HTTPHEADER => array(),
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0
                );
                $this->warn('⚠️  SSL verification disabled (sandbox mode)');
            }

            // Test transaction params
            $params = [
                'transaction_details' => [
                    'order_id' => 'TEST-' . time(),
                    'gross_amount' => 50000,
                ],
                'customer_details' => [
                    'first_name' => 'Test Customer',
                    'phone' => '081234567890',
                ],
                'item_details' => [
                    [
                        'id' => 1,
                        'price' => 50000,
                        'quantity' => 1,
                        'name' => 'Test Item',
                    ]
                ],
            ];

            $this->warn('Calling Midtrans API...');
            $snapToken = Snap::getSnapToken($params);

            $this->newLine();
            $this->info('✅ SUCCESS! Snap token generated:');
            $this->line('   Token: ' . substr($snapToken, 0, 30) . '...');
            $this->line('   Length: ' . strlen($snapToken) . ' characters');
            $this->newLine();
            
            $this->info('🎉 Midtrans integration is working correctly!');
            return 0;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Token generation failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            
            if (strpos($e->getMessage(), 'SSL') !== false || strpos($e->getMessage(), 'cURL') !== false) {
                $this->warn('💡 This is a SSL/cURL issue. Common on Windows.');
                $this->warn('   The implementation uses Snap.js (frontend) to avoid this.');
                $this->warn('   Token generation happens once during checkout.');
            }
            
            return 1;
        }
    }
}
