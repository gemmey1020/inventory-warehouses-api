<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockTransferRequest;
use App\Http\Resources\StockTransferResource;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;

class StockTransferController extends Controller
{
    public function __construct(private StockService $stockService) {}

    public function store(StoreStockTransferRequest $request): JsonResponse
    {
        // dd("in");
        $transfer = $this->stockService->transfer(
            fromWarehouseId: (int) $request->input('from_warehouse_id'),
            toWarehouseId: (int) $request->input('to_warehouse_id'),
            inventoryItemId: (int) $request->input('inventory_item_id'),
            quantity: (int) $request->input('quantity'),
            createdBy: optional($request->user())->id
        );

        return response()->json([
            'success' => true,
            'data'    => new StockTransferResource($transfer),
            'message' => 'Stock transferred successfully.',
        ], 201);
    }
}
