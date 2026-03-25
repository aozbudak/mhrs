<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Üretilen veya yönetilen randevu zaman dilimleri. Kaynak: haftalık şablon veya günlük override.
     */
    public function up(): void
    {
        Schema::create('randevu_slotlari', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->dateTime('baslangic');
            $table->dateTime('bitis');
            $table->string('durum', 32)->default('musait');
            /** Slot o günkü override kurallarından üretildiyse bağlantı (silinirse null). */
            $table->foreignId('gunluk_degisim_id')->nullable()->constrained('gunluk_degisimler')->nullOnDelete();
            $table->timestamps();

            $table->unique(['doctor_id', 'baslangic']);
            $table->index(['doctor_id', 'baslangic']);
            $table->index(['doctor_id', 'durum']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('randevu_slotlari');
    }
};
