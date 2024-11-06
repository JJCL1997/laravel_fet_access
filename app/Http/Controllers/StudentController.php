<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // Método para ver la información del perfil del estudiante
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




    // Método para actualizar la foto de perfil, teléfono o contraseña del estudiante
    public function updateProfile(Request $request)
{
    $user = Auth::user();

    $validatedData = $request->validate([
        'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'telefono' => 'nullable|string|max:15',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    // Actualizar la foto de perfil
    if ($request->hasFile('profile_photo')) {
        if ($user->profile_photo) {
            Storage::delete($user->profile_photo);
        }
        $path = $request->file('profile_photo')->store('profile_photos', 'public');
        $user->profile_photo = $path;
    }

    if (!empty($validatedData['telefono'])) {
        $user->telefono = $validatedData['telefono'];
    }
    if (!empty($validatedData['password'])) {
        $user->password = Hash::make($validatedData['password']);
    }

    $user->save();

    return response()->json(['message' => 'Perfil actualizado exitosamente', 'user' => $user], 200);
}

public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Verifica si la contraseña actual es correcta
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 422);
        }

        // Actualiza la contraseña
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'Contraseña actualizada correctamente.'], 200);
    }



}
