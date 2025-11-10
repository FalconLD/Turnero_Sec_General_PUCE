<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; // <-- 1. IMPORTANTE: Añadir la importación de Rule

class UpdateCubiculoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Cámbialo a true para permitir que la validación se ejecute
        return true; 
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // 2. REEMPLAZAMOS LA LÓGICA DE REGLAS ANTERIOR
        return [
            // Reglas para el nuevo formato de nombre
            'prefijo' => 'required|string|max:10',
            'numero' => 'required|digits:3',
            
            // Reglas estándar
            'tipo_atencion'   => 'required|in:virtual,presencial',
            'user_id'         => 'required|exists:users,id',

            // Lógica condicional completa para enlace_o_ubicacion
            'enlace_o_ubicacion' => [
                // Es obligatorio SÓLO SI el tipo es 'virtual'
                Rule::requiredIf($this->input('tipo_atencion') == 'virtual'),

                // Permite que el campo esté vacío (para 'presencial')
                'nullable', 
                'string',
                'max:255',

                // DEBE ser una URL válida SI el tipo es 'virtual'
                Rule::when($this->input('tipo_atencion') == 'virtual', [
                    'url'
                ]),

                // NO DEBE ser una URL SI el tipo es 'presencial'
                Rule::when($this->input('tipo_atencion') == 'presencial', [
                    'not_regex:/^(https|http):\/\//'
                ]),
            ],
        ];
    }

    /**
     * Opcional: Personaliza los mensajes de error.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'enlace_o_ubicacion.url' => 'Para atención virtual, el campo debe ser un enlace válido (ej: https://zoom.us/...).',
            
            // 3. AÑADIMOS EL NUEVO MENSAJE
            'enlace_o_ubicacion.not_regex' => 'Para atención presencial, la ubicación no puede ser un enlace (URL).'
        ];
    }
}