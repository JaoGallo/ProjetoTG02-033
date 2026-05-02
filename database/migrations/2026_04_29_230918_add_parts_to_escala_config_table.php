<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('escala_config', function (Blueprint $table) {
            $table->text('part2_instrucao')->nullable()->after('status');
            $table->text('part3_assuntos_gerais')->nullable()->after('part2_instrucao');
            $table->text('part4_justica_disciplina')->nullable()->after('part3_assuntos_gerais');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escala_config', function (Blueprint $table) {
            $table->dropColumn(['part2_instrucao', 'part3_assuntos_gerais', 'part4_justica_disciplina']);
        });
    }
};
