<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComentarCitaRequest extends FormRequest
{
    /**
     * Autoriza vía CitaTallerPolicy::atender (mecánico asignado a la cita).
     */
    public function authorize(): bool
    {
        return $this->user()?->can('atender', $this->route('cita')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'comentario_mecanico' => 'required|string|max:2000',
        ];
    }
}
