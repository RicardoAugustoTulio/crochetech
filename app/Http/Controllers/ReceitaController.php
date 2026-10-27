<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReceitaRequest;
use App\Models\Receita;
use Illuminate\Http\Request;

class ReceitaController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->validate([
            'tipo' => ['nullable', 'in:original,adaptada'],
            'busca' => ['nullable', 'string', 'max:150'],
        ]);

        $receitas = $request->user()->receitas()
            ->withCount('carreiras')
            ->when($filtros['tipo'] ?? null, fn ($q, $tipo) => $q->where('tipo', $tipo))
            ->when($filtros['busca'] ?? null, fn ($q, $busca) => $q->where('titulo', 'like', '%'.$busca.'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('receitas.index', [
            'receitas' => $receitas,
            'tipo' => $filtros['tipo'] ?? null,
            'busca' => $filtros['busca'] ?? null,
        ]);
    }

    public function create()
    {
        return view('receitas.create');
    }

    public function store(ReceitaRequest $request)
    {
        $receita = $request->user()->receitas()->create([
            ...$request->validated(),
            'tipo' => Receita::TIPO_ORIGINAL,
        ]);

        return redirect()->route('receitas.show', $receita)
            ->with('sucesso', 'Receita criada com sucesso.');
    }

    public function show(Receita $receita)
    {
        $this->authorize('view', $receita);

        $receita->load([
            'carreiras.pontos',
            'carreiras.repeticoes',
            'materiais',
            'original',
        ]);

        return view('receitas.show', ['receita' => $receita]);
    }

    public function edit(Receita $receita)
    {
        $this->authorize('update', $receita);

        return view('receitas.edit', ['receita' => $receita]);
    }

    public function update(ReceitaRequest $request, Receita $receita)
    {
        $this->authorize('update', $receita);

        $receita->update($request->validated());

        return redirect()->route('receitas.show', $receita)
            ->with('sucesso', 'Receita atualizada com sucesso.');
    }

    public function destroy(Receita $receita)
    {
        $this->authorize('delete', $receita);

        $receita->delete();

        return redirect()->route('receitas.index')
            ->with('sucesso', 'Receita excluída com sucesso.');
    }
}
