<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use Illuminate\Http\Request;

class AccessLogController extends Controller
{
    // Obtener todos los registros de acceso
    public function index()
    {
        return AccessLog::with(['user', 'visitor', 'role'])->get();
    }

    // Crear un nuevo registro de acceso
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'user_id' => 'nullable|exists:users,id',
    //         'visitor_id' => 'nullable|exists:visitors,id',
    //         'role_id' => 'required|exists:roles,id',
    //         'vehicle_type' => 'nullable|string|max:50',
    //         'vehicle_plate' => 'nullable|string|max:20'
    //     ]);

    //     $validated['access_time'] = now(); // Asigna el tiempo actual

    //     $accessLog = AccessLog::create($validated);

    //     return response()->json(['message' => 'Registro de acceso creado', 'accessLog' => $accessLog], 201);
    // }

    // Ver un registro de acceso específico
    public function show($id)
    {
        $accessLog = AccessLog::with(['user', 'visitor', 'role'])->find($id);

        if (!$accessLog) {
            return response()->json(['message' => 'Registro de acceso no encontrado'], 404);
        }

        return response()->json($accessLog, 200);
    }

    // Actualizar un registro de acceso
    // public function update(Request $request, $id)
    // {
    //     $accessLog = AccessLog::find($id);

    //     if (!$accessLog) {
    //         return response()->json(['message' => 'Registro de acceso no encontrado'], 404);
    //     }

    //     $validated = $request->validate([
    //         'user_id' => 'nullable|exists:users,id',
    //         'visitor_id' => 'nullable|exists:visitors,id',
    //         'role_id' => 'required|exists:roles,id',
    //         'vehicle_type' => 'nullable|string|max:50',
    //         'vehicle_plate' => 'nullable|string|max:20'
    //     ]);

    //     $accessLog->update($validated);

    //     return response()->json(['message' => 'Registro de acceso actualizado', 'accessLog' => $accessLog], 200);
    // }

    // Eliminar un registro de acceso
    public function destroy($id)
    {
        $accessLog = AccessLog::find($id);

        if (!$accessLog) {
            return response()->json(['message' => 'Registro de acceso no encontrado'], 404);
        }

        $accessLog->delete();

        return response()->json(['message' => 'Registro de acceso eliminado'], 200);
    }
}
