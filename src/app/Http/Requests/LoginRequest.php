<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|email|max:100',
            'password' => 'required|string|max:16',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email adresi zorunludur.',
            'email.email' => 'Geçerli bir email adresi giriniz.',
            'email.max' => 'Email adresi en fazla 100 karakter olabilir.',
            'password.required' => 'Şifre zorunludur.',
            'password.string' => 'Şifre metin tipinde olmalıdır.',
            'password.max' => 'Şifre en fazla 16 karakter olabilir.',
        ];
    }
}
