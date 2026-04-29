<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_department_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('randevu_slot_dakika')->default(30);
            $table->timestamps();

            $table->unique(['hospital_id', 'department_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_department_settings');
    }
};
