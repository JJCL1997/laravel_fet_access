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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombres'); // Campo para nombres
            $table->string('apellidos'); // Campo para apellidos
            $table->string('email')->unique(); // Campo de correo electrónico único
            $table->string('codigo')->unique(); // Campo único para el código de usuario
            $table->string('telefono')->nullable(); // Campo opcional para teléfono
            $table->timestamp('email_verified_at')->nullable(); // Verificación de email
            $table->string('password'); // Contraseña
            $table->rememberToken(); // Token de recuerdo
            $table->timestamps(); // Timestamps created_at y updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
