<?php

namespace App\Http\Controllers;

use App\Http\Requests\MaterialRequest;
use App\Models\Material;
use App\Models\Receita;

class MaterialController extends Controller
{
    public function store(MaterialRequest $request, Receita $receita)
    {
        $this->authorize('editarEstrutura', $receita);

        $receita->materiais()->create($request->validated());

        return redirect()
            ->route('receitas.show', $receita)
            ->withFragment('materiais')
            ->with('sucesso', 'Material adicionado.');
    }

    public function edit(Material $material)
    {
        $this->authorize('editarEstrutura', $material->receita);

        return view('materiais.edit', [
            'material' => $material,
            'receita' => $material->receita,
        ]);
    }

    public function update(MaterialRequest $request, Material $material)
    {
        $this->authorize('editarEstrutura', $material->receita);

        $material->update($request->validated());

        return redirect()
            ->route('receitas.show', $material->receita)
            ->withFragment('materiais')
            ->with('sucesso', 'Material atualizado.');
    }

    public function destroy(Material $material)
    {
        $this->authorize('editarEstrutura', $material->receita);

        $receita = $material->receita;
        $material->delete();

        return redirect()
            ->route('receitas.show', $receita)
            ->withFragment('materiais')
            ->with('sucesso', 'Material removido.');
    }
}
