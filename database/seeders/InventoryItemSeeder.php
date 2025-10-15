<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryItem;

class InventoryItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Item A', 'sku' => 'SKU-A', 'price' => 100, 'description' => 'Sample item A'],
            ['name' => 'Item B', 'sku' => 'SKU-B', 'price' => 250, 'description' => 'Sample item B'],
            ['name' => 'Item C', 'sku' => 'SKU-C', 'price' => 500, 'description' => 'Sample item C'],
        ];

        foreach ($items as $item) {
            InventoryItem::updateOrCreate(['sku' => $item['sku']], $item);
        }

        $this->command->info('✅ Inventory items seeded successfully!');
    }
}
