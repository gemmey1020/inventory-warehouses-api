<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['sometimes', 'string', 'max:255'],
            'sku'       => ['sometimes', 'string', 'max:255'],
            'price_min' => ['sometimes', 'numeric', 'gte:0'],
            'price_max' => ['sometimes', 'numeric', 'gte:price_min'],
            'per_page'  => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
