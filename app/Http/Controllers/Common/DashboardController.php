<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Middleware de permisos
    public function __construct()
    {
        $this->middleware('can:reportes.ver')->only('index');
    }
    // Mostrar el dashboard con estadísticas
    public function index(Request $request)
    {
        $mes = $request->get('mes');
        $anio = $request->get('anio');

        // Años disponibles
        $aniosDisponibles = DB::table('shifts')
            ->select(DB::raw('YEAR(date_shift) as anio'))
            ->groupBy('anio')
            ->pluck('anio');

        // Total estudiantes
        $totalEstudiantes = DB::table('student_registrations')->count();

        // ---- Filtro de turnos ----
        $turnosQuery = DB::table('shifts');
        if ($anio) $turnosQuery->whereYear('date_shift', $anio);
        if ($mes)  $turnosQuery->whereMonth('date_shift', $mes);

        // Turnos por día
        $turnosPorDia = (clone $turnosQuery)
            ->select(DB::raw('CAST(date_shift AS DATE) as fecha'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('CAST(date_shift AS DATE)'))
            ->orderBy(DB::raw('CAST(date_shift AS DATE)'))
            ->get();

        // Turnos atendidos y pendientes
        $turnosAtendidos = (clone $turnosQuery)->whereNotNull('person_shift')->count();
        $turnosPendientes = (clone $turnosQuery)->whereNull('person_shift')->count();

        // ---- Pagos ----
        $pagosQuery = DB::table('student_registrations');
        if ($anio) $pagosQuery->whereYear('created_at', $anio);
        if ($mes)  $pagosQuery->whereMonth('created_at', $mes);

        $pagosPorForma = $pagosQuery
            ->select('forma_pago', DB::raw('COUNT(*) as total'))
            ->groupBy('forma_pago')
            ->get();

        // ---- Registros por cubículo y tipo de atención ----
        $cubiculosQuery = DB::table('shifts as s')
            ->join('cubiculos as c', 'c.id', '=', 's.cubicle_shift')
            ->select(
                'c.nombre',
                'c.tipo_atencion',
                DB::raw('COUNT(s.id_shift) as total')
            );

        if ($anio) $cubiculosQuery->whereYear('s.date_shift', $anio);
        if ($mes)  $cubiculosQuery->whereMonth('s.date_shift', $mes);

        $cubiculosQuery = $cubiculosQuery
            ->groupBy('c.nombre', 'c.tipo_atencion')
            ->orderBy('c.nombre')
            ->get();

        // ---- Retornar vista ----
        return view('dashboard.index', compact(
            'totalEstudiantes',
            'turnosPorDia',
            'turnosAtendidos',
            'turnosPendientes',
            'pagosPorForma',
            'cubiculosQuery',
            'aniosDisponibles',
            'mes',
            'anio'
        ));
    }
}
