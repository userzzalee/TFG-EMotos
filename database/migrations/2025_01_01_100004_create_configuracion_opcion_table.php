<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_opcion', function (Blueprint $table) {
            $table->foreignId('configuracion_id')->constrained('configuracions')->cascadeOnDelete();
            $table->foreignId('opcion_id')->constrained('opcions')->cascadeOnDelete();
            $table->primary(['configuracion_id', 'opcion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_opcion');
    }
};
