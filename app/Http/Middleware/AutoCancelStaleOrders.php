<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * AutoCancelStaleOrders
 *
 * Secara otomatis mengubah status pesanan yang sudah melebihi 24 jam
 * dan masih berstatus aktif (pending / waiting / processing / preparing)
 * menjadi 'cancelled'.
 *
 * Dijalankan pada setiap request yang sudah terautentikasi.
 * Menggunakan throttle cache 5 menit agar tidak membebani DB di setiap request.
 */
class AutoCancelStaleOrders
{
    // Status yang dianggap "aktif" (belum selesai / belum dibatalkan)
    private const STALE_STATUSES = [
        'pending',
        'waiting_payment',
        'waiting_cashier_confirmation',
        'processing',
        'preparing',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        // Throttle: hanya jalankan sekali per 5 menit untuk seluruh app
        // sehingga tidak ada overhead DB pada setiap request
        $cacheKey = 'auto_cancel_stale_orders_ran';

        if (! Cache::has($cacheKey)) {
            $this->cancelStaleOrders();
            // Simpan flag selama 5 menit (300 detik)
            Cache::put($cacheKey, true, now()->addMinutes(5));
        }

        return $next($request);
    }

    private function cancelStaleOrders(): void
    {
        try {
            // Cutoff: pesanan yang dibuat lebih dari 24 jam lalu
            $cutoff = now()->subHours(24);

            // Bulk update — satu query saja, sangat ringan
            DB::table('orders')
                ->whereIn('status', self::STALE_STATUSES)
                ->where('created_at', '<', $cutoff)
                ->update([
                    'status'         => 'cancelled',
                    'payment_status' => DB::raw(
                        // Biarkan payment_status 'paid' tetap 'paid', sisanya jadi 'cancelled'
                        "CASE WHEN payment_status = 'paid' THEN 'paid' ELSE 'cancelled' END"
                    ),
                    'updated_at'     => now(),
                ]);
        } catch (\Throwable $e) {
            // Jangan sampai error di sini mengganggu request utama
            \Illuminate\Support\Facades\Log::warning('AutoCancelStaleOrders failed: ' . $e->getMessage());
        }
    }
}
