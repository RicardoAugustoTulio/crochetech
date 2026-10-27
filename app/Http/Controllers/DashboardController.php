<?php

namespace App\Http\Controllers;

use App\Models\Projeto;
use App\Models\Receita;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $request->only(['status', 'receita_id', 'de', 'ate']);

        // Eager-load de carreiras e progressos p/ calcularProgresso sem N+1.
        $projetos = Projeto::where('user_id', $request->user()->id)
            ->filtrar($filtros)
            ->with(['receita.carreiras', 'progressos'])
            ->latest()
            ->get();

        $totais = [
            'total' => $projetos->count(),
            'nao_iniciado' => $projetos->where('status', 'nao_iniciado')->count(),
            'em_andamento' => $projetos->where('status', 'em_andamento')->count(),
            'concluido' => $projetos->where('status', 'concluido')->count(),
        ];

        $mediaConclusao = $projetos->isEmpty()
            ? 0.0
            : round($projetos->avg(fn (Projeto $p) => $p->calcularProgresso()), 1);

        $receitas = Receita::where('user_id', $request->user()->id)
            ->orderBy('titulo')
            ->get();

        $totalReceitas = [
            'total' => $receitas->count(),
            'original' => $receitas->where('tipo', Receita::TIPO_ORIGINAL)->count(),
            'adaptada' => $receitas->where('tipo', Receita::TIPO_ADAPTADA)->count(),
        ];

        return view('dashboard', [
            'projetos' => $projetos,
            'totais' => $totais,
            'mediaConclusao' => $mediaConclusao,
            'totalReceitas' => $totalReceitas,
            'receitas' => $receitas,
            'filtros' => $filtros,
        ]);
    }
}
