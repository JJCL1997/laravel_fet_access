<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessLogsTable extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamp('access_time')->useCurrent();
            $table->string('user_name')->nullable();  // Almacena el nombre completo del usuario
            $table->string('user_email')->nullable(); // Almacena el correo del usuario
            $table->string('vehicle_type', 50)->nullable();
            $table->string('vehicle_plate', 7)->nullable();
            $table->string('token', 16)->nullable(); // Campo token

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
}
