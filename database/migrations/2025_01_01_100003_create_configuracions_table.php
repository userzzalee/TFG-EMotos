<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('moto_id')->constrained('motos')->cascadeOnDelete();
            $table->string('nombre', 150)->nullable();
            $table->decimal('precio_total', 10, 2);
            $table->enum('estado', ['borrador', 'guardada', 'pedido_realizado'])->default('borrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracions');
    }
};
