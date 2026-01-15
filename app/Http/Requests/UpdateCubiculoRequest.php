<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCubiculoRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        // Se permite la validación; el control de acceso real se maneja vía Roles/Permisos
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud de actualización.
     */
    public function rules(): array
    {
        return [
            // Identificación del cubículo
            'prefijo'            => 'required|string|max:10',
            'numero'             => 'required|digits:3',

            // Relaciones con las tablas maestras
            'user_id'           => 'required|exists:users,id',
            'operating_area_id' => 'required|exists:operating_areas,id',

            // Restricción de modalidad virtual
            'tipo_atencion'     => 'required|in:virtual',
            'enlace_o_ubicacion' => 'required|url|max:255',
        ];
    }

    /**
     * Mensajes de error personalizados para la actualización.
     */
    public function messages(): array
    {
        return [
            'prefijo.required'            => 'El prefijo es necesario para identificar el cubículo.',
            'numero.digits'               => 'El número debe ser un código de 3 dígitos.',
            'user_id.exists'              => 'El usuario responsable asignado no es válido.',
            'operating_area_id.exists'    => 'El área operativa seleccionada no existe en los registros.',
            'tipo_atencion.in'            => 'Solo se permite actualizar a modalidad virtual.',
            'enlace_o_ubicacion.required' => 'El enlace de la reunión virtual no puede quedar vacío.',
            'enlace_o_ubicacion.url'      => 'Debe proporcionar una URL de reunión válida (ej. Teams o Zoom).',
        ];
    }
}
