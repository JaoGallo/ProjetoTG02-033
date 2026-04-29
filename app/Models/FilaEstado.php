<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilaEstado extends Model
{
    protected $table = 'fila_estado';

    protected $fillable = [
        'user_id',
        'grupo',
        'posicao',
        'fase',
        'data_snapshot',
    ];

    protected $casts = [
        'data_snapshot' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
