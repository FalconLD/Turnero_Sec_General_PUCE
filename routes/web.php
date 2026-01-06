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
    use App\Http\Controllers\ShiftUnlockController;
    use App\Http\Controllers\ProfileController;
    use App\Http\Controllers\AttentionController;
    use App\Http\Controllers\StudentRegistrationController;
    use App\Http\Controllers\Auth\TokenLoginController;
    use App\Http\Controllers\DashboardController;
    use App\Http\Controllers\Admin\PaymentController;
    use App\Http\Controllers\RoleController;


// --- Rutas de Autenticación ---
Auth::routes(['register' => false]); //quitar el registro de nuevos usuarios

// ==================================
//  ZONA PÚBLICA (ESTUDIANTES Y API)
// ==================================
// Estas rutas NO requieren estar logueado como administrativo

Route::get('/shifts/{fecha}', [ShiftController::class, 'getShifts'])
    ->name('api.shifts')->withoutMiddleware(['auth']);
Route::get('/shifts/{modalidad}/{fecha}', [ShiftController::class, 'getShiftsByModalidad'])
    ->name('api.shifts.modalidad')->withoutMiddleware(['auth']);

// Login y Registro de Estudiantes (Flow Token)
Route::get('/registro/{token}', [TokenLoginController::class, 'loginWithToken'])->name('student.registro.token');
Route::get('/registro/error', fn() => view('student.token_error'))->name('student.token.error');
Route::get('/student/personal', [StudentRegistrationController::class, 'showPersonalForm'])->name('student.personal');
Route::get('/student/agendamiento', [StudentRegistrationController::class, 'agendamiento'])->name('student.agendamiento');
Route::post('/student/store', [StudentRegistrationController::class, 'store'])->name('student.store');
Route::post('/student/finish', [StudentRegistrationController::class, 'finish'])->name('student.finish');
Route::get('/student/success', [StudentRegistrationController::class, 'success'])->name('student.success');
Route::post('/student/agendar-turno', [StudentRegistrationController::class, 'agendarTurno'])->name('student.agendarTurno');
Route::post('/student/turno/eliminar', [StudentRegistrationController::class, 'eliminarTurno'])->name('student.turno.eliminar');
Route::get('/student/logout', [StudentRegistrationController::class, 'studentLogout'])->name('student.logout');
Route::get('/get-faculties', [StudentRegistrationController::class, 'getFaculties'])->name('get.faculties');
Route::get('/get-programs', [StudentRegistrationController::class, 'getPrograms'])->name('get.programs');
Route::post('/validar-datos', [StudentRegistrationController::class, 'validarDatos'])->name('validar.datos');



// ==============================================
//  ZONA PROTEGIDA (ADMINISTRATIVOS y PSICOLOGOS)
// ===============================================
Route::middleware(['auth'])->group(function () {

    // --- ACCESO BÁSICO (Para todos los logueados) ---
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index']);

    // Perfil de Usuario
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');


    // --- 1. MÓDULO DE SEGURIDAD (Roles y Usuarios) ---
    // Protegido con: roles.ver y usuarios.ver
    Route::group(['middleware' => ['can:roles.ver']], function () {
        Route::resource('roles', RoleController::class);
    });

    Route::group(['middleware' => ['can:usuarios.ver']], function () {
        Route::resource('users', UserController::class);
        Route::resource('asignacion', AsignacionController::class); // Asumo que va con usuarios
    });


    // --- 2. MÓDULO DE ATENCIÓN ---
    // Protegido con: atencion.ver_calendario
    Route::group(['middleware' => ['can:atencion.ver_calendario']], function () {
        Route::get('/attention', [AttentionController::class, 'index'])->name('attention.index');
        Route::get('/shifts/attention', [ShiftController::class, 'attention'])->name('shifts.attention');
    });


    // --- 3. MÓDULO DE PAGOS (Recepción) ---
    // Protegido con: pagos.ver
    Route::group(['middleware' => ['can:pagos.ver']], function () {
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::get('/admin/payments/{payment}/ver', [PaymentController::class, 'verComprobante'])->name('payments.verComprobante');
        Route::get('/admin/payments/{payment}/descargar', [PaymentController::class, 'descargarComprobante'])->name('payments.descargarComprobante');
    });


    // --- 4. MÓDULO DE DESBLOQUEO DE USUARIO ---
    // Protegido con: desbloqueo.acceder
    Route::group(['middleware' => ['can:desbloqueo.acceder']], function () {
        Route::get('/shift-unlock', [ShiftUnlockController::class, 'index'])->name('shift_unlock.search');
        Route::post('/shift-unlock', [ShiftUnlockController::class, 'search'])->name('shift_unlock.search.post');
        Route::get('/shift-unlock/unlock/{cedula}', [ShiftUnlockController::class, 'unlock'])->name('shift_unlock.unlock');
    });


    // --- 5. MÓDULO DE CONFIGURACIÓN Y HORARIOS (Admin) ---
    // Protegido con: horarios.ver
    Route::group(['middleware' => ['can:horarios.ver']], function () {
        Route::resource('schedules', ScheduleController::class);
        Route::resource('shifts', ShiftController::class); // Gestión manual de turnos

        // Rutas específicas de horarios
        Route::get('/days/create/{schedule}', [DayController::class, 'create'])->name('days.create');
        Route::get('/days/{schedule}/edit', [DayController::class, 'edit'])->name('days.edit');
        Route::post('/days', [DayController::class, 'store'])->name('days.store');
        Route::get('schedules/{schedule}/select-days', [ScheduleController::class, 'selectDays'])->name('schedules.selectDays');
        Route::post('schedules/{schedule}/store-days', [ScheduleController::class, 'storeDays'])->name('schedules.storeDays');
    });


    // --- 6. MÓDULO DE INFRAESTRUCTURA (Cubículos) ---
    // Protegido con: cubiculos.ver
    Route::group(['middleware' => ['can:cubiculos.ver']], function () {
        Route::resource('cubiculos', CubiculoController::class);
    });


    // --- 7. MÓDULO DE PARÁMETROS (Configuración Global) ---
    // Protegido con: parametros.ver
    Route::group(['middleware' => ['can:parametros.ver']], function () {
        Route::resource('parameters', ParameterController::class);
        Route::resource('forms', FormController::class); // Formularios dinámicos
    });


    // --- 8. MÓDULO DE REPORTES (Dashboard) ---
    // Protegido con: reportes.ver
    Route::group(['middleware' => ['can:reportes.ver']], function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Vistas estáticas de reportes/auditoría
        Route::get('/encuesta', function () { return view('encuesta.index'); })->name('encuesta.index');
        Route::get('/auditorias', function () { return view('auditoria.index'); })->name('auditoria.index');
    });

});
