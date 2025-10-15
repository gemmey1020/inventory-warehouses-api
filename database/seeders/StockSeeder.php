<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Warehouse;
use App\Models\InventoryItem;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $items = InventoryItem::all();

        if ($warehouses->isEmpty() || $items->isEmpty()) {
            $this->command->warn('⚠️ Seed warehouses and items first.');
            return;
        }

        foreach ($warehouses as $warehouse) {
            foreach ($items as $item) {
                Stock::updateOrCreate(
                    [
                        'warehouse_id' => $warehouse->id,
                        'inventory_item_id' => $item->id,
                    ],
                    [
                        'quantity' => rand(10, 100),
                    ]
                );
            }
        }

        $this->command->info('✅ Stocks seeded successfully!');
    }
}
