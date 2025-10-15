<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockTransfer;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\InventoryItem;

class StockTransferSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $from = Warehouse::first();
        $to = Warehouse::skip(1)->first();
        $item = InventoryItem::first();

        if (!$from || !$to || !$item) {
            $this->command->warn('⚠️ Missing base data, seed warehouses/items first.');
            return;
        }

        StockTransfer::updateOrCreate([
            'from_warehouse_id' => $from->id,
            'to_warehouse_id'   => $to->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 5,
            'transferred_at'    => now(),
            'created_by'        => $user?->id,
        ]);

        $this->command->info('✅ StockTransfer seeded successfully!');
    }
}
