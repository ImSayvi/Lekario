<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('visit_id')->nullable()->constrained()->onDelete('set null');
            $table->string('medication_name');
            $table->string('medication_code')->nullable(); // kod leku (np. EAN)
            $table->text('dosage')->nullable(); // dawkowanie
            $table->integer('quantity')->default(1); // ilość opakowań
            $table->boolean('is_refundable')->default(false); // czy odpłatne
            $table->text('notes')->nullable(); // dodatkowe uwagi
            $table->date('issue_date'); // data wystawienia
            $table->date('expiry_date')->nullable(); // data ważności recepty
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};