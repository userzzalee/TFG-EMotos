<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'cantidad' => 'required|integer|min:1|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists' => 'El producto no existe.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.integer' => 'La cantidad debe ser un número entero.',
            'cantidad.min' => 'La cantidad mínima es 1.',
            'cantidad.max' => 'La cantidad máxima es 10.',
        ];
    }
}
