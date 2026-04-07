<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('patient_city', 100)->nullable()->after('patient_favorites');
            $table->string('patient_district', 100)->nullable()->after('patient_city');
            $table->foreignId('aile_hekimi_doctor_id')->nullable()->after('patient_district')->constrained('doctors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('aile_hekimi_doctor_id');
            $table->dropColumn(['patient_district', 'patient_city']);
        });
    }
};
