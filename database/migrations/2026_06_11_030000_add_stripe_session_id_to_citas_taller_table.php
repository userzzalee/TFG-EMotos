<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas_taller', function (Blueprint $table) {
            // Guardamos el id de la sesión de Stripe Checkout que pagó la cita,
            // para auditoría y para evitar marcarla pagada dos veces.
            $table->string('stripe_session_id')->nullable()->after('coste');
        });
    }

    public function down(): void
    {
        Schema::table('citas_taller', function (Blueprint $table) {
            $table->dropColumn('stripe_session_id');
        });
    }
};
