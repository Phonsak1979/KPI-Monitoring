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
        $tables = ['s_epi2'];

        foreach ($tables as $tableName) {
            Schema::create($tableName, function (Blueprint $table) {
                // ใช้ string เพราะ id จาก API เป็น varchar(32) เช่น '1c1b8e24...'
                $table->string('id', 32)->primary();
                $table->string('hospcode', 5);
                $table->string('areacode', 8);
                $table->string('date_com', 14)->nullable();
                $table->string('b_year', 4);
                $table->integer('target')->nullable();
                $table->integer('result')->nullable();
                $table->integer('target10')->nullable();
                $table->integer('target11')->nullable();
                $table->integer('target12')->nullable();
                $table->integer('target01')->nullable();
                $table->integer('target02')->nullable();
                $table->integer('target03')->nullable();
                $table->integer('target04')->nullable();
                $table->integer('target05')->nullable();
                $table->integer('target06')->nullable();
                $table->integer('target07')->nullable();
                $table->integer('target08')->nullable();
                $table->integer('target09')->nullable();
                $table->integer('dtp4_10')->nullable();
                $table->integer('opv4_10')->nullable();
                $table->integer('je2_10')->nullable();
                $table->integer('dtp4_11')->nullable();
                $table->integer('opv4_11')->nullable();
                $table->integer('je2_11')->nullable();
                $table->integer('dtp4_12')->nullable();
                $table->integer('opv4_12')->nullable();
                $table->integer('je2_12')->nullable();
                $table->integer('dtp4_01')->nullable();
                $table->integer('opv4_01')->nullable();
                $table->integer('je2_01')->nullable();
                $table->integer('dtp4_02')->nullable();
                $table->integer('opv4_02')->nullable();
                $table->integer('je2_02')->nullable();
                $table->integer('dtp4_03')->nullable();
                $table->integer('opv4_03')->nullable();
                $table->integer('je2_03')->nullable();
                $table->integer('dtp4_04')->nullable();
                $table->integer('opv4_04')->nullable();
                $table->integer('je2_04')->nullable();
                $table->integer('dtp4_05')->nullable();
                $table->integer('opv4_05')->nullable();
                $table->integer('je2_05')->nullable();
                $table->integer('dtp4_06')->nullable();
                $table->integer('opv4_06')->nullable();
                $table->integer('je2_06')->nullable();
                $table->integer('dtp4_07')->nullable();
                $table->integer('opv4_07')->nullable();
                $table->integer('je2_07')->nullable();
                $table->integer('dtp4_08')->nullable();
                $table->integer('opv4_08')->nullable();
                $table->integer('je2_08')->nullable();
                $table->integer('dtp4_09')->nullable();
                $table->integer('opv4_09')->nullable();
                $table->integer('je2_09')->nullable();
                $table->integer('mmr1_10')->nullable();
                $table->integer('mmr1_11')->nullable();
                $table->integer('mmr1_12')->nullable();
                $table->integer('mmr1_01')->nullable();
                $table->integer('mmr1_02')->nullable();
                $table->integer('mmr1_03')->nullable();
                $table->integer('mmr1_04')->nullable();
                $table->integer('mmr1_05')->nullable();
                $table->integer('mmr1_06')->nullable();
                $table->integer('mmr1_07')->nullable();
                $table->integer('mmr1_08')->nullable();
                $table->integer('mmr1_09')->nullable();
                $table->integer('mmr2_10')->nullable();
                $table->integer('mmr2_11')->nullable();
                $table->integer('mmr2_12')->nullable();
                $table->integer('mmr2_01')->nullable();
                $table->integer('mmr2_02')->nullable();
                $table->integer('mmr2_03')->nullable();
                $table->integer('mmr2_04')->nullable();
                $table->integer('mmr2_05')->nullable();
                $table->integer('mmr2_06')->nullable();
                $table->integer('mmr2_07')->nullable();
                $table->integer('mmr2_08')->nullable();
                $table->integer('mmr2_09')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = ['s_epi2'];

        foreach ($tables as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};
