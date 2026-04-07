<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('randevular', function (Blueprint $table) {
            $table->boolean('gizli')->default(false)->after('sikayet');
            $table->index(['user_id', 'gizli']);
        });
    }

    public function down(): void
    {
        Schema::table('randevular', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'gizli']);
            $table->dropColumn('gizli');
        });
    }
};
