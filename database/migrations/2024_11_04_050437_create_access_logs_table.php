<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade'); // Referencia al usuario
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->onDelete('cascade'); // Referencia al visitante
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Referencia al rol
            $table->timestamp('access_time')->useCurrent(); // Fecha y hora de acceso
            $table->string('vehicle_type', 50)->nullable(); // Tipo de vehículo
            $table->string('vehicle_plate', 7)->nullable(); // Placa del vehículo
            $table->timestamps();
        }); 
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
