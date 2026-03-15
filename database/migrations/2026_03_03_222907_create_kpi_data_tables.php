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
        $tables = ['s_anc_quality', 's_kpi_anc12', 's_anc5'];

        foreach ($tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                // ใช้ string เพราะ id จาก API เป็น varchar(32) เช่น '1c1b8e24...'
                $table->string('id', 32)->primary(); 
                $table->string('hospcode', 5);
                $table->string('areacode', 8);
                $table->string('date_com', 14)->nullable();
                $table->string('b_year', 4);
                $table->integer('target')->nullable()->comment('รวมทั้งปีงบประมาณ B');
                $table->integer('result')->nullable()->comment('รวมทั้งปีงบประมาณ A');
                $table->integer('target1')->nullable();
                $table->integer('result1')->nullable();
                $table->integer('target2')->nullable();
                $table->integer('result2')->nullable();
                $table->integer('target3')->nullable();
                $table->integer('result3')->nullable();
                $table->integer('target4')->nullable();
                $table->integer('result4')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['s_anc_quality', 's_kpi_anc12', 's_anc5'];

        foreach ($tables as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
