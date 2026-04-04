<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'user_id',
        'category',
        'priority',
        'attachment',
        'turma',
    ];

    protected $casts = [
        'priority' => 'boolean',
    ];

    /**
     * O instrutor que criou o aviso.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuários que visualizaram o aviso.
     */
    public function readers()
    {
        return $this->belongsToMany(User::class, 'announcement_user')
                    ->withPivot('viewed_at')
                    ->withTimestamps();
    }
}
