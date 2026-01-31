<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn('visit_date'); // usuwamy stare visit_date
            $table->dateTime('start_time')->after('patient_id');
            $table->dateTime('end_time')->after('start_time');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->after('end_time');
        });
    }

    public function down(): void {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn(['start_time','end_time','status']);
            $table->dateTime('visit_date')->after('patient_id');
        });
    }
};
