<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fila_estado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('grupo', ['Mon', 'Atdr']);
            $table->integer('posicao')->nullable(); // NULL quando em serviço
            $table->enum('fase', ['inicial', 'fila', 'servico']);
            $table->date('data_snapshot'); // data de referência do estado
            $table->unique(['user_id', 'data_snapshot']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fila_estado');
    }
};
