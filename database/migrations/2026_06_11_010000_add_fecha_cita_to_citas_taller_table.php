<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas_taller', function (Blueprint $table) {
            // Fecha y hora elegidas por el cliente para llevar la moto.
            // Nullable para no romper las citas creadas antes de esta función.
            $table->dateTime('fecha_cita')->nullable()->after('matricula');
        });
    }

    public function down(): void
    {
        Schema::table('citas_taller', function (Blueprint $table) {
            $table->dropColumn('fecha_cita');
        });
    }
};
