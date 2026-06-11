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
            'marca'       => 'required|string|max:100',
            'modelo'      => 'required|string|max:100',
            'matricula'   => 'required|string|max:20',
            'fecha_cita'  => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (! AgendaTaller::esSlotValido(Carbon::parse($value))) {
                        $fail('La fecha y hora seleccionadas no están disponibles. Elige otro hueco.');
                    }
                },
            ],
            'problema'    => 'required|string|max:2000',
            'comentarios' => 'nullable|string|max:1000',
            'fotos'       => 'nullable|array|max:5',
            'fotos.*'     => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ];
    }
}
