<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('receita_original_id')->nullable()->constrained('receitas')->nullOnDelete();
            $table->string('titulo', 150);
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['original', 'adaptada'])->default('original');
            $table->decimal('fator_adaptacao', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receitas');
    }
};
