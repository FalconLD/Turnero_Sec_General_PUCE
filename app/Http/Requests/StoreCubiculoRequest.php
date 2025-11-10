<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- ¡MUY IMPORTANTE! Importa la clase Rule.

class StoreCubiculoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Permite que la validación se ejecute
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'prefijo' => 'required|string|max:10',
            'numero' => 'required|digits:3',
            'tipo_atencion'   => 'required|in:virtual,presencial',
            'user_id'         => 'required|exists:users,id',

            // ---- NUEVA LÓGICA DE VALIDACIÓN CLAVE ----
            'enlace_o_ubicacion' => [
                // 1. Es obligatorio SÓLO SI el tipo es 'virtual'.
                Rule::requiredIf($this->input('tipo_atencion') == 'virtual'),

                // 2. Si se rellena (para 'presencial' es opcional), debe ser un string y max 255.
                'nullable', // Permite que el campo esté vacío (para 'presencial')
                'string',
                'max:255',

                // 3. DEBE ser una URL válida SI el tipo es 'virtual'.
                Rule::when($this->input('tipo_atencion') == 'virtual', [
                    'url'
                ]),

                // 4. NO DEBE ser una URL SI el tipo es 'presencial'.
                Rule::when($this->input('tipo_atencion') == 'presencial', [
                    // Esta Regex busca si el texto empieza con http:// o https://
                    'not_regex:/^(https|http):\/\//'
                ]),
            ],
        ];
    }

    /**
     * Personaliza los mensajes de error.
     *
     * @return array
     */
    public function messages()
    {
        return [
            // Mensaje para la regla 'url' (cuando es virtual)
            'enlace_o_ubicacion.url' => 'Para atención virtual, el campo debe ser un enlace válido (ej: https://zoom.us/...).',
            
            // Mensaje para la regla 'not_regex' (cuando es presencial)
            'enlace_o_ubicacion.not_regex' => 'Para atención presencial, la ubicación no puede ser un enlace (URL).'
        ];
    }
}