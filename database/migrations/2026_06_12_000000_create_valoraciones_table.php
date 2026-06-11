<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id();
            // Quién valora (comprador) y a quién valora (vendedor)
            $table->foreignId('autor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendedor_id')->constrained('users')->onDelete('cascade');
            // Anuncio sobre el que se realizó la venta
            $table->foreignId('anuncio_id')->nullable()->constrained('anuncios')->onDelete('set null');
            $table->unsignedTinyInteger('puntuacion'); // 1-5 estrellas
            $table->text('comentario')->nullable();
            $table->timestamps();

            // Un comprador solo puede valorar una vez cada anuncio.
            $table->unique(['autor_id', 'anuncio_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }
};
