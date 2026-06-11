<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modelo' => 'required|in:enduro,trail',
            'color' => 'required|in:gris,dorado,rojo',
            'motor' => 'required|in:40,80',
            'precio' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'modelo.required' => 'El modelo es obligatorio.',
            'modelo.in' => 'El modelo seleccionado no es válido.',
            'color.required' => 'El color es obligatorio.',
            'color.in' => 'El color seleccionado no es válido.',
            'motor.required' => 'El motor es obligatorio.',
            'motor.in' => 'El motor seleccionado no es válido.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
        ];
    }
}
