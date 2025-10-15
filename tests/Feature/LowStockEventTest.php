<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\{Warehouse, InventoryItem, Stock, User};
use App\Events\LowStockDetected;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

class LowStockEventTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_fires_low_stock_event_when_quantity_drops_below_threshold()
    {
        $this->seed();

        Event::fake();

        $user = User::first();
        $from = Warehouse::first();
        $to   = Warehouse::skip(1)->first();
        $item = InventoryItem::first();

        // نجهز المخزون بكمية صغيرة
        $stock = Stock::where('warehouse_id', $from->id)
            ->where('inventory_item_id', $item->id)
            ->first();
        $stock->update(['quantity' => 8]); // أقل من threshold (10)

        $payload = [
            'from_warehouse_id' => $from->id,
            'to_warehouse_id'   => $to->id,
            'inventory_item_id' => $item->id,
            'quantity'          => 3,
        ];

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/stock-transfers', $payload)
            ->assertStatus(201);

        Event::assertDispatched(LowStockDetected::class);
    }
}
