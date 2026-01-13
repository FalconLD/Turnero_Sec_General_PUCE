<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// --- IMPORTACIÓN DE CONTROLADORES ---

// 1. Controladores de Autenticación y Perfil
use App\Http\Controllers\Auth\TokenLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

// 2. Controladores de Gestión (General)
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CubiculoController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\FormController;

// 3. Controladores de Operación
use App\Http\Controllers\AttentionController;
use App\Http\Controllers\ShiftUnlockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentRegistrationController;

// 4. Controladores en Sub-Namespace "Admin" (Estructura Académica y Pagos)
use App\Http\Controllers\Admin\OperatingAreaController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\CareerController;

// Ruta para activar la generación de turnos desde un Schedule (Horario)
Route::post('/schedules/{id}/generate', [App\Http\Controllers\ScheduleController::class, 'generate'])->name('schedules.generate');
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


// ==============================================================================
//  RUTAS DE INICIO Y AUTENTICACIÓN
// ==============================================================================

// 1. Ruta Raíz: Redirige a Bienvenida o Login
Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

// 2. Ruta de Bienvenida (Ventana Neutra): IMPORTANTE para evitar 404 tras Login
Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

// 3. Rutas de Auth estándar (Login/Logout)
Auth::routes(['register' => false]);

// ==============================================================================
//  ZONA PÚBLICA (ESTUDIANTES Y API)
// ==============================================================================
Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts'])->name('api.shifts');
Route::get('/shifts/{modalidad}/{fecha}', [ShiftController::class, 'getShiftsByModalidad'])->name('api.shifts.modalidad');

Route::controller(TokenLoginController::class)->group(function () {
    Route::get('/registro/{token}', 'loginWithToken')->name('student.registro.token');
    Route::get('/registro/error', fn() => view('student.token_error'))->name('student.token.error');
});

Route::controller(StudentRegistrationController::class)->prefix('student')->group(function () {
    Route::get('/personal', 'showPersonalForm')->name('student.personal');
    Route::get('/agendamiento', 'agendamiento')->name('student.agendamiento');
    Route::post('/store', 'store')->name('student.store');
    Route::post('/finish', 'finish')->name('student.finish');
    Route::get('/success', 'success')->name('student.success');
    Route::post('/agendar-turno', 'agendarTurno')->name('student.agendarTurno');
    Route::post('/turno/eliminar', 'eliminarTurno')->name('student.turno.eliminar');
    Route::get('/logout', 'studentLogout')->name('student.logout');
    Route::get('/faculties', 'getFaculties')->name('get.faculties');
    Route::get('/programs', 'getPrograms')->name('get.programs');
    Route::post('/validar-datos', 'validarDatos')->name('validar.datos');
});

// ==============================================================================
//  ZONA PROTEGIDA (ADMINISTRACIÓN / DASHBOARD)
// ==============================================================================
Route::middleware(['auth'])->prefix('admin')->group(function () {

    // --- DASHBOARD PRINCIPAL ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // Perfil de Usuario
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    // --- 1. MÓDULO DE SEGURIDAD (Roles y Usuarios) ---
    Route::middleware(['can:roles.ver'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::middleware(['can:usuarios.ver'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // --- 2. CONFIGURACIÓN TURNERO (Estructura Académica) ---
    Route::resource('faculties', FacultyController::class)->names('faculties');
    Route::resource('operating-areas', OperatingAreaController::class)->names('operating-areas');
    Route::resource('careers', CareerController::class)->names('careers');

    // --- 3. GESTIÓN DE OPERADORES (Asignaciones) ---
    Route::controller(AssignmentController::class)->group(function () {
        Route::get('/assignments', 'index')->name('assignments.index');
        Route::get('/assignments/{user}/edit', 'edit')->name('assignments.edit');
        Route::put('/assignments/{user}', 'update')->name('assignments.update');
    });

    // --- 4. MÓDULO DE ATENCIÓN Y TURNOS ---
    Route::middleware(['can:atencion.ver_calendario'])->group(function () {
        Route::get('/attention', [AttentionController::class, 'index'])->name('attention.index');
        Route::get('/shifts/attention', [ShiftController::class, 'attention'])->name('shifts.attention');
    });

    // --- 5. MÓDULO DE PAGOS (Recepción) ---
    Route::middleware(['can:pagos.ver'])->group(function () {
        Route::controller(PaymentController::class)->group(function () {
            Route::get('/payments', 'index')->name('payments.index');
            Route::post('/payments/{payment}/verify', 'verify');
            Route::post('/payments/{payment}/reject', 'reject');
            Route::get('/payments/{payment}/ver', 'verComprobante')->name('payments.verComprobante');
            Route::get('/payments/{payment}/descargar', 'descargarComprobante')->name('payments.descargarComprobante');
        });
    });

    // --- 6. MÓDULO DE DESBLOQUEO DE USUARIO ---
    Route::middleware(['can:desbloqueo.acceder'])->group(function () {
        Route::controller(ShiftUnlockController::class)->group(function () {
            Route::get('/shift-unlock', 'index')->name('shift_unlock.search');
            Route::post('/shift-unlock', 'search')->name('shift_unlock.search.post');
            Route::get('/shift-unlock/unlock/{cedula}', 'unlock')->name('shift_unlock.unlock');
        });
    });

    // --- 7. INFRAESTRUCTURA Y HORARIOS ---
    Route::middleware(['can:cubiculos.ver'])->group(function () {
        Route::resource('modulos', CubiculoController::class)->names('cubiculos');
    });

    Route::middleware(['can:horarios.ver'])->group(function () {
        Route::resource('schedules', ScheduleController::class);
        Route::resource('shifts', ShiftController::class);
        Route::controller(DayController::class)->group(function () {
            Route::get('/days/create/{schedule}', 'create')->name('days.create');
            Route::get('/days/{schedule}/edit', 'edit')->name('days.edit');
            Route::post('/days', 'store')->name('days.store');
        });
        Route::controller(ScheduleController::class)->group(function () {
            Route::get('schedules/{schedule}/select-days', 'selectDays')->name('schedules.selectDays');
            Route::post('schedules/{schedule}/store-days', 'storeDays')->name('schedules.storeDays');
        });
    });

    // --- 8. PARÁMETROS Y REPORTES ---
    Route::middleware(['can:parametros.ver'])->group(function () {
        Route::resource('parameters', ParameterController::class);
        Route::resource('forms', FormController::class);
    });

    Route::middleware(['can:reportes.ver'])->group(function () {
        Route::get('/encuesta', fn() => view('encuesta.index'))->name('encuesta.index');
        Route::get('/auditorias', fn() => view('auditoria.index'))->name('auditoria.index');
    });
});


Route::get('/fix-permissions', function () {
    // 1. Aseguramos que los permisos existan en la tabla 'permissions'
    Permission::firstOrCreate(['name' => 'cubiculos.ver']);
    Permission::firstOrCreate(['name' => 'horarios.ver']);

    // 2. Buscamos el rol Operador (si no existe, lo crea)
    $role = Role::firstOrCreate(['name' => 'Operador']);

    // 3. Le asignamos los permisos
    $role->givePermissionTo(['cubiculos.ver', 'horarios.ver']);

    return "Permisos asignados correctamente al rol Operador.";
});