<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('randevular', function (Blueprint $table) {
            $table->string('katilim_durumu', 24)
                ->default('yanit_bekleniyor')
                ->index()
                ->after('durum');
            $table->timestamp('hatirlatma_bildirildi_at')->nullable()->after('katilim_durumu');
            $table->timestamp('katilim_bildirimi_at')->nullable()->after('hatirlatma_bildirildi_at');
        });
    }

    public function down(): void
    {
        Schema::table('randevular', function (Blueprint $table) {
            $table->dropColumn([
                'katilim_durumu',
                'hatirlatma_bildirildi_at',
                'katilim_bildirimi_at',
            ]);
        });
    }
};
