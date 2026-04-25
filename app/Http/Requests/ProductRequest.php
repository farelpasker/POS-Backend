<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            "name" => ['required','string','max:255','unique:products,name,'. $this->route('product')],
            "category_id" => ['required','exists:categories,id'],
            "price" => ['required', 'integer', 'min:0'],
            "thumbnail" => [$this->isMethod('POST') ? 'required' : 'nullable','image','mimes:jpeg,jpg,png','max:2048'],
            "about" => ['required','string'],
            "is_popular" => ['boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The product name is required.',
            'name.unique' => 'The product name has already been taken.',
            'category_id.required' => 'The product category is required.',
            'category_id.exists' => 'The selected category does not exist.',
            'price.required' => 'The product price is required.',
            'price.integer' => 'The product price must be an integer.',
            'price.min' => 'The product price must be at least 0.',
            'thumbnail.required' => 'The product thumbnail is required.',
            'thumbnail.image' => 'The product thumbnail must be an image.',
            'thumbnail.max' => 'The product thumbnail must not exceed 2MB in size.',
            'about.required' => 'The product description is required.',
            'is_popular.boolean' => 'The product popularity field must be true or false.',
        ];
    }
}
