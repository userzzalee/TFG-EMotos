<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnuncioRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cualquier usuario autenticado puede publicar (la ruta ya exige auth).
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'precio'      => 'required|numeric|min:0',
            'categoria'   => 'nullable|string|max:100',
            'estado'      => 'required|in:nuevo,bueno,usado,para-piezas',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
        ];
    }
}
