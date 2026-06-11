<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinalizarCitaRequest extends FormRequest
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
            'coste'               => 'required|numeric|min:0',
            'comentario_mecanico' => 'nullable|string|max:2000',
        ];
    }
}
