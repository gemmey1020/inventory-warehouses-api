<?php

namespace App\Services;

use App\Events\LowStockDetected;
use App\Models\InventoryItem;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\Warehouse;
use Illuminate\Database\DatabaseManager as DBAL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use DomainException;

class StockService
{
    public function __construct(private DBAL $db) {}

    /**
     *  transfer stock from warehouse A to warehouse B for a specific item
     */
    public function transfer(
        int $fromWarehouseId,
        int $toWarehouseId,
        int $inventoryItemId,
        int $quantity,
        ?int $createdBy = null
    ): StockTransfer {
        if ($fromWarehouseId === $toWarehouseId) {
            throw new InvalidArgumentException('Source and destination warehouses must differ.');
        }
        if ($quantity < 1) {
            throw new InvalidArgumentException('Quantity must be >= 1.');
        }

        // تأكيد وجود الكيانات (يرمي ModelNotFoundException لو مش موجود)
        Warehouse::findOrFail($fromWarehouseId);
        Warehouse::findOrFail($toWarehouseId);
        InventoryItem::findOrFail($inventoryItemId);

        return $this->db->transaction(function () use (
            $fromWarehouseId,
            $toWarehouseId,
            $inventoryItemId,
            $quantity,
            $createdBy
        ) {
            // قفل الصفوف لمنع الـ Race Conditions
            $fromStock = Stock::where('warehouse_id', $fromWarehouseId)
                ->where('inventory_item_id', $inventoryItemId)
                ->lockForUpdate()
                ->first();

            if (!$fromStock || $fromStock->quantity < $quantity) {
                throw new DomainException('Insufficient stock for transfer.');
            }

            $toStock = Stock::where('warehouse_id', $toWarehouseId)
                ->where('inventory_item_id', $inventoryItemId)
                ->lockForUpdate()
                ->first();

            if (!$toStock) {
                $toStock = new Stock([
                    'warehouse_id'      => $toWarehouseId,
                    'inventory_item_id' => $inventoryItemId,
                    'quantity'          => 0,
                ]);
            }

            // التحديث الذرّي
            $fromStock->decrement('quantity', $quantity);
            $toStock->quantity += $quantity;
            $toStock->save();

            // سجّل عملية النقل
            $transfer = StockTransfer::create([
                'from_warehouse_id' => $fromWarehouseId,
                'to_warehouse_id'   => $toWarehouseId,
                'inventory_item_id' => $inventoryItemId,
                'quantity'          => $quantity,
                'transferred_at'    => now(),
                'created_by'        => $createdBy,
            ]);

            // Threshold قابل للتهيئة
            $threshold = (int) config('inventory.low_stock_threshold', 10);
            if ($fromStock->quantity < $threshold) {
                LowStockDetected::dispatch($fromStock->fresh(['warehouse', 'item']), $threshold);
            }

            // إبطال الكاش الخاص بالمخازن المتأثرة
            $this->invalidateWarehouseInventoryCache($fromWarehouseId);
            $this->invalidateWarehouseInventoryCache($toWarehouseId);

            return $transfer;
        });
    }

    /**
     * قراءة مخزون مخزن (مع فلترة وكاش)
     */
    public function getWarehouseInventory(
        int $warehouseId,
        array $filters = [],
        int $perPage = 20
    ) {
        $ttl   = (int) config('inventory.warehouse_inventory_ttl', 60);
        $key   = $this->cacheKey($warehouseId, $filters, $perPage);

        // لو بتستخدم Redis وعايز Tags: تقدر تبدّل لـ Cache::tags(['warehouse:'.$warehouseId])->remember(...)
        return Cache::remember($key, $ttl, function () use ($warehouseId, $filters, $perPage) {
            $q = Stock::query()
                ->with(['item'])
                ->where('warehouse_id', $warehouseId);

            if (!empty($filters['name'])) {
                $q->whereHas(
                    'item',
                    fn($qq) =>
                    $qq->where('name', 'like', '%' . $filters['name'] . '%')
                );
            }
            if (!empty($filters['sku'])) {
                $q->whereHas(
                    'item',
                    fn($qq) =>
                    $qq->where('sku', 'like', '%' . $filters['sku'] . '%')
                );
            }
            if (!empty($filters['price_min'])) {
                $q->whereHas(
                    'item',
                    fn($qq) =>
                    $qq->where('price', '>=', (float) $filters['price_min'])
                );
            }
            if (!empty($filters['price_max'])) {
                $q->whereHas(
                    'item',
                    fn($qq) =>
                    $qq->where('price', '<=', (float) $filters['price_max'])
                );
            }

            return $q->orderByDesc('quantity')->paginate($perPage);
        });
    }

    private function cacheKey(int $warehouseId, array $filters, int $perPage): string
    {
        return sprintf(
            'warehouse:%d:inventory:%s:p%d',
            $warehouseId,
            md5(json_encode($filters)),
            $perPage
        );
    }

    private function invalidateWarehouseInventoryCache(int $warehouseId): void
    {
        // بدون Tags: امسح مفاتيح متوقعة (بسيط). في الإنتاج يفضّل Tags مع Redis/Memcached.
        // هنا هنختار مسار بسيط: نمسح كل المفاتيح المطابقة بنمط معروف (لو عندك driver يدعم ذلك).
        // بديل عملي: زد "version" في مفتاح الكاش (key versioning) وتخزنه في Cache منفصل.

        // أبسط حل هنا: نرفع "nonce" في نسخة المفتاح
        Cache::increment("warehouse:{$warehouseId}:kver", 1);
    }
}
