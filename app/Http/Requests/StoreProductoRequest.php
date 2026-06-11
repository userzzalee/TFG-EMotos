<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Doble seguridad: además del middleware 'admin' en la ruta.
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
            'imagen'      => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock'       => 'required|integer|min:0',
            'categoria'   => 'nullable|string|max:100',
        ];
    }
}
