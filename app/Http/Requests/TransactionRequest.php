<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'merchant_id' => 'required|exists:merchants,id',
            'products'=> 'required|array|min:1', //Product must be array,
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'=> 'Nama wajib diisi',
            'name.string'=> 'Nama wajib string',
            'name.max'=> 'Nama max 255 karakter',
            'phone.required'=> 'phone wajib diisi',
            'phone.string' => 'phone wajib berbentuk string',
            'phone.max' => 'phone max 255 karakter',
            'merchant_id.required' => 'merchant wajib diisi',
            'merchant_id.exists' => 'merchant ini belum terdaftar',
            'products.required' => 'product wajib untuk diisi',
            'products.array' => 'product berbentuk array',
            'products.min' => 'minimal product 1',
            'products.*.product_id.required' => 'product wajib diisi',
            'products.*.product_id.exists' => 'product tidak ada di database',
            'products.*.quantity.required' => 'quantity wajib diisi',
            'products.*.quantity.integer' => 'quantity harus diisi integer',
            'products.*.quantity.min' => 'quantity min 1',
        ];
    }
}
