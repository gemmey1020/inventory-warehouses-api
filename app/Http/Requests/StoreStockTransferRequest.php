<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // هنعمل Authorization بالـ Gate/Policy لاحقًا
    }

    public function rules(): array
    {
        return [
            'from_warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'to_warehouse_id'   => ['required', 'integer', 'different:from_warehouse_id', 'exists:warehouses,id'],
            'inventory_item_id' => ['required', 'integer', 'exists:inventory_items,id'],
            'quantity'          => ['required', 'integer', 'min:1'],
        ];
    }
}
