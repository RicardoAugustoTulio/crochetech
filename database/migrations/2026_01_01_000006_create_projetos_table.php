<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('receita_id')->constrained()->cascadeOnDelete();
            $table->string('titulo', 150);
            $table->enum('status', ['nao_iniciado', 'em_andamento', 'concluido'])->default('nao_iniciado');
            $table->timestamp('data_conclusao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};
