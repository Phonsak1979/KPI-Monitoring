<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sync_schedules', function (Blueprint $table) {
            $table->text('last_run_result')->nullable()->after('last_run_at')->comment('ผลลัพธ์การ Sync ล่าสุด (JSON)');
        });
    }

    public function down(): void
    {
        Schema::table('sync_schedules', function (Blueprint $table) {
            $table->dropColumn('last_run_result');
        });
    }
};
