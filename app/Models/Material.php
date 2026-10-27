<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $table = 'materiais';

    public const UNIDADES = ['g', 'm', 'novelo', 'unidade', 'cm'];

    protected $fillable = [
        'receita_id',
        'nome',
        'quantidade',
        'unidade',
    ];

    protected function casts(): array
    {
        return [
            'quantidade' => 'decimal:2',
        ];
    }

    public function receita(): BelongsTo
    {
        return $this->belongsTo(Receita::class);
    }

    public function calcularProporcional(float $fator): float
    {
        return round($this->quantidade * $fator, 2);
    }
}
