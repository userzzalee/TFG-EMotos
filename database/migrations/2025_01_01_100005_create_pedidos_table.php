<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('configuracion_id')->nullable()->constrained('configuracions')->nullOnDelete();
            $table->string('numero_pedido', 20)->unique();
            $table->enum('estado', ['pendiente', 'confirmado', 'en_produccion', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            $table->decimal('precio_total', 10, 2);
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
