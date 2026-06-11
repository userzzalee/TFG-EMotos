<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => 'required|string|min:10|max:255',
            'shipping_city' => 'required|string|min:2|max:100',
            'shipping_postal_code' => 'required|string|regex:/^[0-9]{5}$/',
            'shipping_phone' => 'required|string|regex:/^(\+34|0034)?[6-9][0-9]{8}$/',
            'payment_method' => 'required|string|in:card,cash',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'La dirección es obligatoria',
            'shipping_address.min' => 'La dirección debe tener al menos 10 caracteres',
            'shipping_address.max' => 'La dirección no puede exceder 255 caracteres',
            'shipping_city.required' => 'La ciudad es obligatoria',
            'shipping_city.min' => 'La ciudad debe tener al menos 2 caracteres',
            'shipping_city.max' => 'La ciudad no puede exceder 100 caracteres',
            'shipping_postal_code.required' => 'El código postal es obligatorio',
            'shipping_postal_code.regex' => 'El código postal debe tener 5 dígitos (ej: 28001)',
            'shipping_phone.required' => 'El teléfono es obligatorio',
            'shipping_phone.regex' => 'El teléfono debe tener formato español (ej: +34 600 000 000 o 600000000)',
            'payment_method.required' => 'El método de pago es obligatorio',
            'payment_method.in' => 'El método de pago seleccionado no es válido',
        ];
    }
}
