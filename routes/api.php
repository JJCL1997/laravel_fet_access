<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

use App\Http\Controllers\StudentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register', [AuthController::class, 'register']);  // Ruta de registro
Route::post('/login', [AuthController::class, 'login']);  // Ruta de inicio de sesión
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');  // Ruta para cerrar sesión


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [StudentController::class, 'index']);  // Obtener todos los estudiantes
    Route::post('/students', [StudentController::class, 'store']);  // Crear un nuevo estudiante
    Route::get('/students/{id}', [StudentController::class, 'show']);  // Obtener un estudiante por ID
    Route::put('/students/{id}', [StudentController::class, 'update']);  // Actualizar un estudiante
    Route::patch('/students/{id}', [StudentController::class, 'patchUpdate']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);  // Eliminar un estudiante
});