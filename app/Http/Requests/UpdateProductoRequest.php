<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->esAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre'      => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio'      => 'required|numeric|min:0',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock'       => 'required|integer|min:0',
            'categoria'   => 'nullable|string|max:100',
        ];
    }
}
