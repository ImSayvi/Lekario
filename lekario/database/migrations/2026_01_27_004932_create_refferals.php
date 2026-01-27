<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('visit_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['examination', 'specialist']); // badania / lekarz specjalista
            $table->string('referral_to'); // na co skierowanie (np. "RTG klatki piersiowej" lub "Kardiolog")
            $table->text('diagnosis')->nullable(); // rozpoznanie / uzasadnienie
            $table->text('notes')->nullable(); // dodatkowe uwagi
            $table->date('issue_date'); // data wystawienia
            $table->date('valid_until')->nullable(); // ważne do
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};