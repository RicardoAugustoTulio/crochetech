<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjetoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projeto = $this->route('projeto');

        // Na edição de projeto com progresso, a receita fica travada e não é
        // enviada no formulário (PRJ-03); exige-se receita_id só quando aplicável.
        $temProgresso = $projeto
            && $projeto->progressos()
                ->where(fn ($q) => $q->where('pontos_feitos', '>', 0)->orWhere('concluida', true))
                ->exists();

        $receitaRule = $temProgresso
            ? ['nullable']
            : [
                'required',
                Rule::exists('receitas', 'id')->where('user_id', $this->user()->id),
            ];

        return [
            'titulo' => ['required', 'string', 'max:150'],
            'receita_id' => $receitaRule,
        ];
    }
}
