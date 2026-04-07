<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('engelli')->default(false)->after('gender');
        });

        Schema::table('randevu_slotlari', function (Blueprint $table) {
            $table->string('slot_tipi', 24)->default('normal')->after('durum');
            $table->index(['doctor_id', 'slot_tipi', 'baslangic']);
        });
    }

    public function down(): void
    {
        Schema::table('randevu_slotlari', function (Blueprint $table) {
            $table->dropIndex(['doctor_id', 'slot_tipi', 'baslangic']);
            $table->dropColumn('slot_tipi');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('engelli');
        });
    }
};
