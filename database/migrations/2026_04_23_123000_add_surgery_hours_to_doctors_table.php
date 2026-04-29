<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->time('ameliyat_baslangic_saati')->nullable()->after('is_aile_hekimi');
            $table->time('ameliyat_bitis_saati')->nullable()->after('ameliyat_baslangic_saati');
        });
    }

    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['ameliyat_baslangic_saati', 'ameliyat_bitis_saati']);
        });
    }
};
