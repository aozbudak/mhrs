<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hasta–doktor randevu kaydı; müsait slot ile eşleşir.
     */
    public function up(): void
    {
        Schema::create('randevular', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('randevu_slot_id')->unique()->constrained('randevu_slotlari')->cascadeOnDelete();
            $table->text('sikayet')->nullable();
            $table->string('durum', 32)->default('bekliyor')->index();
            $table->text('iptal_nedeni')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'durum']);
            $table->index(['doctor_id', 'durum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('randevular');
    }
};
