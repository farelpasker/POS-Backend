<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MerchantRequest extends FormRequest
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
            "name"=> "required|string|max:255|unique:merchants,name," . $this->route('merchant'),
            "address"=> "required|string|max:500",
            "photo"=> [$this->isMethod('POST') ? 'required' : 'nullable','image','mimes:jpeg,png,jpg','max:2048'],
            'phone' => 'required|string|max:13',
            "keeper_id"=> "required|exists:users,id",
        ];
    }

    public function messages(){
        return [
            'name.required' => 'Merchant name is required',
            'name.unique' => 'Merchant name already exists',
            'address.required' => 'Merchant address is required',
            'photo.required' => 'Merchant photo is required',
            'photo.image' => 'Merchant photo must be an image',
            'photo.mimes' => 'Merchant photo must be a valid image format',
            'photo.max' => 'Merchant photo must not exceed 2MB',
            'phone.required' => 'Merchant phone is required',
            'phone.max' => 'Merchant phone must not exceed 13 characters',
            'keeper_id.required' => 'Merchant keeper is required',
            'keeper_id.exists' => 'Merchant keeper does not exist',
        ];
    }
}
