<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->json('districts')->nullable()->after('city');
        });

        $rows = DB::table('hospitals')->select('id', 'district')->get();
        foreach ($rows as $row) {
            $d = isset($row->district) ? trim((string) $row->district) : '';
            $payload = $d === '' ? null : json_encode([$d], JSON_UNESCAPED_UNICODE);
            DB::table('hospitals')->where('id', $row->id)->update(['districts' => $payload]);
        }

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn('district');
        });
    }

    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            $table->string('district', 100)->nullable()->after('city');
        });

        $rows = DB::table('hospitals')->select('id', 'districts')->get();
        foreach ($rows as $row) {
            $arr = json_decode($row->districts ?? 'null', true);
            $first = is_array($arr) && count($arr) > 0 ? (string) reset($arr) : null;
            DB::table('hospitals')->where('id', $row->id)->update(['district' => $first]);
        }

        Schema::table('hospitals', function (Blueprint $table) {
            $table->dropColumn('districts');
        });
    }
};
