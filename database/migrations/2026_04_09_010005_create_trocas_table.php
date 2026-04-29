<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trocas', function (Blueprint $table) {
            $table->id();
            $table->date('data');
            $table->foreignId('integrante_origem_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('integrante_destino_id')->constrained('users')->onDelete('cascade');
            $table->text('motivo')->nullable();
            $table->foreignId('criado_por')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trocas');
    }
};
