<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Support\Facades\Auth;

class QRController extends Controller
{
    public function generateQrCode(Request $request)
{
    $user = Auth::user();

    if ($user->role !== 'student') {
        return response()->json(['message' => 'No tienes permiso para generar un código QR.'], 403);
    }

    $student = User::where('email', $user->email)->first();

    if (!$student) {
        return response()->json(['message' => 'Estudiante no encontrado'], 404);
    }

    // Genera un token único
    $uniqueToken = bin2hex(random_bytes(8)); // Token aleatorio de 16 caracteres

    // Guardar el token en la base de datos
    $student->last_qr_token = $uniqueToken;
    $student->save();

    $data = "Nombre: {$student->nombres} {$student->apellidos}\nCódigo: {$student->codigo}\nEmail: {$student->email}\nToken: {$uniqueToken}";

    $qrCode = Builder::create()
        ->data($data)
        ->size(300)
        ->margin(10)
        ->build();

    $qrCodeBase64 = base64_encode($qrCode->getString());

    return response()->json([
        'qr_code' => "data:{$qrCode->getMimeType()};base64,{$qrCodeBase64}"
    ]);
}


public function validateQrCode(Request $request)
{
    // Asumimos que el código QR incluye un token y el email del estudiante
    $token = $request->input('token');
    $email = $request->input('email');

    // Busca al usuario en la base de datos
    $user = User::where('email', $email)->first();

    if (!$user || $user->role !== 'student') {
        return response()->json(['message' => 'Usuario no encontrado o no autorizado.'], 403);
    }

    // Valida el token (puedes verificar si coincide con el último generado)
    if ($user->last_qr_token !== $token) {
        return response()->json(['message' => 'Código QR no válido o expirado.'], 403);
    }

    return response()->json(['message' => 'Usuario autorizado.'], 200);
}



    
}
