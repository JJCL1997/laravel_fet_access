<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitorsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id(); // ID único del visitante
            $table->string('nombres', 100); // Nombres del visitante
            $table->string('apellidos', 100); // Apellidos del visitante
            $table->string('identificacion', 50)->unique(); // Número de identificación único
            $table->string('motivo_visita', 255)->nullable(); // Razón de la visita (opcional)
            $table->string('telefono', 15)->nullable(); // Teléfono del visitante (opcional)
            $table->timestamps(); // Campos de timestamp: created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
}
