<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

// ==============================================================================
//  IMPORTACIÓN DE CONTROLADORES (ESTRUCTURA POR DOMINIOS)
// ==============================================================================

// 1. Dominio: Common (Utilidades y Vistas Neutras)
use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Common\ProfileController;
use App\Http\Controllers\Common\DashboardController;

// 2. Dominio: Admin (Gestión de Infraestructura y Configuración)
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CubiculoController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\DayController;
use App\Http\Controllers\Admin\ParameterController;
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\Admin\OperatingAreaController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\PaymentController;

// 3. Dominio: Operator (Atención y Flujo de Turnos)
use App\Http\Controllers\Operator\AttentionController;
use App\Http\Controllers\Operator\ShiftController;
use App\Http\Controllers\Operator\ShiftUnlockController;

// 4. Dominio: Student (Zona Pública y Registro)
use App\Http\Controllers\Student\TokenLoginController;
use App\Http\Controllers\Student\StudentRegistrationController;


// ==============================================================================
//  RUTAS DE INICIO Y AUTENTICACIÓN
// ==============================================================================

// Ruta Raíz: Redirige a Bienvenida o Login
Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

// Ruta de Bienvenida (Ventana Neutra)
Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

// Rutas de Auth estándar (Registro desactivado)
Auth::routes(['register' => false]);


// ==============================================================================
//  ZONA PÚBLICA (ESTUDIANTES Y API)
// ==============================================================================

// APIs de Turnos
Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts'])->name('api.shifts');
// Route::get('/shifts/{modalidad}/{fecha}', [ShiftController::class, 'getShiftsByModalidad'])->name('api.shifts.modalidad');

// Acceso mediante Token
Route::controller(TokenLoginController::class)->group(function () {
    Route::get('/registro/{token}', 'loginWithToken')->name('student.registro.token');
    Route::get('/registro/error', fn() => view('student.token_error'))->name('student.token.error');
});

// Flujo de Registro y Agendamiento del Estudiante
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
//  ZONA PROTEGIDA (ADMINISTRACIÓN / OPERACIÓN)
// ==============================================================================

Route::middleware(['auth'])->prefix('admin')->group(function () {

    // --- DASHBOARD Y PERFIL ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/perfil', 'edit')->name('profile.edit');
        Route::put('/perfil', 'update')->name('profile.update');
    });

    // --- 1. MÓDULO DE SEGURIDAD (Roles y Usuarios) ---
    Route::middleware(['can:roles.ver'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    Route::middleware(['can:usuarios.ver'])->group(function () {
        Route::resource('users', UserController::class);
    });

    // --- 2. CONFIGURACIÓN ACADÉMICA ---
    Route::middleware(['can:facultades.ver'])->group(function () {
        Route::resource('faculties', FacultyController::class)->names('faculties');
    });

    Route::middleware(['can:areas.ver'])->group(function () {
        Route::resource('operating-areas', OperatingAreaController::class)->names('operating-areas');
    });

    // --- 3. GESTIÓN DE OPERADORES Y ASIGNACIONES ---
    Route::middleware(['can:asignaciones.ver'])->group(function () {
        Route::controller(AssignmentController::class)->group(function () {
            Route::get('/assignments', 'index')->name('assignments.index');
            Route::get('/assignments/{user}/edit', 'edit')->name('assignments.edit');
            Route::put('/assignments/{user}', 'update')->name('assignments.update');
        });
        Route::resource('careers', CareerController::class)->names('careers');
    });

    // --- 4. MÓDULO DE ATENCIÓN (OPERADORES) ---
    Route::middleware(['can:atencion.ver_calendario'])->group(function () {
        Route::get('/attention', [AttentionController::class, 'index'])->name('attention.index');
        Route::get('/shifts/attention', [ShiftController::class, 'attention'])->name('shifts.attention');
    });

    // --- 6. MÓDULO DE DESBLOQUEO ---
    Route::middleware(['can:desbloqueo.ver'])->group(function () {
        Route::controller(ShiftUnlockController::class)->group(function () {
            Route::get('/shift-unlock', 'index')->name('shift_unlock.search');
            Route::post('/shift-unlock', 'search')->name('shift_unlock.search.post');
            Route::get('/shift-unlock/unlock/{cedula}', 'unlock')->name('shift_unlock.unlock');
        });
    });

    // --- 7. INFRAESTRUCTURA, HORARIOS Y DÍAS ---
    Route::middleware(['can:cubiculos.ver'])->group(function () {
        Route::resource('modulos', CubiculoController::class)->names('cubiculos');
    });

    Route::middleware(['can:horarios.ver'])->group(function () {
        Route::resource('schedules', ScheduleController::class);
        Route::post('/schedules/{id}/generate', [ScheduleController::class, 'generateShifts'])->name('schedules.generate');

        Route::resource('shifts', ShiftController::class);

        Route::controller(DayController::class)->group(function () {
            Route::get('/days/create/{schedule}', 'create')->name('days.create');
            Route::get('/days/{schedule}/edit', 'edit')->name('days.edit');
            Route::post('/days', 'store')->name('days.store');
        });
    });

    // --- 8. PARÁMETROS, FORMULARIOS Y REPORTES ---
    Route::middleware(['can:usuarios.ver'])->group(function () {
        Route::resource('parameters', ParameterController::class);
        Route::resource('forms', FormController::class);
    });

    Route::middleware(['can:reportes.ver'])->group(function () {
        Route::get('/encuesta', fn() => view('common.encuesta.index'))->name('encuesta.index');
        Route::get('/auditorias', fn() => view('auditoria.index'))->name('auditoria.index');
    });
});
