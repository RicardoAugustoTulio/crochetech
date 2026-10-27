<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:150'],
            'descricao' => ['nullable', 'string'],
        ];
    }
}
