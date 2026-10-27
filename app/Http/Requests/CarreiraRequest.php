<?php

namespace App\Http\Requests;

use App\Models\Carreira;
use App\Models\Receita;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CarreiraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $carreira = $this->route('carreira');
        $receita = $carreira instanceof Carreira
            ? $carreira->receita
            : $this->route('receita');

        $receitaId = $receita instanceof Receita ? $receita->id : $receita;
        $ignoreId = $carreira instanceof Carreira ? $carreira->id : null;

        return [
            'numero' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('carreiras', 'numero')
                    ->where(fn ($query) => $query->where('receita_id', $receitaId))
                    ->ignore($ignoreId),
            ],
            'descricao' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'numero.unique' => 'Já existe uma carreira com este número nesta receita.',
        ];
    }
}
