<?php

namespace App\Http\Controllers;

use App\Models\Cubiculo;
use App\Models\User;
use App\Http\Requests\StoreCubiculoRequest; // <-- Lo usaremos
use App\Http\Requests\UpdateCubiculoRequest; // <-- Lo usaremos
use Illuminate\Http\Request; // <-- Agregada por si acaso, aunque no la usemos mucho
use Illuminate\Support\Facades\Auth;
use App\Models\OperatingArea;

class CubiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Obtenemos el usuario autenticado
    $user = Auth::user();

    // Verificamos si existe un usuario logueado para evitar errores
    if (!$user) {
        return redirect()->route('login');
    }

    // Cambiamos 'Super Admin' por el nombre exacto de tu rol
    if ($user->roles->pluck('name')->contains('Super Admin')) {
        $cubiculos = Cubiculo::all();
    } else {
        // Obtenemos las áreas del operador. 
        // Si la relación no existe, devolvemos un array vacío para que no explote
        $misAreasIds = $user->operatingAreas ? $user->operatingAreas->pluck('id')->toArray() : [];

        // Filtramos por la columna exacta de tu base de datos (id_area u operating_area_id)
        $cubiculos = Cubiculo::whereIn('operating_area_id', $misAreasIds)->get();
    }

    return view('cubiculos.index', compact('cubiculos'));
}

    /**
     * Show the form for creating a new resource.
     */
        public function create()
        {
            // 1. Cargamos los usuarios para el select de responsables
            $users = User::all();

            // 2. Cargamos las áreas con sus facultades
            $areas = OperatingArea::with('faculty')->get();

            // 3. Enviamos AMBAS variables en un solo compact
            return view('cubiculos.create', compact('users', 'areas'));
        }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCubiculoRequest $request)
    {
        // 1. Obtenemos los datos ya validados por el StoreCubiculoRequest
        $datosValidados = $request->validated();
        
        // 2. Combinamos para crear el nombre final
        $nombreFinal = $datosValidados['prefijo'] . $datosValidados['numero'];

        // 3. VALIDACIÓN DE DUPLICADOS (IMPORTANTE)
        $yaExiste = Cubiculo::where('nombre', $nombreFinal)->exists();
        
        if ($yaExiste) {
            // Volvemos atrás con un mensaje de error específico
            return back()->withErrors(['numero' => "El cubículo '$nombreFinal' ya existe. Verifique el número."])
                         ->withInput(); // withInput() para que no se borren los campos
        }

        // 4. Creamos el cubículo manualmente
        // (No podemos usar $request->validated() directamente porque 'nombre' no existe ahí)
        Cubiculo::create([
            'nombre' => $nombreFinal,
            'tipo_atencion' => $datosValidados['tipo_atencion'],
            'user_id' => $datosValidados['user_id'],
            'enlace_o_ubicacion' => $datosValidados['enlace_o_ubicacion'] ?? null,
        ]);
        
        return redirect()->route('cubiculos.index')->with('success', 'Cubículo creado exitosamente.');
    }

    
    /**
     * Display the specified resource.
     */
    public function show(Cubiculo $cubiculo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cubiculo $cubiculo)
    {
        $users = User::all();

        // --- LÓGICA NUEVA PARA SEPARAR EL NOMBRE ---
        // Esto es para rellenar los campos 'prefijo' y 'numero' en tu edit.blade.php
        $partes = explode('-', $cubiculo->nombre, 2);
        $prefijo = '';
        $numero = '';

        // Verificamos que tenga 2 partes y la segunda sea numérica (ej. C-101)
        if (count($partes) === 2 && ctype_digit($partes[1])) {
            $prefijo = $partes[0] . '-'; // 'C-'
            $numero  = $partes[1];      // '101'
        } else {
            // Si el nombre es antiguo o no tiene el formato (ej. "CRISTOFER")
            // Dejamos el prefijo vacío y ponemos el nombre raro en 'numero'
            // para que el usuario lo vea y corrija.
            $prefijo = ''; 
            $numero = $cubiculo->nombre;
        }
        // --- FIN LÓGICA NUEVA ---

        return view('cubiculos.edit', compact('cubiculo', 'users', 'prefijo', 'numero'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCubiculoRequest $request, Cubiculo $cubiculo)
    {
        // 1. Obtenemos los datos validados
        $datosValidados = $request->validated();
        
        // 2. Combinamos para crear el nombre final
        $nombreFinal = $datosValidados['prefijo'] . $datosValidados['numero'];

        // 3. VALIDACIÓN DE DUPLICADOS (Diferente al 'store')
        $yaExiste = Cubiculo::where('nombre', $nombreFinal)
                            ->where('id', '!=', $cubiculo->id) // <- Ignoramos el cubículo actual
                            ->exists();
        
        if ($yaExiste) {
            return back()->withErrors(['numero' => "El cubículo '$nombreFinal' ya existe."])->withInput();
        }

        // 4. Actualizamos manualmente
        $cubiculo->update([
            'nombre' => $nombreFinal,
            'tipo_atencion' => $datosValidados['tipo_atencion'],
            'user_id' => $datosValidados['user_id'],
            'enlace_o_ubicacion' => $datosValidados['enlace_o_ubicacion'] ?? null,
        ]);
        
        return redirect()->route('cubiculos.index')->with('success', 'Cubículo actualizado exitosamente.');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cubiculo $cubiculo)
    {
         $cubiculo->delete();
         return redirect()->route('cubiculos.index')->with('success', 'Cubículo eliminado correctamente.');
    }
}