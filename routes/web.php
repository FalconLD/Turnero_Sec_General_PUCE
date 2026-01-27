<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ==============================================================================
//  IMPORTACIÓN DE CONTROLADORES (ESTRUCTURA POR DOMINIOS)
// ==============================================================================

// 1. Dominio: Common (Utilidades y Vistas Neutras)
use App\Http\Controllers\Common\HomeController;
use App\Http\Controllers\Common\ProfileController;
use App\Http\Controllers\Common\DashboardController;

// 2. Dominio: Admin (Gestión, Configuración y Operación)
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
// Controladores movidos de Operator a Admin:
use App\Http\Controllers\Admin\AttentionController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\ShiftUnlockController;

// 3. Dominio: Student (Zona Pública y Registro)
use App\Http\Controllers\Student\TokenLoginController;
use App\Http\Controllers\Student\StudentRegistrationController;


// ==============================================================================
//  RUTAS DE INICIO Y AUTENTICACIÓN
// ==============================================================================

Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
});

Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

Auth::routes(['register' => false]);


// ==============================================================================
//  ZONA PÚBLICA (ESTUDIANTES Y API)
// ==============================================================================

// APIs de Turnos (Acceso público/estudiante)
Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts'])->name('api.shifts');

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
//  ZONA PROTEGIDA (ADMINISTRACIÓN / GESTIÓN INTERNA)
// ==============================================================================

Route::middleware(['auth'])->prefix('admin')->group(function () {

    // --- 0. DASHBOARD Y PERFIL ---
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

    // --- 4. MÓDULO DE ATENCIÓN (OPERACIÓN) ---
    Route::middleware(['can:atencion.ver_calendario'])->group(function () {
        Route::get('/attention', [AttentionController::class, 'index'])->name('attention.index');
        Route::get('/shifts/attention', [ShiftController::class, 'attention'])->name('shifts.attention');
    });

    // --- 5. MÓDULO DE DESBLOQUEO DE USUARIOS ---
    Route::middleware(['can:desbloqueo.ver'])->group(function () {
        Route::controller(ShiftUnlockController::class)->group(function () {
            Route::get('/shift-unlock', 'index')->name('shift_unlock.search');
            Route::post('/shift-unlock', 'search')->name('shift_unlock.search.post');
            Route::get('/shift-unlock/unlock/{cedula}', 'unlock')->name('shift_unlock.unlock');
        });
    });

    // --- 6. INFRAESTRUCTURA (CUBÍCULOS) ---
    Route::middleware(['can:cubiculos.ver'])->group(function () {
        Route::resource('modulos', CubiculoController::class)->names('cubiculos');
    });

    // --- 7. PLANIFICACIÓN (HORARIOS, DÍAS Y TURNOS) ---
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

    // --- 8. PARÁMETROS Y FORMULARIOS ---
    Route::middleware(['can:usuarios.ver'])->group(function () {
        Route::resource('parameters', ParameterController::class);
        Route::resource('forms', FormController::class);
    });

    // --- 9. REPORTES Y AUDITORÍA ---
    Route::middleware(['can:reportes.ver'])->group(function () {
        Route::get('/encuesta', fn() => view('common.encuesta.index'))->name('encuesta.index');
        Route::get('/auditorias', fn() => view('admin.auditoria.index'))->name('auditoria.index');
    });
});
