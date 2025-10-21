<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        $rules = [
            'nombre'          => 'required|string|max:255',
            'tipo_atencion'   => 'required|in:virtual,presencial',
            'user_id'         => 'required|exists:users,id',
            'enlace_o_ubicacion' => 'required|string|max:255', // Regla base
        ];

        // ---- LÓGICA DE VALIDACIÓN CLAVE ----
        // Si el tipo de atención es 'virtual', 
        // añadimos la regla de que DEBE ser una URL válida.
        if ($this->input('tipo_atencion') == 'virtual') {
            $rules['enlace_o_ubicacion'] .= '|url';
        }
        
        return $rules;
    }

    /**
     * Personaliza los mensajes de error.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'enlace_o_ubicacion.url' => 'Para atención virtual, el campo debe ser un enlace válido (ej: https://zoom.us/...).',
        ];
    }
}