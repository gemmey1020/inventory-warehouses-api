<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;

use Tests\TestCase;
use App\Models\{Warehouse, InventoryItem, Stock, User};
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockTransferFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_transfers_stock_successfully_and_updates_quantities()
    {
        $this->seed();

        $user = User::first();
        $from = Warehouse::first();
        $to   = Warehouse::skip(1)->first();
        $item = InventoryItem::first();

        // الكمية الأولية
        $fromStock = Stock::where('warehouse_id', $from->id)
            ->where('inventory_item_id', $item->id)
            ->first();
        $fromQtyBefore = $fromStock->quantity;

        $payload = [
            'from_warehouse_id' => $from->id,
            'to_warehouse_id'   => $to->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 5,
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/stock-transfers', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'from_warehouse_id',
                    'to_warehouse_id',
                    'inventory_item_id',
                    'quantity',
                    'transferred_at',
                ],
                'message',
            ]);

        // تحقق من تحديث الكميات
        $fromStock->refresh();
        $toStock = Stock::where('warehouse_id', $to->id)
            ->where('inventory_item_id', $item->id)
            ->first();

        $this->assertEquals($fromQtyBefore - 5, $fromStock->quantity);
        $this->assertEquals($toStock->quantity >= 5, true);
    }
}
