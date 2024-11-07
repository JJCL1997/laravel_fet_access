<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AccessLogController;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::get('/generate-visitor-qr', [QRController::class, 'generateVisitorRegistrationQrCode']);

Route::post('/visitors', [VisitorController::class, 'registerAndLogAccess']);
Route::post('/visitor/register', [VisitorController::class, 'registerAndLogAccess'])->name('visitor.registration.form');

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);


Route::post('/register', [AuthController::class, 'register']);



Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::patch('/users/{id}', [UserController::class, 'patchUpdate']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    // Route::post('/register', [AuthController::class, 'register']);
    Route::get('/visitors/{id}', [VisitorController::class, 'show']);
    Route::get('/visitors', [VisitorController::class, 'index']);
    Route::put('/visitors/{id}', [VisitorController::class, 'update']);
    Route::delete('/visitors/{id}', [VisitorController::class, 'destroy']);
    Route::patch('/visitors/{id}', [VisitorController::class, 'patchUpdate']);
    Route::get('/access-logs', [AccessLogController::class, 'index']);
    // Route::post('/access-logs', [AccessLogController::class, 'store']);
    Route::get('/access-logs/{id}', [AccessLogController::class, 'show']);
    // Route::put('/access-logs/{id}', [AccessLogController::class, 'update']);
    Route::delete('/access-logs/{id}', [AccessLogController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:student'])->group(function () {
    Route::get('/generate-qr', [QRController::class, 'generateQrCode']);
    Route::get('/student/profile', [UserController::class, 'showProfile']);
    Route::post('/student/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/student/update-password', [UserController::class, 'updatePassword']);
});

Route::middleware(['auth:sanctum', 'role:vigilant'])->group(function () {
    Route::post('/validate-qr', [QRController::class, 'validateQrCode']);
    Route::get('/check-access/{token}', [QRController::class, 'checkAccess']);
});
