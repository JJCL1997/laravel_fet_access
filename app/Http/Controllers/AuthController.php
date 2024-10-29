<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Registro de usuario
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'codigo' => 'required|string|max:255|unique:users',
            'telefono' => 'nullable|string|max:15',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:admin,student,vigilant'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            if ($errors->has('email') && $errors->has('codigo')) {
                return response()->json(['message' => 'El correo y  código ya están registrados.'], 400);
            } elseif ($errors->has('email')) {
                return response()->json(['message' => 'El correo ya está registrado.'], 400);
            } elseif ($errors->has('codigo')) {
                return response()->json(['message' => 'Código ya registrado.'], 400);
            }
            return response()->json(['message' => 'Validación fallida', 'errors' => $errors], 400);
        }

        $user = User::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'codigo' => $request->codigo,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return response()->json(['message' => 'Usuario registrado exitosamente', 'user' => $user], 201);
    }




    // Login de usuario
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
                'role' => $user->role
            ]
        ]);
    }

    // Logout de usuario
    public function logout(Request $request)
    {
        // Revoca todos los tokens del usuario
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso']);
    }
}
