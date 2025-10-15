<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Warehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Riyadh Main', 'location' => 'Riyadh'],
            ['name' => 'Jeddah Hub', 'location' => 'Jeddah'],
            ['name' => 'Dammam Center', 'location' => 'Dammam'],
        ];

        foreach ($warehouses as $data) {
            Warehouse::updateOrCreate(['name' => $data['name']], $data);
        }

        $this->command->info('✅ Warehouses seeded successfully!');
    }
}
