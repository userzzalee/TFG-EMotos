<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vincula las conversaciones a un anuncio de segunda mano.
 *
 * Antes solo existía `producto_id` (merchandising) y los chats de segunda mano
 * se creaban con producto_id = null, por lo que dos personas que hablaban de
 * anuncios distintos compartían la misma conversación. Con `anuncio_id` cada
 * anuncio tiene su propia conversación.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversaciones', function (Blueprint $table) {
            $table->foreignId('anuncio_id')->nullable()->after('producto_id')
                  ->constrained('anuncios')->nullOnDelete();
        });

        // Sustituimos la clave única para que incluya el anuncio.
        Schema::table('conversaciones', function (Blueprint $table) {
            $table->dropUnique(['comprador_id', 'vendedor_id', 'producto_id']);
        });

        Schema::table('conversaciones', function (Blueprint $table) {
            $table->unique(['comprador_id', 'vendedor_id', 'producto_id', 'anuncio_id']);
        });
    }

    public function down(): void
    {
        Schema::table('conversaciones', function (Blueprint $table) {
            $table->dropUnique(['comprador_id', 'vendedor_id', 'producto_id', 'anuncio_id']);
        });

        Schema::table('conversaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('anuncio_id');
        });

        Schema::table('conversaciones', function (Blueprint $table) {
            $table->unique(['comprador_id', 'vendedor_id', 'producto_id']);
        });
    }
};
