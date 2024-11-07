<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validación de los datos del request
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'codigo' => 'required|string|max:255|unique:users',
            'telefono' => 'nullable|integer|digits_between:1,15',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validación fallida', 'errors' => $validator->errors()], 400);
        }



        // Creación del usuario en la base de datos
        $user = User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'codigo' => $request->codigo,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password), // Encriptar la contraseña
            'role_id' => $request->role_id
        ]);

        // Respuesta de éxito
        return response()->json(['message' => 'Usuario registrado exitosamente', 'user' => $user], 201);
    }


    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|string|email',
        'password' => 'required|string',
    ]);

    // Usar Eager Loading para cargar el rol
    $user = User::with('role')->where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Inicio de sesión exitoso',
        'token' => $token,
        'user' => [
            'nombres' => $user->nombres,
            'apellidos' => $user->apellidos,
            'email' => $user->email,
            'codigo' => $user->codigo,
            'telefono' => $user->telefono,
            'role' => $user->role ? $user->role->role_name : 'Sin rol' // Manejo de rol nulo
        ]
    ]);
}

public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => [
            'required',
            'email',
            'exists:users,email',
        ],
    ], [
        'email.required' => 'El campo correo electrónico es obligatorio.',
        'email.email' => 'El campo debe ser una dirección de correo electrónico válida.',
        'email.exists' => 'El correo electrónico ingresado no existe en nuestros registros.',
    ]);

    $user = User::where('email', $request->email)->first();

    // Generar un código de verificación
    $verificationCode = Str::random(6);

    // Almacenar el código de verificación temporalmente
    $user->password_reset_code = $verificationCode;
    $user->save();

    // Enviar el código de verificación al correo del usuario
    Mail::raw("Su código de verificación es: $verificationCode", function ($message) use ($user) {
        $message->to($user->email);
        $message->subject('Código de verificación para restablecer la contraseña');
    });

    return response()->json([
        'message' => 'Código de verificación enviado al correo.',
    ], 200);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'email' => [
            'required',
            'email',
            'exists:users,email',
        ],
        'verification_code' => [
            'required',
            'string',
        ],
        'new_password' => [
            'required',
            'string',
            'min:8',
            'confirmed',
        ],
    ], [
        'email.required' => 'El campo correo electrónico es obligatorio.',
        'email.email' => 'El campo debe ser una dirección de correo electrónico válida.',
        'email.exists' => 'El correo electrónico ingresado no existe en nuestros registros.',
        'verification_code.required' => 'El código de verificación es obligatorio.',
        'new_password.required' => 'La nueva contraseña es obligatoria.',
        'new_password.string' => 'La nueva contraseña debe ser una cadena de texto.',
        'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
        'new_password.confirmed' => 'La confirmación de la contraseña no coincide.',
    ]);

    $user = User::where('email', $request->email)->first();

    // Verificar el código de verificación
    if ($user->password_reset_code !== $request->verification_code) {
        return response()->json(['message' => 'Código de verificación incorrecto.'], 400);
    }

    // Actualizar la contraseña
    $user->password = Hash::make($request->new_password);
    $user->password_reset_code = null; // Limpiar el código de verificación
    $user->save();

    return response()->json(['message' => 'Contraseña actualizada exitosamente.'], 200);
}

public function logout(Request $request)
{
    // Revoca todos los tokens del usuario
    $request->user()->tokens()->delete();

    return response()->json(['message' => 'Cierre de sesión exitoso']);
}

}
