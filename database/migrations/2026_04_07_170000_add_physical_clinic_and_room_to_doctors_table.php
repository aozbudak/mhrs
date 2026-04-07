<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->string('physical_clinic_name', 120)->nullable()->after('hospital_id');
            $table->string('room_no', 32)->nullable()->after('physical_clinic_name');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['physical_clinic_name', 'room_no']);
        });
    }
};
