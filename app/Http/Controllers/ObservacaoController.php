<?php

namespace App\Http\Controllers;

use App\Http\Requests\ObservacaoRequest;
use App\Models\Observacao;
use App\Models\Projeto;

class ObservacaoController extends Controller
{
    public function store(ObservacaoRequest $request, Projeto $projeto)
    {
        $this->authorize('update', $projeto);

        $projeto->registrarObservacao($request->validated('texto'));

        return redirect()
            ->route('projetos.show', $projeto)
            ->with('sucesso', 'Observação registrada.');
    }

    public function destroy(Observacao $observacao)
    {
        $this->authorize('update', $observacao->projeto);

        $projeto = $observacao->projeto;
        $observacao->delete();

        return redirect()
            ->route('projetos.show', $projeto)
            ->with('sucesso', 'Observação removida.');
    }
}
