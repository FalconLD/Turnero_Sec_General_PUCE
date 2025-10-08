<?php

use App\Http\Controllers\AsignacionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CubiculoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\ScheduleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
 
Route::get('/', function () {
    //return view('auth.login');
    return view('dashboard');
 
})->middleware('auth')->name('admin.home');
 
//Route::get('/cubiculos', function () {
   // return view('cubiculos.index'); // 👈 usa la carpeta y archivo que creaste
//})->middleware('auth')->name('cubiculos.index');
 
 
//Route::get('/forms', function () {
  //  return view('forms.index'); // 👈 usa la carpeta y archivo que creaste
//})->middleware('auth')->name('forms.index');
Auth::routes();
 
Route::get('/users', function () {
    return view('users.index'); // 👈 usa la carpeta y archivo que creaste
})->middleware('auth')->name('users.index');
Auth::routes();
 
 /*
Route::get('/horarios', function () {
    return view('horarios.index'); // 👈 usa la carpeta y archivo que creaste
})->middleware('auth')->name('horarios.index');
Auth::routes();*/
 
Route::get('/encuesta', function () {
    return view('encuesta.index'); // 👈 usa la carpeta y archivo que creaste
})->middleware('auth')->name('encuesta.index');
Auth::routes();

Route::get('/audtorias', function () {
    return view('auditoria.index'); // 👈 usa la carpeta y archivo que creaste
})->middleware('auth')->name('auditoria.index');
Auth::routes();

Route::resource('asignacion', AsignacionController::class);
    
Route::resource('cubiculos', CubiculoController::class);

Route::resource('users', UserController::class);
 
Route::resource('forms', FormController::class);

Route::resource('schedules', ScheduleController::class);

// Rutas para el segundo paso: seleccionar días
Route::get('schedules/{schedule}/select-days', [ScheduleController::class, 'selectDays'])->name('schedules.selectDays');
Route::post('schedules/{schedule}/store-days', [ScheduleController::class, 'storeDays'])->name('schedules.storeDays');
 



Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

