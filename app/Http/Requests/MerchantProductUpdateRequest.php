<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "warehouse_id" => ["required","integer","exists:warehouses,id"],
            "stock" => ["required","integer","min:0"]
        ];
    }

    public function messages(): array
    {
        return [
            "warehouse_id.required" => "Warehouse ID is required",
            "warehouse_id.integer" => "Warehouse ID must be an integer",
            "warehouse_id.exists" => "Warehouse ID does not exist",
            "stock.required" => "Stock is required",
            "stock.integer" => "Stock must be an integer",
            "stock.min" => "Stock must be at least 0",
        ];
    }
}
