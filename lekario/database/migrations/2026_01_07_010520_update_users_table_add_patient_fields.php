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
        Schema::table('users', function (Blueprint $table) {
            // Usunięcie starej kolumny 'name' jeśli istnieje
            if (Schema::hasColumn('users', 'name')) {
                $table->dropColumn('name');
            }

            // Dodanie nowych kolumn
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('phone', 15)->unique()->after('email');
            $table->string('pesel', 11)->unique()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Przywrócenie starej struktury
            $table->dropColumn(['first_name', 'last_name', 'phone', 'pesel']);
            $table->string('name')->after('id');
        });
    }
};