<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
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
            'product_id' => 'required|string',
            'quantity' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'product_id.required' => 'Ürün UUID zorunludur.',
            'quantity.required' => 'Miktar zorunludur.',
            'quantity.numeric' => 'Miktar numerik olmalıdır.',
        ];
    }
}
