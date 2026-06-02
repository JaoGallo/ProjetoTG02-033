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
        Schema::table('escala_diaria', function (Blueprint $table) {
            $table->boolean('pontos_contabilizados')->default(false)->after('valor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escala_diaria', function (Blueprint $table) {
            $table->dropColumn('pontos_contabilizados');
        });
    }
};
