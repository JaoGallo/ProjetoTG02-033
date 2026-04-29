<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nome_de_guerra',
        'email',
        'password',
        'ra',
        'cpf',
        'role',
        'points',
        'faults',
        'photo',
        'numero',
        'turma',
        'is_cfc',
        'telefone',
    ];

    public function escalaDiaria()
    {
        return $this->hasMany(\App\Models\EscalaDiaria::class);
    }

    public function filaEstado()
    {
        return $this->hasMany(\App\Models\FilaEstado::class);
    }

    /**
     * Retorna 'Mon' ou 'Atdr' baseado no is_cfc.
     */
    public function getGrupoEscalaAttribute(): string
    {
        return $this->is_cfc ? 'Mon' : 'Atdr';
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_cfc' => 'boolean',
        ];
    }
}
