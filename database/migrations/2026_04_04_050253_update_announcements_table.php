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
        Schema::table('announcements', function (Blueprint $table) {
            $table->enum('category', ['geral', 'urgente', 'escala', 'instrucao'])->default('geral')->after('content');
            $table->boolean('priority')->default(false)->after('category');
            $table->string('attachment')->nullable()->after('priority');
            $table->integer('turma')->nullable()->after('attachment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['category', 'priority', 'attachment', 'turma']);
        });
    }
};
