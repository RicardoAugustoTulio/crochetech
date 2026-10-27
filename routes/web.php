<?php

use App\Http\Controllers\AdaptacaoController;
use App\Http\Controllers\CarreiraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExecucaoController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\ObservacaoController;
use App\Http\Controllers\PontoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjetoController;
use App\Http\Controllers\ReceitaController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\RepeticaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('receitas', ReceitaController::class);
    Route::get('receitas/{receita}/adaptar', [AdaptacaoController::class, 'create'])->name('receitas.adaptar.create');
    Route::post('receitas/{receita}/adaptar', [AdaptacaoController::class, 'store'])->name('receitas.adaptar.store');
    Route::resource('receitas.carreiras', CarreiraController::class)->except(['index', 'show'])->shallow();
    Route::resource('carreiras.pontos', PontoController::class)->only(['store', 'update', 'destroy'])->shallow();
    Route::resource('carreiras.repeticoes', RepeticaoController::class)->only(['store', 'update', 'destroy'])->shallow()->parameters(['repeticoes' => 'repeticao']);
    Route::resource('receitas.materiais', MaterialController::class)->only(['store', 'edit', 'update', 'destroy'])->shallow()->parameters(['materiais' => 'material']);

    Route::resource('projetos', ProjetoController::class);
    Route::resource('projetos.observacoes', ObservacaoController::class)->only(['store', 'destroy'])->shallow()->parameters(['observacoes' => 'observacao']);
    Route::get('projetos/{projeto}/executar', [ExecucaoController::class, 'show'])->name('projetos.executar');
    Route::post('projetos/{projeto}/carreiras/{carreira}/pontos', [ExecucaoController::class, 'pontos'])->name('projetos.executar.pontos');
    Route::post('projetos/{projeto}/carreiras/{carreira}/concluir', [ExecucaoController::class, 'concluir'])->name('projetos.executar.concluir');

    Route::get('relatorios/projetos', [RelatorioController::class, 'projetos'])->name('relatorios.projetos');
    Route::get('relatorios/receitas/{receita}/materiais', [RelatorioController::class, 'materiais'])->name('relatorios.materiais');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
