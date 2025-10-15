<?php

return [
    // if low stock detected for any item in any warehouse
    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 10),

    // if single warehouse inventory cache duration (seconds)
    'warehouse_inventory_ttl' => env('WAREHOUSE_INV_TTL', 60),
];
