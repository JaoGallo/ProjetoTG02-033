<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EscalaConfig extends Model
{
    protected $table = 'escala_config';

    protected $fillable = [
        'nome',
        'grupo',
        'data_inicio',
        'data_fim',
        'qnt_cmt_dia',
        'qnt_gd_dia',
        'dias_iniciais',
        'valor_inicial',
        'status',
        'gerada_em',
        'part2_instrucao',
        'part3_assuntos_gerais',
        'part4_justica_disciplina',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim'    => 'date',
        'gerada_em'   => 'datetime',
    ];

    public function escalaDiaria(): HasMany
    {
        return $this->hasMany(EscalaDiaria::class, 'escala_config_id');
    }

    /**
     * Retorna a label legível do grupo.
     */
    public function getGrupoLabelAttribute(): string
    {
        return $this->grupo === 'Mon' ? 'Monitores (CFC)' : 'Atiradores';
    }

    /**
     * Total de pessoas em serviço por dia.
     */
    public function getQntServicoDiaAttribute(): int
    {
        return $this->qnt_cmt_dia + $this->qnt_gd_dia;
    }
}
