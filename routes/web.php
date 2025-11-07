<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Controladores generales
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\CubiculoController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;

// Controladores de estudiantes y token
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\Auth\TokenLoginController;


// --- AUTENTICACIÓN NORMAL (usuarios administrativos, etc.) ---
Auth::routes();


// --- RUTAS PÚBLICAS (acceso con token PUCE para estudiantes) ---
// --- RUTAS PÚBLICAS ---
Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts'])->withoutMiddleware(['auth']);


// ✅ Login automático desde token
Route::get('/registro/{token}', [TokenLoginController::class, 'loginWithToken'])
    ->name('student.registro.token');
   

// ✅ Página de error si el token no es válido o expiró
Route::get('/registro/error', fn() => view('student.token_error'))
    ->name('student.token.error');

// ✅ Formulario de registro personal del estudiante
Route::get('/student/personal', [StudentRegistrationController::class, 'showPersonalForm'])
    ->name('student.personal');

// ✅ Guardar los datos del formulario
Route::post('/student/store', [StudentRegistrationController::class, 'store'])
    ->name('student.store');

// ✅ Página de términos y condiciones
Route::get('/student/terms', [StudentRegistrationController::class, 'terms'])
    ->name('student.terms');

// ✅ Finalizar registro (acepta términos)
Route::post('/student/finish', [StudentRegistrationController::class, 'finish'])
    ->name('student.finish');

// ✅ Página de éxito
Route::get('/student/success', [StudentRegistrationController::class, 'success'])
    ->name('student.success');


// --- RUTAS PROTEGIDAS (requieren login normal) ---
Route::middleware(['auth'])->group(function () {

    // Dashboard principal
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']);

    // Recursos principales
    Route::resource('asignacion', AsignacionController::class);
    Route::resource('cubiculos', CubiculoController::class);
    Route::resource('users', UserController::class);
    Route::resource('forms', FormController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('parameters', ParameterController::class);

    // Días del horario
    Route::get('/days/create/{schedule}', [DayController::class, 'create'])->name('days.create');
    Route::get('/days/{schedule}/edit', [DayController::class, 'edit'])->name('days.edit');
    Route::post('/days', [DayController::class, 'store'])->name('days.store');

    // Flujo de creación de horarios
    Route::get('schedules/{schedule}/select-days', [ScheduleController::class, 'selectDays'])
        ->name('schedules.selectDays');
    Route::post('schedules/{schedule}/store-days', [ScheduleController::class, 'storeDays'])
        ->name('schedules.storeDays');

    // Vistas estáticas
    Route::get('/encuesta', fn() => view('encuesta.index'))->name('encuesta.index');
    Route::get('/auditorias', fn() => view('auditoria.index'))->name('auditoria.index');

    // Turnos
    Route::get('/shifts/attention', [ShiftController::class, 'attention'])->name('shifts.attention');
    
});
