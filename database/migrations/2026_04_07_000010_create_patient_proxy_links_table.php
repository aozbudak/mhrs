<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Vekil hastanın, tanımlı başka bir hasta adına randevu alabilmesi için bağlantı.
     */
    public function up(): void
    {
        Schema::create('patient_proxy_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proxy_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('patient_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['proxy_user_id', 'patient_user_id']);
            $table->index('patient_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_proxy_links');
    }
};
