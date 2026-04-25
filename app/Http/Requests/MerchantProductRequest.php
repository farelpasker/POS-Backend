<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantProductRequest extends FormRequest
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
            "product_id"=> ["required","exists:products,id"],
            "warehouse_id"=> ["required","exists:warehouses,id"],
            "stock"=> ["required","integer","min:1"],
        ];
    }

    public function messages(): array {
        return [
            "product_id.required"=> "Product ID is required",
            "product_id.exists"=> "Product ID does not exist",
            "warehouse_id.required"=> "Warehouse ID is required",
            "warehouse_id.exists"=> "Warehouse ID does not exist",
            "stock.required"=> "Stock is required",
            "stock.integer"=> "Stock must be an integer",
            "stock.min"=> "Stock must be at least 1",
        ];
    }
}
