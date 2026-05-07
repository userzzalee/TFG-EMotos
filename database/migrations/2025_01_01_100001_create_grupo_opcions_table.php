<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_opcions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moto_id')->constrained('motos')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->boolean('obligatorio')->default(true);
            $table->unsignedTinyInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_opcions');
    }
};
