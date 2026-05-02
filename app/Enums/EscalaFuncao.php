<?php

namespace App\Enums;

enum EscalaFuncao: string
{
    case Comandante = 'comandante';
    case Guarda = 'guarda';
    case Fila = 'fila';
    case Inicial = 'inicial';
    case Feriado = 'feriado';
    case Inativo = 'inativo';

    /**
     * Retorna true se a função é uma função de serviço ativo (escala).
     */
    public function isServico(): bool
    {
        return in_array($this, [self::Comandante, self::Guarda]);
    }

    /**
     * Retorna a label curta usada no valor da escala.
     */
    public function valorLabel(): string
    {
        return match ($this) {
            self::Comandante => 'Cmt',
            self::Guarda     => 'Gd',
            default          => '',
        };
    }
}
