<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('randevular', function (Blueprint $table) {
            $table->foreignId('booked_by_user_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('randevular', function (Blueprint $table) {
            $table->dropConstrainedForeignId('booked_by_user_id');
        });
    }
};
