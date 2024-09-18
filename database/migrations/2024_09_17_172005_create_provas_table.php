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
        Schema::create('provas', function (Blueprint $table) {
            $table->id();
            $table->json('questoes');
            $table->boolean('finalizada')->default(false);
            $table->string('dificuldade');
            $table->integer('quantidade_questoes');
            $table->json('materias');
            $table->unsignedBigInteger('usuario');
            $table->timestamps();

            $table->foreign('usuario')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provas');
    }
};
