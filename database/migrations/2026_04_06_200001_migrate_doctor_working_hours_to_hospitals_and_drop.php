<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('doctor_working_hours') || ! Schema::hasTable('hospital_working_hours')) {
            return;
        }

        $reps = DB::table('doctors')
            ->join('doctor_working_hours', 'doctors.id', '=', 'doctor_working_hours.doctor_id')
            ->whereNotNull('doctors.hospital_id')
            ->select('doctors.hospital_id', DB::raw('MIN(doctors.id) as rep_doctor_id'))
            ->groupBy('doctors.hospital_id')
            ->get();

        $now = now();
        foreach ($reps as $row) {
            $hours = DB::table('doctor_working_hours')
                ->where('doctor_id', $row->rep_doctor_id)
                ->orderBy('weekday')
                ->orderBy('sort_order')
                ->get();

            foreach ($hours as $h) {
                DB::table('hospital_working_hours')->insert([
                    'hospital_id' => $row->hospital_id,
                    'weekday' => $h->weekday,
                    'start_time' => $h->start_time,
                    'end_time' => $h->end_time,
                    'sort_order' => $h->sort_order,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Schema::dropIfExists('doctor_working_hours');
    }

    public function down(): void
    {
        Schema::create('doctor_working_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['doctor_id', 'weekday']);
        });
    }
};
