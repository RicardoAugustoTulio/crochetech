<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepeticaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantidade' => ['required', 'integer', 'min:1'],
            'instrucao' => ['nullable', 'string'],
        ];
    }
}
