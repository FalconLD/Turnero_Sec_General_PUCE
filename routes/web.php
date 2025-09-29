<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/cubiculos', function () {
    return view('cubiculos.index'); // 👈 usa la carpeta y archivo que creaste
})->middleware('auth')->name('cubiculos.index');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


