<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Obtener todos los usuarios con rol específico.
     */
    public function index()
{
    return response()->json(User::all());
}


    /**
     * Mostrar un usuario específico.
     */
    public function show($id)
{
    $user = User::find($id);

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    return response()->json($user, 200);
}


    /**
     * Actualizar un usuario específico.
     */
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
        ]));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json(['message' => 'Usuario actualizado exitosamente', 'user' => $user]);
    }

    /**
     * Actualización parcial de un usuario.
     */
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
            'password' => 'sometimes|string|min:8'
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
        ]));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json(['message' => 'Usuario actualizado parcialmente', 'user' => $user]);
    }

    /**
     * Eliminar un usuario específico.
     */
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
