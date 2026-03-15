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
        $tables = ['s_childdev_specialpp'];

        foreach ($tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                // ใช้ string เพราะ id จาก API เป็น varchar(32) เช่น '1c1b8e24...'
                $table->string('id', 32)->primary();
                $table->string('hospcode', 5);
                $table->string('areacode', 8);
                $table->string('date_com', 14)->nullable();
                $table->string('b_year', 4);
                $table->string('monthly', 2);
                $table->integer('target')->nullable();
                $table->integer('result')->nullable();
                $table->integer('target_9')->nullable();
                $table->integer('result_9')->nullable();
                $table->integer('1b260_1_9')->nullable();
                $table->integer('1b261_9')->nullable();
                $table->integer('1b262_9')->nullable();
                $table->integer('follow_9')->nullable();
                $table->integer('1b260_2_9')->nullable();
                $table->integer('improper_9')->nullable();
                $table->integer('1b202_9')->nullable();
                $table->integer('1b212_9')->nullable();
                $table->integer('1b222_9')->nullable();
                $table->integer('1b232_9')->nullable();
                $table->integer('1b242_9')->nullable();
                $table->integer('wait30_9')->nullable();
                $table->integer('loss_9')->nullable();
                $table->integer('target_18')->nullable();
                $table->integer('result_18')->nullable();
                $table->integer('1b260_1_18')->nullable();
                $table->integer('1b261_18')->nullable();
                $table->integer('1b262_18')->nullable();
                $table->integer('follow_18')->nullable();
                $table->integer('1b260_2_18')->nullable();
                $table->integer('improper_18')->nullable();
                $table->integer('1b202_18')->nullable();
                $table->integer('1b212_18')->nullable();
                $table->integer('1b222_18')->nullable();
                $table->integer('1b232_18')->nullable();
                $table->integer('1b242_18')->nullable();
                $table->integer('wait30_18')->nullable();
                $table->integer('loss_18')->nullable();
                $table->integer('target_30')->nullable();
                $table->integer('result_30')->nullable();
                $table->integer('1b260_1_30')->nullable();
                $table->integer('1b261_30')->nullable();
                $table->integer('1b262_30')->nullable();
                $table->integer('follow_30')->nullable();
                $table->integer('1b260_2_30')->nullable();
                $table->integer('improper_30')->nullable();
                $table->integer('1b202_30')->nullable();
                $table->integer('1b212_30')->nullable();
                $table->integer('1b222_30')->nullable();
                $table->integer('1b232_30')->nullable();
                $table->integer('1b242_30')->nullable();
                $table->integer('wait30_30')->nullable();
                $table->integer('loss_30')->nullable();
                $table->integer('target_42')->nullable();
                $table->integer('result_42')->nullable();
                $table->integer('1b260_1_42')->nullable();
                $table->integer('1b261_42')->nullable();
                $table->integer('1b262_42')->nullable();
                $table->integer('follow_42')->nullable();
                $table->integer('1b260_2_42')->nullable();
                $table->integer('improper_42')->nullable();
                $table->integer('1b202_42')->nullable();
                $table->integer('1b212_42')->nullable();
                $table->integer('1b222_42')->nullable();
                $table->integer('1b232_42')->nullable();
                $table->integer('1b242_42')->nullable();
                $table->integer('wait30_42')->nullable();
                $table->integer('loss_42')->nullable();
                $table->integer('target_60')->nullable();
                $table->integer('result_60')->nullable();
                $table->integer('1b260_1_60')->nullable();
                $table->integer('1b261_60')->nullable();
                $table->integer('1b262_60')->nullable();
                $table->integer('follow_60')->nullable();
                $table->integer('1b260_2_60')->nullable();
                $table->integer('improper_60')->nullable();
                $table->integer('1b202_60')->nullable();
                $table->integer('1b212_60')->nullable();
                $table->integer('1b222_60')->nullable();
                $table->integer('1b232_60')->nullable();
                $table->integer('1b242_60')->nullable();
                $table->integer('wait30_60')->nullable();
                $table->integer('loss_60')->nullable();
                $table->integer('1b260_m_9');
                $table->integer('1b260_f_9');
                $table->integer('1b260_m_18');
                $table->integer('1b260_f_18');
                $table->integer('1b260_m_30');
                $table->integer('1b260_f_30');
                $table->integer('1b260_m_42');
                $table->integer('1b260_f_42');
                $table->integer('1b260_m_60');
                $table->integer('1b260_f_60');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['s_childdev_specialpp'];

        foreach ($tables as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
