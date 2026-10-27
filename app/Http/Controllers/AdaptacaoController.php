<?php

namespace App\Http\Controllers;

use App\Models\Receita;
use Illuminate\Http\Request;

class AdaptacaoController extends Controller
{
    public function create(Receita $receita)
    {
        $this->authorize('adaptar', $receita);

        $receita->load('carreiras.pontos', 'carreiras.repeticoes', 'materiais');

        return view('receitas.adaptar', compact('receita'));
    }

    public function store(Request $request, Receita $receita)
    {
        $this->authorize('adaptar', $receita);

        // ADP-02: aceita vírgula como separador decimal antes de validar.
        $request->merge([
            'fator' => is_string($request->input('fator'))
                ? str_replace(',', '.', $request->input('fator'))
                : $request->input('fator'),
        ]);

        $dados = $request->validate([
            'fator' => ['required', 'numeric', 'min:0.1', 'max:10'],
            'titulo' => ['nullable', 'string', 'max:150'],
        ], [
            'fator.min' => 'O fator de adaptação deve estar entre 0,1 e 10.',
            'fator.max' => 'O fator de adaptação deve estar entre 0,1 e 10.',
        ]);

        $inicio = microtime(true);

        $adaptada = $receita->adaptar((float) $dados['fator'], $dados['titulo'] ?? null);

        $ms = (int) round((microtime(true) - $inicio) * 1000);

        return redirect()
            ->route('receitas.show', $adaptada)
            ->with('sucesso', "Receita adaptada gerada em {$ms} ms.");
    }
}
