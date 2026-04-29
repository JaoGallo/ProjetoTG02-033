<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escala_config', function (Blueprint $table) {
            $table->id();
            $table->enum('grupo', ['Mon', 'Atdr'])->unique();
            $table->date('data_inicio');
            $table->date('data_fim');
            $table->unsignedTinyInteger('qnt_cmt_dia')->default(1);
            $table->unsignedTinyInteger('qnt_gd_dia');
            $table->unsignedTinyInteger('dias_iniciais');
            $table->unsignedTinyInteger('valor_inicial')->default(50);
            $table->timestamp('gerada_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escala_config');
    }
};
