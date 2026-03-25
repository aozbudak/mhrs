<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            /** ISO-8601: 1 = Pazartesi … 7 = Pazar */
            $table->unsignedTinyInteger('weekday')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['doctor_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_working_hours');
    }
};
