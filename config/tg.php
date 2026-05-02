<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Turma Ativa
    |--------------------------------------------------------------------------
    |
    | Define a turma/ano ativa do sistema. Todas as queries de atiradores,
    | escalas e avisos usam este valor. Altere no .env quando mudar o ano.
    |
    */

    'turma_ativa' => (int) env('TG_TURMA_ATIVA', date('Y')),

];
