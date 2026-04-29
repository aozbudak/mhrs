<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospital_department_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('senior_age_threshold')->default(65)->after('randevu_slot_dakika');
            $table->boolean('auto_transfer_senior')->default(false)->after('senior_age_threshold');
            $table->boolean('mesai_tasima_aktif')->default(true)->after('auto_transfer_senior');
            $table->time('ameliyat_blok_baslangic')->nullable()->after('mesai_tasima_aktif');
            $table->time('ameliyat_blok_bitis')->nullable()->after('ameliyat_blok_baslangic');
        });
    }

    public function down(): void
    {
        Schema::table('hospital_department_settings', function (Blueprint $table) {
            $table->dropColumn([
                'senior_age_threshold',
                'auto_transfer_senior',
                'mesai_tasima_aktif',
                'ameliyat_blok_baslangic',
                'ameliyat_blok_bitis',
            ]);
        });
    }
};
