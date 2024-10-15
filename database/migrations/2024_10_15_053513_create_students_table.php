<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();  // Identificador único
            $table->string('nombres');  // Nombre del estudiante
            $table->string('apellidos');  // Apellidos del estudiante
            $table->string('email')->unique();  // Correo electrónico único
            $table->string('codigo_estudiante')->unique();  // Número de carnet universitario único
            $table->string('telefono');  // Número de teléfono del estudiante
            $table->string('password');  // Contraseña
            $table->enum('status', ['active', 'inactive']);  // Estado del estudiante
            $table->timestamps();  // Timestamps para created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
