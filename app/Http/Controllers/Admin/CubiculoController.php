<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    $user = Auth::user();

    if ($user->roles->pluck('name')->contains('Super Admin')) {
        $cubiculos = Cubiculo::with('users')->get();
    } else {
        // Obtenemos los IDs de las áreas de Belén (Medicina)
        $misAreasIds = $user->operatingAreas->pluck('id')->toArray();

        // FILTRO CRÍTICO: Solo cubículos que pertenezcan a esas áreas
        $cubiculos = Cubiculo::with('users')
            ->whereIn('operating_area_id', $misAreasIds)
            ->get();
    }

    return view('admin.cubiculos.index', compact('cubiculos'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cargamos usuarios con sus áreas asignadas para el filtro dinámico
        $users = User::with('operatingAreas')->get();

        // El Super Usuario necesita ver esto inicialmente vacío o con todas las áreas
        $areas = OperatingArea::all();

        return view('admin.cubiculos.create', compact('users', 'areas'));
    }
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

        return redirect()->route('cubiculos.index')->with('info', 'Cubículo creado exitosamente.');
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
    public function edit($id)
    {
        // 1. Buscamos el cubículo por ID
        $cubiculo = Cubiculo::findOrFail($id);
        $users = User::with('operatingAreas')->get(); // Importante cargar las relaciones
        $user = Auth::user();

        // 2. SEGURIDAD: Si no es Super Admin, verificamos que el área le pertenezca
        if (!$user->roles->pluck('name')->contains('Super Admin')) {
            // Obtenemos los IDs de las áreas asignadas al usuario en la tabla pivote
            $misAreasIds = $user->operatingAreas->pluck('id')->toArray();

            // Si el cubículo que intenta editar no es de su área, bloqueamos el acceso
            if (!in_array($cubiculo->operating_area_id, $misAreasIds)) {
                abort(403, 'No tienes permiso para editar cubículos de otras facultades.');
            }
        }

        // 3. Cargamos los datos necesarios para los selects del formulario
        $users = User::all();
        $areas = OperatingArea::with('faculty')->get();

        // 4. Lógica para separar el nombre (Prefijo y Número)
        $partes = explode('-', $cubiculo->nombre, 2);
        $prefijo = (count($partes) === 2) ? $partes[0] . '-' : '';
        $numero  = (count($partes) === 2) ? $partes[1] : $cubiculo->nombre;

        // 5. Retornamos la vista con todas las variables necesarias
        return view('admin.cubiculos.edit', compact('cubiculo', 'users', 'areas', 'prefijo', 'numero'));
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

    return redirect()->route('cubiculos.index')->with('info', 'Cubículo actualizado exitosamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) // Usamos $id para que sea genérico y no falle por el nombre del parámetro
    {
        $cubiculo = Cubiculo::findOrFail($id);
        $cubiculo->delete();

        return redirect()->route('cubiculos.index')->with('info', 'Cubículo eliminado correctamente.');
    }
}
