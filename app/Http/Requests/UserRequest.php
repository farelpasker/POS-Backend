<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => ['required','string','max:255'],
            'email' => ['required','string','email','max:255','unique:users,email,' . $this->route('user')],
            'password' => $this->isMethod('POST') ? ['required','string','min:8'] : ['nullable','string','min:8'],
            'photo' => ['nullable','image','mimes:jpeg,png,jpg,gif','max:2048'],
            'phone' => ['nullable','string','max:15']

        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email must be a valid email address.',
            'email.unique' => 'The email has already been taken.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters long.',
            'photo.image' => 'The photo must be an image.',
            'photo.mimes' => 'The photo must be a file of type: jpeg,png,jpg,gif.',
            'photo.max' => 'The photo may not be greater than 2048 kilobytes.',
            'phone.string' => 'The phone must be a string.',
            'phone.max' => 'The phone may not be greater than 15 characters.',
        ];
    }
}
