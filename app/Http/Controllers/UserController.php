<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    // Método para ver la información del perfil del usuario (estudiante)
    public function showProfile()
    {
        $user = Auth::user();

        return response()->json([
            'nombres' => $user->nombres,
            'apellidos' => $user->apellidos,
            'email' => $user->email,
            'codigo' => $user->codigo,
            'telefono' => $user->telefono,
            'profile_photo' => $user->profile_photo ? asset('storage/' . $user->profile_photo) : null, // Asegúrate de que esto devuelve la URL completa
        ]);
    }

    // Método para actualizar la foto de perfil y el teléfono del usuario
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }



        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Validar los datos de entrada
        $validatedData = $request->validate([
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'telefono' => 'nullable|string|max:15',
        ]);

        // Actualizar la foto de perfil si se ha subido una nueva
        if ($request->hasFile('profile_photo')) {
            // Eliminar la foto anterior si existe
            if ($user->profile_photo) {
                Storage::delete($user->profile_photo);
            }
            // Almacenar la nueva foto y actualizar la propiedad en el usuario
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        // Actualizar el teléfono si se ha proporcionado
        if (!empty($validatedData['telefono'])) {
            $user->telefono = $validatedData['telefono'];
        }

        // Guardar los cambios en el usuario
        $user->save();

        return response()->json(['message' => 'Perfil actualizado exitosamente', 'user' => $user], 200);
    }

    // Método para actualizar la contraseña del usuario
    public function updatePassword(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        // Obtener el usuario autenticado
        $user = auth()->user();

        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        // Verificar si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 422);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($request->new_password);
        // Guardar los cambios en el usuario
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente.'], 200);
    }
}
