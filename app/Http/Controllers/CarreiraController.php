<?php

namespace App\Http\Controllers;

use App\Http\Requests\CarreiraRequest;
use App\Models\Carreira;
use App\Models\Receita;

class CarreiraController extends Controller
{
    public function create(Receita $receita)
    {
        $this->authorize('editarEstrutura', $receita);

        return view('carreiras.create', [
            'receita' => $receita,
            'numeroSugerido' => $receita->proximoNumeroCarreira(),
        ]);
    }

    public function store(CarreiraRequest $request, Receita $receita)
    {
        $this->authorize('editarEstrutura', $receita);

        $carreira = $receita->carreiras()->create($request->validated());

        return redirect()
            ->route('carreiras.edit', $carreira)
            ->with('sucesso', 'Carreira adicionada.');
    }

    public function edit(Carreira $carreira)
    {
        $this->authorize('editarEstrutura', $carreira->receita);

        $carreira->load('pontos', 'repeticoes');

        return view('carreiras.edit', [
            'carreira' => $carreira,
            'receita' => $carreira->receita,
        ]);
    }

    public function update(CarreiraRequest $request, Carreira $carreira)
    {
        $this->authorize('editarEstrutura', $carreira->receita);

        $carreira->update($request->validated());

        return redirect()
            ->route('carreiras.edit', $carreira)
            ->with('sucesso', 'Carreira atualizada.');
    }

    public function destroy(Carreira $carreira)
    {
        $this->authorize('editarEstrutura', $carreira->receita);

        $receita = $carreira->receita;
        $carreira->delete();

        return redirect()
            ->route('receitas.show', $receita)
            ->with('sucesso', 'Carreira removida.');
    }
}
