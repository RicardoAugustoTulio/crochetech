<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carreira extends Model
{
    use HasFactory;

    protected $fillable = [
        'receita_id',
        'numero',
        'descricao',
    ];

    protected function casts(): array
    {
        return [
            'numero' => 'integer',
        ];
    }

    public function receita(): BelongsTo
    {
        return $this->belongsTo(Receita::class);
    }

    public function pontos(): HasMany
    {
        return $this->hasMany(Ponto::class);
    }

    public function repeticoes(): HasMany
    {
        return $this->hasMany(Repeticao::class);
    }

    public function progressos(): HasMany
    {
        return $this->hasMany(Progresso::class);
    }

    public function pontosEsperados(): int
    {
        $somaPontos = $this->pontos->sum(fn (Ponto $ponto) => $ponto->pontosGerados());
        $somaRepeticoes = $this->repeticoes->sum('quantidade');

        return (int) ($somaPontos * ($somaRepeticoes > 0 ? $somaRepeticoes : 1));
    }
}
