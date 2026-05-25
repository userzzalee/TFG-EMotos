<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas_taller', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('marca');
            $table->string('modelo');
            $table->string('matricula');
            $table->text('problema');
            $table->text('comentarios')->nullable();
            $table->json('fotos')->nullable(); // array de rutas de imágenes
            // Estado: pendiente | aceptada | en_proceso | finalizada | pagada
            $table->string('estado')->default('pendiente');
            $table->text('comentario_mecanico')->nullable();
            $table->decimal('coste', 8, 2)->nullable();
            $table->foreignId('mecanico_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas_taller');
    }
};
