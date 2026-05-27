<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comprador_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendedor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('set null');
            $table->timestamp('ultimo_mensaje_at')->nullable();
            $table->timestamps();

            // Evitar conversaciones duplicadas por el mismo producto
            $table->unique(['comprador_id', 'vendedor_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversaciones');
    }
};
