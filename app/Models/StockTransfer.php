<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_warehouse_id',
        'to_warehouse_id',
        'inventory_item_id',
        'quantity',
        'transferred_at',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'transferred_at' => 'datetime',
    ];

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
// RELATIONSHIPS: fromWarehouse, toWarehouse, item, creator TO GET RELATED DATA about
// ( who + transfare what + from where + to where )
