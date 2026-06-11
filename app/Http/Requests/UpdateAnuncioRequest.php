<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnuncioRequest extends FormRequest
{
    /**
     * Autoriza usando la AnuncioPolicy: solo el dueño del anuncio.
     * El anuncio llega por route-model binding ({anuncio}).
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('anuncio')) ?? false;
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
            'vendido'     => 'boolean',
            'imagen'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:3072',
        ];
    }
}
