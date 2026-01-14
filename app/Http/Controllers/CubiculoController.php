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
    // Middleware de permisos
    public function __construct()
    {
        $this->middleware('can:cubiculos.ver')->only('index');
        $this->middleware('can:cubiculos.crear')->only(['create', 'store']);
        $this->middleware('can:cubiculos.editar')->only(['edit', 'update']);
        $this->middleware('can:cubiculos.eliminar')->only('destroy');
    }
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
            'operating_area_id' => $request->operating_area_id, // Area operativa agregadada 
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
public function edit($id) // Cambiamos a $id para mayor seguridad con el nombre del parámetro
{
    // Buscamos el cubículo manualmente para asegurar que el ID llegue
    $cubiculo = Cubiculo::findOrFail($id);
    $users = User::all();
    
    // Impportante, para la seleccion de facultades 
    $areas = OperatingArea::with('faculty')->get();

    // Lógica de separar nombre (se mantiene la tuya)
    $partes = explode('-', $cubiculo->nombre, 2);
    $prefijo = (count($partes) === 2 && ctype_digit($partes[1])) ? $partes[0] . '-' : '';
    $numero  = (count($partes) === 2 && ctype_digit($partes[1])) ? $partes[1] : $cubiculo->nombre;

    // Pasamos 'areas' a la vista
    return view('cubiculos.edit', compact('cubiculo', 'users', 'areas', 'prefijo', 'numero'));
}
    /**
     * Update the specified resource in storage.
     */
public function update(UpdateCubiculoRequest $request, $id) // Usamos $id aquí también
{
    $cubiculo = Cubiculo::findOrFail($id);
    $datosValidados = $request->validated();
    $nombreFinal = $datosValidados['prefijo'] . $datosValidados['numero'];

    $yaExiste = Cubiculo::where('nombre', $nombreFinal)
                        ->where('id', '!=', $cubiculo->id)
                        ->exists();

    if ($yaExiste) {
        return back()->withErrors(['numero' => "El cubículo '$nombreFinal' ya existe."])->withInput();
    }

    $cubiculo->update([
        'nombre' => $nombreFinal,
        'tipo_atencion' => $datosValidados['tipo_atencion'],
        'user_id' => $datosValidados['user_id'],
        'enlace_o_ubicacion' => $datosValidados['enlace_o_ubicacion'] ?? null,
        'operating_area_id' => $request->operating_area_id, // <-- ¡No olvides actualizar el área operativa!
    ]);

    return redirect()->route('cubiculos.index')->with('success', 'Cubículo actualizado exitosamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) // Usamos $id para que sea genérico y no falle por el nombre del parámetro
    {
        $cubiculo = Cubiculo::findOrFail($id);
        $cubiculo->delete();

        return redirect()->route('cubiculos.index')->with('success', 'Cubículo eliminado correctamente.');
    }
}