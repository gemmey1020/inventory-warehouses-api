<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\{Warehouse, InventoryItem, Stock, User};
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DomainException;

class StockServiceTest extends TestCase
{

    use RefreshDatabase;

    protected StockService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // استخدم كل الـ seeders اللي كتبناها
        $this->service = app(StockService::class);
    }

    #[Test]
    public function it_throws_exception_if_transfer_quantity_exceeds_available_stock()
    {
        $from = Warehouse::first();
        $to   = Warehouse::skip(1)->first();
        $item = InventoryItem::first();
        $user = User::first();

        // خزن كمية محدودة
        $stock = Stock::where('warehouse_id', $from->id)
            ->where('inventory_item_id', $item->id)
            ->first();
        $stock->update(['quantity' => 5]);

        $this->expectException(DomainException::class);
        $this->expectExceptionMessage('Insufficient stock for transfer.');

        $this->service->transfer(
            fromWarehouseId: $from->id,
            toWarehouseId: $to->id,
            inventoryItemId: $item->id,
            quantity: 50,
            createdBy: $user->id
        );
    }
}
