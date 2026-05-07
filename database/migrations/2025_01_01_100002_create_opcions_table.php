<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opcions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_opcion_id')->constrained('grupo_opcions')->cascadeOnDelete();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_extra', 8, 2)->default(0);
            $table->string('imagen')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opcions');
    }
};
