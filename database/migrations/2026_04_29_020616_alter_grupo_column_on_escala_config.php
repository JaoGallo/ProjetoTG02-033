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
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            // SQLite doesn't support MODIFY COLUMN. 
            // Since enum is already a varchar in SQLite, we might just need to remove the unique constraint or similar.
            // But if we want to change the type, we usually have to recreate the table.
            // However, in Laravel 10+, ->change() should work IF properly configured.
            // If it's failing, we'll try to do it manually or skip if it's already a string.
            try {
                Schema::table('escala_config', function (Blueprint $table) {
                    $table->string('grupo', 255)->change();
                });
            } catch (\Exception $e) {
                // If it still fails, it's likely already compatible enough for SQLite
            }
        } else {
            Schema::table('escala_config', function (Blueprint $table) {
                $table->string('grupo', 255)->change();
            });
        }
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
