<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCubiculoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        // Se permite la validación para todos los usuarios autenticados con permisos
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            // Identificadores del nombre del cubículo
            'prefijo'            => 'required|string|max:10',
            'numero'             => 'required|digits:3',

            // Relaciones mandatorias
            'user_id'           => 'required|exists:users,id',
            'operating_area_id' => 'required|exists:operating_areas,id',

            // Lógica de atención 100% virtual
            'tipo_atencion'     => 'required|in:virtual',
            'enlace_o_ubicacion' => 'required|url|max:255', // Siempre debe ser un enlace válido
        ];
    }

    /**
     * Personaliza los mensajes de error para el usuario.
     */
    public function messages(): array
    {
        return [
            'prefijo.required'            => 'El prefijo es obligatorio (ej. C -).',
            'numero.required'             => 'El número del cubículo es obligatorio.',
            'numero.digits'               => 'El número debe tener exactamente 3 dígitos (ej. 001).',
            'user_id.required'            => 'Debe asignar un usuario responsable al cubículo.',
            'user_id.exists'              => 'El usuario seleccionado no es válido.',
            'operating_area_id.required'  => 'Debe vincular el cubículo a un área operativa.',
            'operating_area_id.exists'    => 'El área operativa seleccionada no existe.',
            'tipo_atencion.in'            => 'Secretaría General solo permite atención virtual.',
            'enlace_o_ubicacion.required' => 'El enlace de la reunión (Zoom/Teams) es obligatorio para atención virtual.',
            'enlace_o_ubicacion.url'      => 'Debe ingresar un enlace válido que comience con http:// o https://.',
        ];
    }
}
