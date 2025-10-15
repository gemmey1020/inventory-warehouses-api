<?php

namespace App\Events;

use App\Models\Stock;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LowStockDetected
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Stock $stock,
        public int $threshold
    ) {}
}
