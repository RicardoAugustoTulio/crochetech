<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pontos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carreira_id')->constrained()->cascadeOnDelete();
            $table->string('tipo', 50);
            $table->unsignedInteger('quantidade');
            $table->text('instrucao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pontos');
    }
};
