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
    $validator = Validator::make($request->all(), [
        'nombres' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'codigo' => 'required|string|max:255|unique:users',
        'telefono' => 'nullable|string|max:15',
        'password' => 'required|string|min:8|confirmed',
        'role_id' => 'required|exists:roles,id' // Verifica que el role_id exista en la tabla roles
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors();

        // Verifica errores específicos para mensajes personalizados
        if ($errors->has('email') && $errors->has('codigo')) {
            return response()->json(['message' => 'El correo y el código ya están registrados.'], 400);
        } elseif ($errors->has('email')) {
            return response()->json(['message' => 'El correo ya está registrado.'], 400);
        } elseif ($errors->has('codigo')) {
            return response()->json(['message' => 'Código ya registrado.'], 400);
        } elseif ($errors->has('password')) {
            // Verifica si el error de la contraseña es por longitud mínima
            if ($errors->first('password') === 'The password field must be at least 8 characters.') {
                return response()->json(['message' => 'La contraseña debe tener al menos 8 caracteres.'], 400);
            }
            // Verifica si el error de la contraseña es por confirmación
            if ($errors->first('password') === 'The password confirmation does not match.') {
                return response()->json(['message' => 'La confirmación de la contraseña no coincide.'], 400);
            }
        }

        // Respuesta general para otros errores de validación
        return response()->json(['message' => 'Validación fallida', 'errors' => $errors], 400);
    }

    // Creación del usuario en la base de datos
    $user = User::create([
        'nombres' => $request->nombres,
        'apellidos' => $request->apellidos,
        'email' => $request->email,
        'codigo' => $request->codigo,
        'telefono' => $request->telefono,
        'password' => Hash::make($request->password),
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

        $user = User::where('email', $request->email)->first();

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
                'role' => $user->role->role_name // Obtiene el nombre del rol relacionado
            ]
        ]);
    }
    public function forgotPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ]);

    $user = User::where('email', $request->email)->first();

    // Generar un código de verificación
    $verificationCode = Str::random(6);

    // Almacenar el código de verificación temporalmente
    $user->password_reset_code = $verificationCode;
    $user->save();

    // Enviar el código de verificación al correo del usuario
    Mail::raw("Su código de verificación es: $verificationCode", function($message) use ($user) {
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
            'email' => 'required|email|exists:users,email',
            'verification_code' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
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
