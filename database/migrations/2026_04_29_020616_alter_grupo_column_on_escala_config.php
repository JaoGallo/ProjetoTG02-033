<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('escala_config', function (Blueprint $table) {
            $table->string('grupo', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('escala_config', function (Blueprint $table) {
            $table->enum('grupo', ['Mon', 'Atdr'])->change();
        });
    }
};
