<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Auth;

class QRController extends Controller
{
    public function generateQrCode(Request $request)
{
    $user = Auth::user();

    // Verificar que el usuario tiene rol de estudiante
    if (!$user->role || $user->role->role_name !== 'student') {
        return response()->json(['message' => 'No tienes permiso para generar un código QR.'], 403);
    }

    // Validar los datos del vehículo
    $request->validate([
        'vehicle_type' => 'required|string|in:carro,moto,no_registra|max:50',
        'vehicle_plate' => 'nullable|string|max:7',
    ]);

    // Obtener el tipo de vehículo y la placa
    $vehicleType = $request->input('vehicle_type');
    $vehiclePlate = $request->input('vehicle_plate');

    // Lógica de validación según el tipo de vehículo
    if ($vehicleType === 'carro' || $vehicleType === 'moto') {
        if (empty($vehiclePlate)) {
            return response()->json(['message' => 'La placa es obligatoria para carro o moto.'], 422);
        }
    } elseif ($vehicleType === 'no_registra') {
        // Si el tipo de vehículo es "no_registra", se puede generar el QR sin placa
        $vehiclePlate = 'No registra';
    }

    // Generar un token único para el QR y guardarlo en el usuario
    $uniqueToken = bin2hex(random_bytes(8));
    $user->last_qr_token = $uniqueToken;
    $user->save();

    // Datos para el código QR, incluyendo la información del vehículo
    $data = "Nombre: {$user->nombres} {$user->apellidos}\nCódigo: {$user->codigo}\nEmail: {$user->email}\nToken: {$uniqueToken}\nVehículo: {$vehicleType}\nPlaca: {$vehiclePlate}";

    // Generar el QR
    $qrCode = Builder::create()
        ->data($data)
        ->size(300)
        ->margin(10)
        ->build();

    // Convertir el QR a base64
    $qrCodeBase64 = base64_encode($qrCode->getString());

    return response()->json([
        'qr_code' => "data:{$qrCode->getMimeType()};base64,{$qrCodeBase64}",
        'token' => $uniqueToken,
        'vehicle_type' => $vehicleType,
        'vehicle_plate' => $vehiclePlate
    ]);
}




public function validateQrCode(Request $request)
{
    $token = $request->input('token');
    $vehicleType = $request->input('vehicle_type');
    $vehiclePlate = $request->input('vehicle_plate');

    // Buscar al usuario por el token en el campo 'last_qr_token'
    $user = User::where('last_qr_token', $token)->with('role')->first();

    // Verificar si el usuario existe, tiene el rol adecuado y el token coincide
    if (!$user || !$user->role || $user->role->role_name !== 'student') {
        return response()->json(['message' => 'Usuario no encontrado o no autorizado.'], 403);
    }

    // Registrar el acceso en access_logs, incluyendo información adicional
    $accessLog = AccessLog::create([
        'user_id' => $user->id,
        'role_id' => $user->role_id,
        'user_name' => "{$user->nombres} {$user->apellidos}", // Nombre completo del usuario
        'user_email' => $user->email, // Correo del usuario
        'access_time' => now(),
        'vehicle_type' => $vehicleType,
        'vehicle_plate' => $vehiclePlate,
        'token' => $token,
    ]);

    return response()->json([
        'message' => 'Usuario autorizado y acceso registrado.',
        'access_log' => $accessLog
    ], 200);
}


public function generateVisitorRegistrationQrCode()
{
    // URL de la aplicación Ionic para redirigir a la página de registro
    $url = 'http://localhost:8100/visitor-registration';

    // Generar el código QR con la URL de registro
    $qrCode = Builder::create()
        ->data($url)
        ->size(300)
        ->margin(10)
        ->build();

    // Convertir el QR a base64
    $qrCodeBase64 = base64_encode($qrCode->getString());

    return response()->json([
        'qr_code' => "data:{$qrCode->getMimeType()};base64,{$qrCodeBase64}",
        'url' => $url,
    ]);
}





}
