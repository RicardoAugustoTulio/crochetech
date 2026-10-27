<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carreiras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receita_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('numero');
            $table->text('descricao')->nullable();
            $table->timestamps();

            $table->unique(['receita_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carreiras');
    }
};
