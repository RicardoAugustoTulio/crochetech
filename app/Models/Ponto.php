<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ponto extends Model
{
    use HasFactory;

    public const TIPOS = [
        'corrente',
        'ponto baixo',
        'ponto alto',
        'meio ponto alto',
        'ponto baixíssimo',
        'aumento',
        'diminuição',
        'anel mágico',
        'outro',
    ];

    protected $fillable = [
        'carreira_id',
        'tipo',
        'quantidade',
        'instrucao',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'integer',
        ];
    }

    public function carreira(): BelongsTo
    {
        return $this->belongsTo(Carreira::class);
    }

    /**
     * Pontos que cada instrução deixa na carreira: o aumento produz 2 pontos
     * e o anel mágico é só a base (0). Os demais tipos contam 1.
     */
    public const PONTOS_GERADOS = [
        'aumento' => 2,
        'anel mágico' => 0,
    ];

    public function pontosGerados(): int
    {
        return $this->quantidade * (self::PONTOS_GERADOS[$this->tipo] ?? 1);
    }

    public function adaptar(float $fator): int
    {
        // O anel mágico é a base da peça e não muda de quantidade.
        if ($this->tipo === 'anel mágico') {
            return $this->quantidade;
        }

        return max(1, (int) round($this->quantidade * $fator));
    }
}
