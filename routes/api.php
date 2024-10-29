<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', [AuthController::class, 'login']); 
Route::post('/register', [AuthController::class, 'register']); 
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']); // Obtener todos los usuarios (estudiantes y vigilantes)
    Route::get('/users/{id}', [UserController::class, 'show']); // Mostrar un usuario específico
    Route::post('/users}', [UserController::class, 'store']); 
    Route::put('/users/{id}', [UserController::class, 'update']); // Actualizar un usuario específico
    Route::patch('/users/{id}', [UserController::class, 'patchUpdate']); // Actualización parcial de un usuario
    Route::delete('/users/{id}', [UserController::class, 'destroy']); // Eliminar un usuario
});



Route::middleware(['auth:sanctum', 'role:student'])->get('/generate-qr', [QRController::class, 'generateQrCode']);
