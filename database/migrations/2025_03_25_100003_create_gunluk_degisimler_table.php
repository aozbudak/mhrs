<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Günlük override: belirli bir takvim gününde haftalık doctor_working_hours şablonunun yerine
     * geçer veya o günü tamamen kapatır.
     */
    public function up(): void
    {
        Schema::create('gunluk_degisimler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->date('tarih');
            /** kapali: o gün slot üretilmez | ozel_saatler: gunluk_degisim_saatleri kullanılır */
            $table->string('tur', 32);
            $table->string('aciklama', 512)->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'tarih']);
            $table->index(['doctor_id', 'tarih']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gunluk_degisimler');
    }
};
