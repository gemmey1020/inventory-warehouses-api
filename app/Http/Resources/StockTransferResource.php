<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'from_warehouse_id' => $this->from_warehouse_id,
            'to_warehouse_id'   => $this->to_warehouse_id,
            'inventory_item_id' => $this->inventory_item_id,
            'quantity'          => (int) $this->quantity,
            'transferred_at'    => $this->transferred_at?->toISOString(),
            'created_by'        => $this->created_by,
        ];
    }
}
