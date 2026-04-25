<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehouseRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:warehouses,name,' . $this->route('warehouse'),
            'address' => 'required|string',
            'photo' => [$this->isMethod('POST') ? 'required' : 'nullable','image','mimes:jpeg,png,jpg','max:2048'],
            'phone' => 'required|string|max:255|unique:warehouses,phone,' . $this->route('warehouse'),
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Warehouse name is required',
            'name.unique' => 'Warehouse name already exists',
            'address.required' => 'Warehouse address is required',
            'photo.required' => 'Warehouse photo is required',
            'photo.image' => 'Warehouse photo must be an image',
            'photo.mimes' => 'Warehouse photo must be a valid image format',
            'photo.max' => 'Warehouse photo must not exceed 2MB',
            'phone.required' => 'Warehouse phone is required',
            'phone.unique' => 'Warehouse phone already exists',
        ];
    }
}
