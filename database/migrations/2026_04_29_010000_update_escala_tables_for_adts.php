<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Atualiza a tabela de configurações para ser uma tabela de "Períodos/ADTs"
        Schema::table('escala_config', function (Blueprint $table) {
            $table->dropUnique(['grupo']);
            $table->string('nome')->after('id')->nullable();
            $table->enum('status', ['rascunho', 'publicado', 'finalizado'])->default('publicado')->after('gerada_em');
        });

        // Adiciona o vínculo na escala diária
        Schema::table('escala_diaria', function (Blueprint $table) {
            $table->foreignId('escala_config_id')->nullable()->after('id')->constrained('escala_config')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('escala_diaria', function (Blueprint $table) {
            $table->dropConstrainedForeignId('escala_config_id');
        });

        Schema::table('escala_config', function (Blueprint $table) {
            $table->unique('grupo');
            $table->dropColumn(['nome', 'status']);
        });
    }
};
