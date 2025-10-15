<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryIndexRequest;
use App\Http\Resources\InventoryItemResource;
use App\Models\InventoryItem;

class InventoryController extends Controller
{
    public function index(InventoryIndexRequest $request)
    {
        $q = InventoryItem::query();

        if ($name = $request->string('name')->toString()) {
            $q->where('name', 'like', "%{$name}%");
        }
        if ($sku = $request->string('sku')->toString()) {
            $q->where('sku', 'like', "%{$sku}%");
        }
        if ($request->filled('price_min')) {
            $q->where('price', '>=', (float) $request->input('price_min'));
        }
        if ($request->filled('price_max')) {
            $q->where('price', '<=', (float) $request->input('price_max'));
        }

        $perPage = (int) $request->input('per_page', 20);
        $items   = $q->orderBy('name')->paginate($perPage);

        return InventoryItemResource::collection($items);
    }
}
