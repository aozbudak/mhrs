<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patient_proxy_links', function (Blueprint $table) {
            $table->string('kimlik_tc_kimlik_no', 11)->nullable()->after('patient_user_id');
            $table->string('kimlik_seri_no', 32)->nullable()->after('kimlik_tc_kimlik_no');
            $table->date('kimlik_dogum_tarihi')->nullable()->after('kimlik_seri_no');
            $table->string('kimlik_cinsiyet', 1)->nullable()->after('kimlik_dogum_tarihi');
        });
    }

    public function down(): void
    {
        Schema::table('patient_proxy_links', function (Blueprint $table) {
            $table->dropColumn([
                'kimlik_cinsiyet',
                'kimlik_dogum_tarihi',
                'kimlik_seri_no',
                'kimlik_tc_kimlik_no',
            ]);
        });
    }
};
