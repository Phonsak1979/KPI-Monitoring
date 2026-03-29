<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sync_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('sync_time', 5)->comment('เวลา Sync อัตโนมัติ (HH:MM)');
            $table->boolean('is_active')->default(true)->comment('สถานะเปิด/ปิด');
            $table->timestamp('last_run_at')->nullable()->comment('เวลาที่รันล่าสุด');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_schedules');
    }
};
