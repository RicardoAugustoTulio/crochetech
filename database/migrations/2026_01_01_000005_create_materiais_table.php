<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receita_id')->constrained()->cascadeOnDelete();
            $table->string('nome', 100);
            $table->decimal('quantidade', 10, 2);
            $table->string('unidade', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiais');
    }
};
