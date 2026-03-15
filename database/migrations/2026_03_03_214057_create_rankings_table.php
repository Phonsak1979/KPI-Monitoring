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
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->string('ranking_code');
            $table->string('ranking_name');
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->string('table_name');
            $table->string('hdc_link');
            $table->float('target_value');
            $table->float('score_5')->nullable();
            $table->float('score_4')->nullable();
            $table->float('score_3')->nullable();
            $table->float('score_2')->nullable();
            $table->float('score_1')->nullable();
            $table->string('score_1_operator')->nullable();
            $table->float('score_0')->nullable();
            $table->integer('rank');
            $table->float('weight');
            $table->float('score_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankings');
    }
};
