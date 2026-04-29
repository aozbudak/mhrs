<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hospital_department_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            /** ISO-8601: 1 = Pazartesi … 7 = Pazar */
            $table->unsignedTinyInteger('weekday')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['hospital_id', 'department_id', 'weekday']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_department_working_hours');
    }
};
