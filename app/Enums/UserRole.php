<?php

namespace App\Enums;

enum UserRole: string
{
    case Master = 'master';
    case Instructor = 'instructor';
    case Atirador = 'atirador';

    /**
     * Retorna true se o role tem permissão de administração.
     */
    public function isAdmin(): bool
    {
        return in_array($this, [self::Master, self::Instructor]);
    }

    /**
     * Retorna a label legível em PT-BR.
     */
    public function label(): string
    {
        return match ($this) {
            self::Master     => 'Administrador',
            self::Instructor => 'Instrutor',
            self::Atirador   => 'Atirador',
        };
    }
}
