<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\Receita;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RelatorioController extends Controller
{
    public function projetos(Request $request): View
    {
        $filtros = $request->only(['status', 'receita_id', 'de', 'ate']);

        $projetos = Projeto::where('user_id', $request->user()->id)
            ->filtrar($filtros)
            ->with(['receita.carreiras', 'progressos'])
            ->latest()
            ->get();

        $totais = [
            'total' => $projetos->count(),
            'concluido' => $projetos->where('status', 'concluido')->count(),
            'media' => $projetos->isEmpty()
                ? 0.0
                : round($projetos->avg(fn (Projeto $p) => $p->calcularProgresso()), 1),
        ];

        return view('relatorios.projetos', [
            'projetos' => $projetos,
            'totais' => $totais,
            'filtros' => $filtros,
            'receitas' => Receita::where('user_id', $request->user()->id)->orderBy('titulo')->get(),
        ]);
    }

    public function materiais(Receita $receita): View
    {
        $this->authorize('view', $receita);

        return view('relatorios.materiais', [
            'receita' => $receita,
            'materiais' => $receita->calcularMateriais(),
        ]);
    }
}
