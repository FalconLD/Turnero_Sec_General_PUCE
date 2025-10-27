<?php

// Importación de todos los controladores utilizados en este archivo.
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\CubiculoController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParameterController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\ShiftController;

// --- Rutas de Autenticación ---
// Esta única línea registra todas las rutas necesarias para la autenticación:
// login, logout, registro, olvido de contraseña, etc.
Auth::routes();

// --- Grupo de Rutas Protegidas por Autenticación ---
// Todas las rutas dentro de este grupo requerirán que el usuario haya iniciado sesión.
Route::middleware(['auth'])->group(function () {

    // --- Rutas Principales del Dashboard ---
    // Ruta para la página de inicio principal después de iniciar sesión.
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']); // Redirección para compatibilidad

    // --- Rutas de Recursos (CRUD) ---
    // Laravel genera automáticamente las rutas para Crear, Leer, Actualizar y Eliminar.
    // Por ejemplo, para 'cubiculos', crea: cubiculos.index, cubiculos.create, cubiculos.store, etc.
    Route::resource('asignacion', AsignacionController::class);
    Route::resource('cubiculos', CubiculoController::class);
    Route::resource('users', UserController::class);
    Route::resource('forms', FormController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('parameters', ParameterController::class);
   


Route::middleware(['auth'])->group(function () {
    Route::get('/days/create/{schedule}', [DayController::class, 'create'])->name('days.create');
    Route::get('/days/{schedule}/edit', [DayController::class, 'edit'])->name('days.edit');
    Route::post('/days', [DayController::class, 'store'])->name('days.store');
});


    // --- Rutas Específicas para Horarios (Schedules) ---
    // Estas son rutas adicionales para el controlador de horarios que no forman parte del CRUD estándar.
    // Se utilizan para el flujo de creación de horarios en varios pasos.
    Route::get('schedules/{schedule}/select-days', [ScheduleController::class, 'selectDays'])->name('schedules.selectDays');
    Route::post('schedules/{schedule}/store-days', [ScheduleController::class, 'storeDays'])->name('schedules.storeDays');

    // --- Rutas Estáticas (solo muestran una vista) ---
    // Aunque es mejor usar controladores, si solo necesitas mostrar una vista, esta es una forma limpia.
    // Se recomienda crear controladores para estas secciones si su lógica crece en el futuro.
    Route::get('/encuesta', function () {
        return view('encuesta.index');
    })->name('encuesta.index');

    Route::get('/auditorias', function () {
        return view('auditoria.index');
    })->name('auditoria.index');

  /* Antiguo */
       //Route::get('/student/terms', [StudentRegistrationController::class, 'showTerms'])->name('student.terms');
       // Route::post('/student/accept-terms', [StudentRegistrationController::class, 'acceptTerms'])->name('student.accept.terms');
        Route::get('/student/personal', [StudentRegistrationController::class, 'showPersonalForm'])->name('student.personal');
        Route::post('/student/store', [StudentRegistrationController::class, 'store'])->name('student.store');
        Route::get('/student/success', [StudentRegistrationController::class, 'success'])->name('student.success');


    Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts']);

});
