<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Troca extends Model
{
    protected $table = 'trocas';

    protected $fillable = [
        'data',
        'integrante_origem_id',
        'integrante_destino_id',
        'motivo',
        'criado_por',
    ];

    protected $casts = [
        'data' => 'date',
    ];

    public function integranteOrigem(): BelongsTo
    {
        return $this->belongsTo(User::class, 'integrante_origem_id');
    }

    public function integranteDestino(): BelongsTo
    {
        return $this->belongsTo(User::class, 'integrante_destino_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}
