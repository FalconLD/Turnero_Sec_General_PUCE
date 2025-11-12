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
use App\Http\Controllers\ShiftUnlockController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttentionController;
use App\Http\Controllers\PaymentController; 
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
    // Esta ruta mostrará la página de edición del perfil
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    
    // Esta ruta recibirá los datos del formulario y los guardará
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/attention', [AttentionController::class, 'index'])->name('attention.index');

    // Obtener facultades y programas para el formulario de registro de estudiantes
    Route::get('/get-faculties', [StudentRegistrationController::class, 'getFaculties'])->name('get.faculties');
    Route::get('/get-programs', [StudentRegistrationController::class, 'getPrograms'])->name('get.programs');

    //Ruta para el registro de la hora y fecha de estudiantes. 
    Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts'])->name('shifts.getAvailable');

    
    Route::get('payments', [App\Http\Controllers\Admin\PaymentController::class, 'index'])
     ->name('payments.index');
     
    // La ruta ahora recibe el ID del {payment}
    Route::post('payments/{payment}/verify', [App\Http\Controllers\Admin\PaymentController::class, 'verify'])
        ->name('payments.verify');

    Route::post('payments/{payment}/reject', [App\Http\Controllers\Admin\PaymentController::class, 'reject'])
        ->name('payments.reject');

    // --- Rutas de Recursos (CRUD) ---
    // Laravel genera automáticamente las rutas para Crear, Leer, Actualizar y Eliminar.
    // Por ejemplo, para 'cubiculos', crea: cubiculos.index, cubiculos.create, cubiculos.store, etc.
    Route::resource('asignacion', AsignacionController::class);
    Route::resource('cubiculos', CubiculoController::class);
    Route::resource('users', UserController::class);
    Route::resource('forms', FormController::class);
    Route::resource('schedules', ScheduleController::class);
    Route::resource('parameters', ParameterController::class);
    Route::resource('shifts', ShiftController::class);



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
      
    Route::get('/student/personal', [StudentRegistrationController::class, 'showPersonalForm'])->name('student.personal');
    Route::post('/student/store', [StudentRegistrationController::class, 'store'])->name('student.store');
    Route::get('/student/success', [StudentRegistrationController::class, 'success'])->name('student.success');
    
    Route::get('/shifts/attention', [ShiftController::class, 'attention'])->name('shifts.attention');
    //Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts']);
    //Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts'])->name('shifts.getAvailable');

    
    Route::post('/student/finish', [StudentRegistrationController::class, 'finish'])->name('student.finish');

        

    Route::get('/shift-unlock', [ShiftUnlockController::class, 'index'])->name('shift_unlock.search');
    Route::post('/shift-unlock', [ShiftUnlockController::class, 'search'])->name('shift_unlock.search.post');
    Route::get('/shift-unlock/unlock/{cedula}', [ShiftUnlockController::class, 'unlock'])->name('shift_unlock.unlock');

    Route::post('/validar-datos', [App\Http\Controllers\StudentRegistrationController::class, 'validarDatos'])->name('validar.datos');

 
});