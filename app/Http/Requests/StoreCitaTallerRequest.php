<?php

namespace App\Http\Requests;

use App\Support\AgendaTaller;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreCitaTallerRequest extends FormRequest
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
            'marca'       => 'required|string|min:2|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s\-]+$/',
            'modelo'      => 'required|string|min:2|max:100',
            'matricula'   => 'required|string|max:20|regex:/^[0-9]{4}\s?[A-Za-z]{3}$/',
            'fecha_cita'  => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (! AgendaTaller::esSlotValido(Carbon::parse($value))) {
                        $fail('La fecha y hora seleccionadas no están disponibles. Elige otro hueco.');
                    }
                },
            ],
            'problema'    => 'required|string|min:10|max:2000',
            'comentarios' => 'nullable|string|max:1000',
            'fotos'       => 'nullable|array|max:5',
            'fotos.*'     => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }

    public function messages(): array
    {
        return [
            'marca.required'    => 'La marca es obligatoria.',
            'marca.min'         => 'La marca debe tener al menos 2 caracteres.',
            'marca.max'         => 'La marca no puede exceder 100 caracteres.',
            'marca.regex'       => 'La marca solo puede contener letras.',
            'modelo.required'   => 'El modelo es obligatorio.',
            'modelo.min'        => 'El modelo debe tener al menos 2 caracteres.',
            'modelo.max'        => 'El modelo no puede exceder 100 caracteres.',
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.regex'   => 'La matrícula debe tener formato español (ej: 1234 ABC).',
            'fecha_cita.required' => 'Debes seleccionar una fecha y hora para la cita.',
            'fecha_cita.date'   => 'La fecha seleccionada no es válida.',
            'problema.required' => 'La descripción del problema es obligatoria.',
            'problema.min'      => 'La descripción debe tener al menos 10 caracteres.',
            'problema.max'      => 'La descripción no puede exceder 2000 caracteres.',
            'comentarios.max'   => 'Los comentarios no pueden exceder 1000 caracteres.',
            'fotos.max'         => 'Solo puedes subir un máximo de 5 fotos.',
            'fotos.*.image'     => 'Cada archivo debe ser una imagen.',
            'fotos.*.mimes'     => 'Las fotos deben ser JPG, PNG o WebP.',
            'fotos.*.max'       => 'Cada foto no puede superar los 4 MB.',
        ];
    }
}
