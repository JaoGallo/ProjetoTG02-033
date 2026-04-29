<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escala_diaria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('data');
            $table->string('valor', 10)->default('');
            $table->enum('funcao', ['inicial', 'guarda', 'comandante', 'fila', 'feriado', 'inativo']);
            $table->unique(['user_id', 'data']);
            $table->index('data');
            $table->index('funcao');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escala_diaria');
    }
};
