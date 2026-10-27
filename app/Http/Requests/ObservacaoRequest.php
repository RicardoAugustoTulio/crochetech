<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObservacaoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'texto' => ['required', 'string', 'max:1000'],
        ];
    }
}
