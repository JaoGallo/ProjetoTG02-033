<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop tabelas legadas que serão substituídas pelo novo sistema
        Schema::dropIfExists('scale_swaps');
        Schema::dropIfExists('scales');
    }

    public function down(): void
    {
        // Recria as tabelas legadas se necessário fazer rollback
        Schema::create('scales', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->enum('status', ['confirmed', 'swapped', 'absent'])->default('confirmed');
            $table->timestamps();
        });

        Schema::create('scale_swaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('target_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('scale_id')->constrained('scales')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected', 'approved'])->default('pending');
            $table->timestamps();
        });
    }
};
