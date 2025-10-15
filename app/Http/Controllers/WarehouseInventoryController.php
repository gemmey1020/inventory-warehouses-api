<?php

namespace App\Http\Controllers;

use App\Http\Resources\StockResource;
use App\Models\Warehouse;
use App\Services\StockService;

class WarehouseInventoryController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function show(Warehouse $warehouse)
    {
        $filters = request()->only(['name', 'sku', 'price_min', 'price_max']);
        $perPage = (int) request('per_page', 20);

        $stocks = $this->stockService->getWarehouseInventory($warehouse->id, $filters, $perPage)
            ->through(fn($stock) => new StockResource($stock->loadMissing('item')));

        return $stocks;
    }
}
