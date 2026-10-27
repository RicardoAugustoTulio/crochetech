<?php

namespace App\Http\Requests;

use App\Models\Ponto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PontoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', Rule::in(Ponto::TIPOS)],
            'quantidade' => ['required', 'integer', 'min:1'],
            'instrucao' => ['nullable', 'string'],
        ];
    }
}
