<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    public function index()
{
    // Obtener todos los usuarios con rol de estudiante o vigilante
    $users = User::whereIn('role', ['student', 'vigilant'])->get();
    return response()->json($users);
}

    public function show($id)
{
    // Buscar el usuario por id que tenga rol de estudiante o vigilante
    $user = User::whereIn('role', ['student', 'vigilant'])->find($id);

    if (!$user) {
        return response()->json(['message' => 'Usuario no encontrado'], 404);
    }

    return response()->json($user);
}

    public function update(Request $request, $id)
    {
        $student = User::where('role', 'student')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
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

        $student->update($request->only([
            'nombres',
            'apellidos',
            'email',
            'codigo',
            'telefono',
        ]));

        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
            $student->save();
        }

        return response()->json(['message' => 'Estudiante actualizado exitosamente', 'student' => $student]);
    }

    public function patchUpdate(Request $request, $id)
    {
        $student = User::where('role', 'student')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
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

        $student->update($request->only([
            'nombres',
            'apellidos',
            'email',
            'codigo',
            'telefono',
        ]));

        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
            $student->save();
        }

        return response()->json(['message' => 'Estudiante actualizado parcialmente', 'student' => $student]);
    }

    public function destroy($id)
    {
        $student = User::where('role', 'student')->find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        $student->delete();

        return response()->json(['message' => 'Estudiante eliminado exitosamente']);
    }
}
