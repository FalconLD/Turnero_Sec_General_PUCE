<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment; // <-- Modelo principal
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Muestra la lista de pagos pendientes y verificados.
     */
    public function index(Request $request)
    {
        // 1. Leer todas las variables de la URL con valores por defecto
        $currentStatus = $request->get('status', 'all');
        $perPage = $request->get('per_page', 10);
        $dateRange = $request->get('date_range', null);
        $search = $request->get('search', null);

        // 2. KPI Global: Total Verificado este mes (ignora filtros)
        $totalVerificadoMes = Payment::where('status', 'verified')
                                     ->whereMonth('verified_at', now()->month)
                                     ->whereYear('verified_at', now()->year)
                                     ->sum('amount');

        // 3. Consulta principal
        $query = Payment::with('studentRegistration', 'verifier')
                         ->orderBy('created_at', 'desc');

        // --- 4. APLICACIÓN DE FILTROS ---

        // Filtro por estado (de las Pestañas)
        if ($currentStatus !== 'all') {
            $query->where('status', $currentStatus);
        }
        
        // Filtro de Búsqueda
        if ($search) {
            $query->whereHas('studentRegistration', function ($q) use ($search) {
                $q->where('names', 'like', '%' . $search . '%')
                  ->orWhere('cedula', 'like', '%' . $search . '%');
            });
        }

        // Filtro de Rango de Fechas
        if ($dateRange) {
            try {
                // El picker envía un string "MM/DD/YYYY - MM/DD/YYYY"
                $dates = explode(' - ', $dateRange);
                // Usamos Carbon para parsear las fechas correctamente
                $startDate = Carbon::createFromFormat('m/d/Y', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', $dates[1])->endOfDay();
                
                // Filtramos por la fecha de CREACIÓN del pago
                $query->whereBetween('created_at', [$startDate, $endDate]);

            } catch (\Exception $e) {
                // Si el formato de fecha es inválido, simplemente ignora el filtro.
            }
        }
        
        // --- 5. CÁLCULO DE KPIs FILTRADOS ---
        // (Se calculan DESPUÉS de aplicar los filtros, pero ANTES de paginar)

        $totalPendiente = $query->clone()->where('status', 'pending')->sum('amount');
        $pagosPendientes = $query->clone()->where('status', 'pending')->count();
        $totalRegistros = $query->clone()->count();

        // --- 6. PAGINACIÓN ---
        $payments = $query->paginate($perPage)->withQueryString();

        // --- 7. ENVIAR DATOS A LA VISTA ---
        return view('payments.index', [
            'payments' => $payments,
            'currentStatus' => $currentStatus,
            'perPage' => $perPage, 
            'search' => $search,
            'dateRange' => $dateRange,
            // KPIs
            'totalVerificadoMes' => $totalVerificadoMes,
            'totalPendiente' => $totalPendiente,
            'pagosPendientes' => $pagosPendientes,
            'totalRegistros' => $totalRegistros,
        ]);
    }

    /**
     * Marca un registro de pago como 'verificado'.
     */
    public function verify(Payment $payment)
    {
        $payment->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', '¡Pago verificado exitosamente!');
    }

    /**
     * Marca un registro de pago como 'rechazado'.
     */
    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['rejection_reason' => 'required|string|min:5']);
        $payment->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return redirect()->back()->with('warning', 'Pago rechazado.');
    }
}
