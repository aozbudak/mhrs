<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 18 yaş altı hastanın yasal velisinin T.C. kimlik numarası (nüfus / MERNİS ile doldurulabilir).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('veli_tc_kimlik_no', 11)->nullable()->after('tc_kimlik_no');
            $table->index('veli_tc_kimlik_no');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['veli_tc_kimlik_no']);
            $table->dropColumn('veli_tc_kimlik_no');
        });
    }
};
