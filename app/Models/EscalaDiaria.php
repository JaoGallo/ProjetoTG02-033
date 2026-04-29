<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscalaDiaria extends Model
{
    protected $table = 'escala_diaria';

    protected $fillable = [
        'escala_config_id',
        'user_id',
        'data',
        'valor',
        'funcao',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function config(): BelongsTo
    {
        return $this->belongsTo(EscalaConfig::class, 'escala_config_id');
    }

    /**
     * Retorna a classe CSS de cor baseada na funcao.
     */
    public function getCorCssAttribute(): string
    {
        return match ($this->funcao) {
            'guarda'     => 'cell-gd',
            'comandante' => 'cell-cmt',
            'fila'       => $this->valor === '1' ? 'cell-proximo' : 'cell-fila',
            'inicial'    => 'cell-inicial',
            'feriado'    => 'cell-feriado',
            'inativo'    => 'cell-inativo',
            default      => '',
        };
    }
}
