<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class NewDay extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // 'user' => 'required|exists:users,id_usu',
        return [
            'schedule' => 'required|exists:schedules,id_hor',
            'dates' => 'required|array',
            'dates.*' => 'required|date',
        ];
    }
}
