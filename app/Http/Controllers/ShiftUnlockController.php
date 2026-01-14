<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShiftUnlock;
use App\Models\StudentRegistration;

class ShiftUnlockController extends Controller
{
    // Middleware de permisos
    public function __construct()
    {
        $this->middleware('can:desbloqueo.ver')->only('search');
        $this->middleware('can:desbloqueo.ejecutar')->only('unlock');
    }
    // Mostrar formulario de búsqueda
    public function index()
    {
        return view('shift_unlock.search');
    }

    // Buscar cédula en shifts
   public function search(Request $request)
{
    $request->validate([
        'cedula' => 'required|string|max:20',
    ]);

    // Buscar turno en shifts
    $shift = ShiftUnlock::where('person_shift', $request->cedula)->first();

    if (!$shift) {
        return redirect()->back()->with('error', 'No se encontró ningún registro con esa cédula.');
    }

    // Traer el estudiante (puede no existir)
    $student = StudentRegistration::where('cedula', $request->cedula)->first();

    return view('shift_unlock.result', [
        'shift' => $shift,
        'student' => $student
    ]);
}


    // Desbloquear estudiante
    public function unlock($cedula)
    {
        $student = StudentRegistration::where('cedula', $cedula)->first();

        if (!$student) {
            return redirect()->back()->with('error', 'No se encontró el estudiante.');
        }

        $student->tomado = 1; // esta parte depende
        $student->save();

        return redirect()->route('shift_unlock.search')->with('success', 'Estudiante desbloqueado correctamente.');
    }
}
