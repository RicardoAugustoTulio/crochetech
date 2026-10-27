<?php

namespace App\Http\Controllers;

use App\Http\Requests\PontoRequest;
use App\Models\Carreira;
use App\Models\Ponto;

class PontoController extends Controller
{
    public function store(PontoRequest $request, Carreira $carreira)
    {
        $this->authorize('editarEstrutura', $carreira->receita);

        $carreira->pontos()->create($request->validated());

        return redirect()
            ->route('carreiras.edit', $carreira)
            ->with('sucesso', 'Ponto adicionado.');
    }

    public function update(PontoRequest $request, Ponto $ponto)
    {
        $this->authorize('editarEstrutura', $ponto->carreira->receita);

        $ponto->update($request->validated());

        return redirect()
            ->route('carreiras.edit', $ponto->carreira)
            ->with('sucesso', 'Ponto atualizado.');
    }

    public function destroy(Ponto $ponto)
    {
        $this->authorize('editarEstrutura', $ponto->carreira->receita);

        $carreira = $ponto->carreira;
        $ponto->delete();

        return redirect()
            ->route('carreiras.edit', $carreira)
            ->with('sucesso', 'Ponto removido.');
    }
}
