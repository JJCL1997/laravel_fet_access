<?php
namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    /**
     * Mostrar todos los estudiantes.
     */
    public function index()
    {
        $students = Student::all();  // Obtener todos los estudiantes
        return response()->json($students);
    }

    /**
     * Crear un nuevo estudiante.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'codigo_estudiante' => 'required|string|max:255|unique:students',
            'telefono' => 'required|string|max:15',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Crear el estudiante
        $student = Student::create([
            'nombres' => $request->nombres,
            'apellidos' => $request->apellidos,
            'email' => $request->email,
            'codigo_estudiante' => $request->codigo_estudiante,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->password),
            'status' => 'active',  // Se puede modificar el estado según lo requieras
        ]);

        return response()->json([
            'message' => 'Estudiante registrado exitosamente',
            'student' => $student
        ], 201);
    }

    /**
     * Mostrar un estudiante específico.
     */
    public function show($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        return response()->json($student);
    }

    /**
     * Actualizar un estudiante existente.
     */
    public function update(Request $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'nombres' => 'sometimes|string|max:255',
            'apellidos' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:students,email,' . $id,
            'codigo_estudiante' => 'sometimes|string|max:255|unique:students,codigo_estudiante,' . $id,
            'telefono' => 'sometimes|string|max:15',
            'password' => 'sometimes|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Actualizar los datos del estudiante
        $student->update($request->only([
            'nombres',
            'apellidos',
            'email',
            'codigo_estudiante',
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
    $student = Student::find($id);

    if (!$student) {
        return response()->json(['message' => 'Estudiante no encontrado'], 404);
    }

    // Validar solo los campos que se envían en la solicitud
    $validator = Validator::make($request->all(), [
        'nombres' => 'sometimes|string|max:255',
        'apellidos' => 'sometimes|string|max:255',
        'email' => 'sometimes|string|email|max:255|unique:students,email,' . $id,
        'codigo_estudiante' => 'sometimes|string|max:255|unique:students,codigo_estudiante,' . $id,
        'telefono' => 'sometimes|string|max:15',
        'password' => 'sometimes|string|min:8'
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 400);
    }

    // Actualizar solo los campos que se proporcionan en la solicitud
    $student->update($request->only([
        'nombres',
        'apellidos',
        'email',
        'codigo_estudiante',
        'telefono',
    ]));

    // Si se incluye una nueva contraseña, actualizarla
    if ($request->filled('password')) {
        $student->password = Hash::make($request->password);
        $student->save();
    }

    return response()->json(['message' => 'Estudiante actualizado parcialmente', 'student' => $student]);
}


    /**
     * Eliminar un estudiante.
     */
    public function destroy($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado'], 404);
        }

        $student->delete();

        return response()->json(['message' => 'Estudiante eliminado exitosamente']);
    }
}
