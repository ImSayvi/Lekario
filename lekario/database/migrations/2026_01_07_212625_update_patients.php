<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Dodajemy kolumnę nullable na początek
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id');
        });

        // 2. Opcjonalnie wypełniamy user_id dla istniejących pacjentów
        // Możesz dopasować ręcznie, np. przypisując pierwszego usera do wszystkich pacjentów
        DB::table('patients')->update(['user_id' => 1]);

        // 3. Zmieniamy kolumnę na NOT NULL i dodajemy constraint
        Schema::table('patients', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->unique()->change();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
