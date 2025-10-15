<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location'];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function inventoryItems()
    {
        // Pivot عبر جدول stocks مع quantity
        return $this->belongsToMany(InventoryItem::class, 'stocks')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
