<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Projeto extends Model
{
    use HasFactory;

    protected $table = 'projetos';

    public const STATUS = [
        'nao_iniciado' => 'Não iniciado',
        'em_andamento' => 'Em andamento',
        'concluido' => 'Concluído',
    ];

    protected $fillable = [
        'user_id',
        'receita_id',
        'titulo',
        'status',
        'data_conclusao',
    ];

    protected function casts(): array
    {
        return [
            'data_conclusao' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function receita(): BelongsTo
    {
        return $this->belongsTo(Receita::class);
    }

    public function progressos(): HasMany
    {
        return $this->hasMany(Progresso::class);
    }

    public function observacoes(): HasMany
    {
        return $this->hasMany(Observacao::class)->latest();
    }

    public function calcularProgresso(): float
    {
        $total = $this->receita->carreiras()->count();

        if ($total === 0) {
            return 0.0;
        }

        $concluidas = $this->progressos()->where('concluida', true)->count();

        return round($concluidas / $total * 100, 1);
    }

    public function atualizarStatus(): void
    {
        $percentual = $this->calcularProgresso();
        $algumPontoFeito = $this->progressos()->where('pontos_feitos', '>', 0)->exists();

        if ($percentual >= 100) {
            $this->status = 'concluido';
            $this->data_conclusao = $this->data_conclusao ?? now();
        } elseif ($percentual > 0 || $algumPontoFeito) {
            $this->status = 'em_andamento';
            $this->data_conclusao = null;
        } else {
            $this->status = 'nao_iniciado';
            $this->data_conclusao = null;
        }

        $this->save();
    }

    public function registrarObservacao(string $texto): Observacao
    {
        return $this->observacoes()->create(['texto' => $texto]);
    }

    public function progressoDa(Carreira $c): Progresso
    {
        return $this->progressos()->firstOrCreate(['carreira_id' => $c->id]);
    }

    /**
     * Filtra projetos por status, receita e período de criação.
     *
     * @param  array<string, mixed>  $filtros
     */
    public function scopeFiltrar(Builder $query, array $filtros): Builder
    {
        return $query
            ->when(
                ! empty($filtros['status']) && array_key_exists($filtros['status'], self::STATUS),
                fn (Builder $q) => $q->where('status', $filtros['status'])
            )
            ->when(
                ! empty($filtros['receita_id']),
                fn (Builder $q) => $q->where('receita_id', $filtros['receita_id'])
            )
            ->when(
                ! empty($filtros['de']),
                fn (Builder $q) => $q->whereDate('created_at', '>=', $filtros['de'])
            )
            ->when(
                ! empty($filtros['ate']),
                fn (Builder $q) => $q->whereDate('created_at', '<=', $filtros['ate'])
            );
    }
}
