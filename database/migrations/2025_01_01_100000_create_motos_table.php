<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('slug', 150)->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio_base', 10, 2);
            $table->string('imagen_principal')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motos');
    }
};
