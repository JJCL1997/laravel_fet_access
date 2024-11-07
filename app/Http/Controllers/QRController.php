<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QRController extends Controller
{
    public function generateQrCode(Request $request)
{
    $user = Auth::user();

    if (!$user || !$user instanceof User) {
        return response()->json(['message' => 'Usuario no autenticado.'], 403);
    }

    if (!$user->role || $user->role->role_name !== 'student') {
        return response()->json(['message' => 'No tienes permiso para generar un código QR.'], 403);
    }

    // Validar datos del vehículo
    $request->validate([
        'vehicle_type' => 'required|string|in:carro,moto,no_registra|max:50',
        'vehicle_plate' => 'nullable|string|max:7',
    ]);

    $vehicleType = $request->input('vehicle_type');
    $vehiclePlate = $request->input('vehicle_plate');

    if (($vehicleType === 'carro' || $vehicleType === 'moto') && empty($vehiclePlate)) {
        return response()->json(['message' => 'La placa es obligatoria para carro o moto.'], 422);
    } elseif ($vehicleType === 'no_registra') {
        $vehiclePlate = 'No registra'; // Asegúrate de almacenar algo que puedas validar más tarde
    }

    $uniqueToken = bin2hex(random_bytes(8));
    $user->last_qr_token = $uniqueToken;
    $user->qr_token_expires_at = now()->addMinutes(60); // Establece la expiración en 60 minutos

    // Guarda el usuario
    if (!$user->save()) {
        return response()->json(['message' => 'Error al guardar el usuario.'], 500);
    }

    // Generar los datos en formato JSON para el QR
    $data = json_encode([
        "nombre" => "{$user->nombres} {$user->apellidos}",
        "codigo" => $user->codigo,
        "email" => $user->email,
        "token" => $uniqueToken,
        "vehiculo" => $vehicleType,
        "placa" => $vehiclePlate
    ]);

    // Crear el QR
    $qrCode = Builder::create()
        ->data($data)
        ->size(300)
        ->margin(10)
        ->build();

    $qrCodeBase64 = base64_encode($qrCode->getString());

    return response()->json([
        'qr_code' => "data:{$qrCode->getMimeType()};base64,{$qrCodeBase64}",
        'token' => $uniqueToken,
        'vehicle_type' => $vehicleType,
        'vehicle_plate' => $vehiclePlate,
        'email' => $user->email,
    ]);
}


public function validateQrCode(Request $request)
{
    // Validación de datos
    $validatedData = $request->validate([
        'token' => 'required|string',
        'email' => 'required|email',
        'vehicle_type' => 'nullable|string',
        'vehicle_plate' => 'nullable|string'
    ]);

    // Buscar el usuario y validar el token
    $user = User::where('email', $validatedData['email'])
                ->where('last_qr_token', $validatedData['token'])
                ->where('qr_token_expires_at', '>=', now())
                ->first();

    if (!$user) {
        return response()->json(['message' => 'Código QR no válido o expirado.'], 403);
    }

    // Verificar si ya existe un registro de acceso para este token
    $existingAccess = AccessLog::where('token', $validatedData['token'])->exists();
    if ($existingAccess) {
        return response()->json(['message' => 'Este código QR ya ha sido escaneado.'], 403);
    }

    // Registrar el acceso en access_logs
    try {
        $accessLog = new AccessLog();
        $accessLog->user_id = $user->id;
        $accessLog->role_id = $user->role_id; // Asigna role_id si es necesario
        $accessLog->vehicle_type = $validatedData['vehicle_type'] ?? null;
        // Solo se asigna la placa si el tipo de vehículo no es "no registra"
        $accessLog->vehicle_plate = ($validatedData['vehicle_type'] !== 'no_registra') ? $validatedData['vehicle_plate'] : null;
        $accessLog->access_time = now();
        $accessLog->token = $validatedData['token'];
        $accessLog->user_email = $validatedData['email']; // Almacena el email del usuario
        $accessLog->user_name = "{$user->nombres} {$user->apellidos}"; // Almacena el nombre del usuario

        // Guardar el acceso
        $accessLog->save();

        return response()->json(['message' => 'Acceso autorizado.']);
    } catch (\Exception $e) {
        // Registrar el error en los logs
        Log::error("Error al registrar el acceso: " . $e->getMessage());
        return response()->json(['message' => 'Error al registrar el acceso.'], 500);
    }
}




public function checkAccess($token)
{
    $exists = AccessLog::where('token', $token)->exists(); // Verifica si existe el registro

    return response()->json(['exists' => $exists]);
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
