<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'warehouse_id' => $this->warehouse_id,
            'item' => new InventoryItemResource($this->whenLoaded('item')),
            'quantity' => (int) $this->quantity,
            'updated_at' => $this->updated_at,
        ];
    }
}
