<?php

namespace App\Listeners;

use App\Events\LowStockDetected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotifyAdminOnLowStock implements ShouldQueue
{
    public function handle(LowStockDetected $event): void
    {
        $stock = $event->stock->load(['warehouse', 'item']);
        Log::warning('Low stock detected', [
            'warehouse' => $stock->warehouse?->name,
            'item'      => $stock->item?->sku,
            'quantity'  => $stock->quantity,
            'threshold' => $event->threshold,
        ]);

        // fire notification or mail to admin
        // Mail::to(config('mail.from.address'))->queue(new LowStockMail($stock, $event->threshold));
    }
}
