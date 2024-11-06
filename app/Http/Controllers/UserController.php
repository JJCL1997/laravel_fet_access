<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        // Incluye el rol en la respuesta para cada usuario
        $users = User::with('role')->get();

        // Modifica la respuesta para incluir el nombre del rol
        $users = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'nombres' => $user->nombres,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'codigo' => $user->codigo,
                'telefono' => $user->telefono,
                'role' => $user->role->role_name, // Incluye el nombre del rol
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ];
        });

        return response()->json($users, 200);
    }

    public function show($id)
    {
        $user = User::with('role')->find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json([
            'id' => $user->id,
            'nombres' => $user->nombres,
            'apellidos' => $user->apellidos,
            'email' => $user->email,
            'codigo' => $user->codigo,
            'telefono' => $user->telefono,
            'role' => $user->role->role_name, // Incluye el nombre del rol
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombres' => 'sometimes|string|max:255',
            'apellidos' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'codigo' => 'sometimes|string|max:255|unique:users,codigo,' . $id,
            'telefono' => 'sometimes|string|max:15',
            'password' => 'sometimes|string|min:8',
            'role_id' => 'sometimes|exists:roles,id' // Valida el ID del rol
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user->update($request->only([
            'nombres',
            'apellidos',
            'email',
            'codigo',
            'telefono',
            'role_id', // Permite la actualización del rol
        ]));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user]);
    }

    public function patchUpdate(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombres' => 'sometimes|string|max:255',
            'apellidos' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $id,
            'codigo' => 'sometimes|string|max:255|unique:users,codigo,' . $id,
            'telefono' => 'sometimes|string|max:15',
            'password' => 'sometimes|string|min:8',
            'role_id' => 'sometimes|exists:roles,id' // Valida el ID del rol
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user->update($request->only([
            'nombres',
            'apellidos',
            'email',
            'codigo',
            'telefono',
            'role_id', // Permite la actualización del rol
        ]));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json(['message' => 'Usuario actualizado parcialmente', 'user' => $user]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'Usuario eliminado exitosamente']);
    }

    
}
