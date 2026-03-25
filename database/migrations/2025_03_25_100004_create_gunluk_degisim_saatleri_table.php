<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * tur=ozel_saatler iken o güne ait çalışma aralıkları (doctor_working_hours ile aynı mantık, tek güne özel).
     */
    public function up(): void
    {
        Schema::create('gunluk_degisim_saatleri', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gunluk_degisim_id')->constrained('gunluk_degisimler')->cascadeOnDelete();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('gunluk_degisim_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gunluk_degisim_saatleri');
    }
};
