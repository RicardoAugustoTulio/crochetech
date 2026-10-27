<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progressos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('projeto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('carreira_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('pontos_feitos')->default(0);
            $table->boolean('concluida')->default(false);
            $table->timestamp('concluida_em')->nullable();
            $table->timestamps();

            $table->unique(['projeto_id', 'carreira_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progressos');
    }
};
