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
        Schema::create('questao_opcoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_questao')->constrained('questoes')->onDelete('cascade');
            $table->char('letra', 1)->nullable(false);
            $table->string('opcao')->nullable(false);
            $table->boolean('opcao_correta')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questao_opcoes');
    }
};
