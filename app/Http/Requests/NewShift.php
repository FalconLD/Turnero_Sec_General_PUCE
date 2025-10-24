<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NewShift extends FormRequest
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
        return [
            'identification' => ['required'],
            'name' => ['required'],
            'email' => ['required', 'max:255', 'email'],
            'phone' => ['required', 'min:7', 'max:10', 'regex:/^[0-9]+$/'],
            'start_time' => 'required|date_format:H:i:s',
            'date' => 'required|date',
            'form_id' => 'required|exists:forms,id_for',
            'answers' => 'array',
            'answers.*.question_id' => 'required_with:answers.*.answer|exists:questions,id_pre',
            'answers.*.answer' => 'nullable',
        ];
    }
}
