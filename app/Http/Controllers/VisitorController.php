<?php

namespace App\Http\Controllers;

use App\Models\Visitor;
use App\Models\AccessLog;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    // Método para registrar el acceso de un visitante
    public function registerAndLogAccess(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'identificacion' => [
                    'required',
                    'string',
                    'unique:visitors,identificacion',
                ],
                'telefono' => 'nullable|string|max:15',
                'motivo_visita' => 'nullable|string',
                'vehicle_type' => 'nullable|string|max:50',
                'vehicle_plate' => 'nullable|string|max:7',
            ], [
                // Mensaje personalizado para identificación duplicada
                'identificacion.unique' => 'Ya existe un visitante registrado con esta identificación.',
            ]);
    
            // Registrar o encontrar al visitante
            $visitor = Visitor::updateOrCreate(
                ['identificacion' => $validated['identificacion']],
                [
                    'nombres' => $validated['nombres'],
                    'apellidos' => $validated['apellidos'],
                    'telefono' => $validated['telefono'] ?? null,
                    'motivo_visita' => $validated['motivo_visita'] ?? null,
                ]
            );
    
            // Registrar el acceso
            $accessLog = AccessLog::create([
                'visitor_id' => $visitor->id,
                'role_id' => 3,
                'user_name' => "{$visitor->nombres} {$visitor->apellidos}",
                'user_email' => 'N/A',
                'access_time' => now(),
                'vehicle_type' => $validated['vehicle_type'] ?? null,
                'vehicle_plate' => $validated['vehicle_plate'] ?? null,
            ]);
    
            return response()->json([
                'message' => 'Visitante y acceso registrados exitosamente.',
                'visitor' => $visitor,
                'access_log' => $accessLog,
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
            ], 422);
        }
    }
    

    public function showRegistrationForm()
{
    return view('visitor.register'); // Esto muestra la vista con el formulario
}


    public function index()
    {
        // Obtener todos los visitantes junto con su último registro de acceso
        $visitors = Visitor::with('latestAccessLog')->get();
        return response()->json($visitors, 200);
    }
    
    public function show($id)
{
    $visitor = Visitor::with(['latestAccessLog' => function ($query) {
        $query->select('log_id', 'visitor_id', 'vehicle_type', 'vehicle_plate', 'access_time'); // Seleccionar solo los campos necesarios
    }])->find($id);

    if (!$visitor) {
        return response()->json(['message' => 'Visitante no encontrado'], 404);
    }

    return response()->json($visitor, 200);
}


    public function update(Request $request, $id)
    {
        $visitor = Visitor::find($id);
    
        if (!$visitor) {
            return response()->json(['message' => 'Visitante no encontrado'], 404);
        }
    
        $validated = $request->validate([
            'nombres' => 'sometimes|string|max:255',
            'apellidos' => 'sometimes|string|max:255',
            'identificacion' => 'sometimes|string|unique:visitors,identificacion,' . $id,
            'telefono' => 'nullable|string|max:15',
            'motivo_visita' => 'nullable|string',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_plate' => 'nullable|string|max:7',
        ]);
    
        // Actualizar los datos del visitante
        $visitor->update($validated);
    
        // Actualizar los datos de acceso del último log, incluyendo `user_name`
        if ($visitor->latestAccessLog) {
            $visitor->latestAccessLog()->update([
                'vehicle_type' => $validated['vehicle_type'] ?? $visitor->latestAccessLog->vehicle_type,
                'vehicle_plate' => $validated['vehicle_plate'] ?? $visitor->latestAccessLog->vehicle_plate,
                'user_name' => "{$visitor->nombres} {$visitor->apellidos}", // Actualiza el nombre completo
            ]);
        }
    
        return response()->json(['message' => 'Visitante y datos de acceso actualizados exitosamente', 'visitor' => $visitor], 200);
    }
    
    
    public function patchUpdate(Request $request, $id)
    {
        $visitor = Visitor::find($id);
    
        if (!$visitor) {
            return response()->json(['message' => 'Visitante no encontrado'], 404);
        }
    
        $validated = $request->validate([
            'nombres' => 'sometimes|string|max:255',
            'apellidos' => 'sometimes|string|max:255',
            'identificacion' => 'sometimes|string|unique:visitors,identificacion,' . $id,
            'telefono' => 'nullable|string|max:15',
            'motivo_visita' => 'nullable|string',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_plate' => 'nullable|string|max:7',
        ]);
    
        // Actualizar los datos del visitante
        $visitor->update($validated);
    
        // Actualizar los datos de acceso del último log, incluyendo `user_name`
        if ($visitor->latestAccessLog) {
            $visitor->latestAccessLog()->update([
                'vehicle_type' => $validated['vehicle_type'] ?? $visitor->latestAccessLog->vehicle_type,
                'vehicle_plate' => $validated['vehicle_plate'] ?? $visitor->latestAccessLog->vehicle_plate,
                'user_name' => "{$visitor->nombres} {$visitor->apellidos}", // Actualiza el nombre completo
            ]);
        }
    
        return response()->json(['message' => 'Visitante y datos de acceso actualizados parcialmente', 'visitor' => $visitor], 200);
    }
    
    // Eliminar un visitante
    public function destroy($id)
    {
        $visitor = Visitor::find($id);

        if (!$visitor) {
            return response()->json(['message' => 'Visitante no encontrado'], 404);
        }

        $visitor->delete();

        return response()->json(['message' => 'Visitante eliminado exitosamente'], 200);
    }
}
