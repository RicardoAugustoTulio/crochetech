<?php

namespace App\Http\Controllers;

use App\Http\Requests\RepeticaoRequest;
use App\Models\Carreira;
use App\Models\Repeticao;

class RepeticaoController extends Controller
{
    public function store(RepeticaoRequest $request, Carreira $carreira)
    {
        $this->authorize('editarEstrutura', $carreira->receita);

        $carreira->repeticoes()->create($request->validated());

        return redirect()
            ->route('carreiras.edit', $carreira)
            ->with('sucesso', 'Repetição adicionada.');
    }

    public function update(RepeticaoRequest $request, Repeticao $repeticao)
    {
        $this->authorize('editarEstrutura', $repeticao->carreira->receita);

        $repeticao->update($request->validated());

        return redirect()
            ->route('carreiras.edit', $repeticao->carreira)
            ->with('sucesso', 'Repetição atualizada.');
    }

    public function destroy(Repeticao $repeticao)
    {
        $this->authorize('editarEstrutura', $repeticao->carreira->receita);

        $carreira = $repeticao->carreira;
        $repeticao->delete();

        return redirect()
            ->route('carreiras.edit', $carreira)
            ->with('sucesso', 'Repetição removida.');
    }
}
