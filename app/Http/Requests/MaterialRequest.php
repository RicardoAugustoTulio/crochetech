<?php

namespace App\Http\Requests;

use App\Models\Material;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:100'],
            'quantidade' => ['required', 'numeric', 'gt:0'],
            'unidade' => ['required', Rule::in(Material::UNIDADES)],
        ];
    }
}
