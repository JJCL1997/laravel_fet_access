<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('email')->unique();
            $table->string('codigo')->unique();
            $table->string('telefono')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('last_qr_token')->nullable(); // Token para QR
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Referencia a la tabla roles
            $table->string('profile_photo')->nullable(); // Foto de perfil
            $table->string('password_reset_code', 6)->nullable(); // Código de restablecimiento de contraseña
            $table->timestamp('qr_token_expires_at')->nullable(); // Fecha de expiración del token QR
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
}
