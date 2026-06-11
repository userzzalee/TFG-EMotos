<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'contenido' => 'required|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'contenido.required' => 'El mensaje no puede estar vacío.',
            'contenido.max' => 'El mensaje no puede superar los 2000 caracteres.',
        ];
    }
}
