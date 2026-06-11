<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreValoracionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'puntuacion.required' => 'Debes seleccionar una puntuación.',
            'puntuacion.min'      => 'La puntuación mínima es 1 estrella.',
            'puntuacion.max'      => 'La puntuación máxima es 5 estrellas.',
        ];
    }
}
