<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers;
use App\Http\Controllers\categoriaController;
use App\Http\Controllers\postcontroller;
use App\Http\Controllers\Usercontroller;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//cargando clase
USE App\Http\Middleware\ApiAuthmiddelware;
//rutas de puebas
Route::get('/', function () {
    return view('welcome');
});

/* Route::get('/test-orm', [PruebasController::class, 'testorm']); */

//rutas de la api 
/* Route::get('/User', [Usercontroller::class, 'pruebas']);
Route::get('/post', [postcontroller::class, 'pruebasPOS']);
Route::get('/categorias', [categoriaController::class, 'pruebascat']); */




//rustas del controlador de usuario 
Route::POST('/Registro', [Usercontroller::class, 'registo']);
Route::POST('/Login', [Usercontroller::class, 'Login']);
Route::PUT('/user/update', [Usercontroller::class, 'update']);
Route::POST('/user/update/Avatar', [Usercontroller::class, 'upload'])->middleware(ApiAuthmiddelware::class);
Route::GET('/user/avatar/{filename}', [Usercontroller::class, 'GetImagen']);
Route::GET('/user/detail/{id}', [Usercontroller::class, 'detail']);

//rutas del controlador de categoris
Route::resource('/category', categoriaController::class);
//routas del post
Route::resource('/post', postcontroller::class);
Route::POST('/post/upload', [postcontroller::class, 'upload']);
Route::GET('/post/imagen/{filename}', [postcontroller::class, 'getimage']);
Route::GET('/post/categoria/{id}', [postcontroller::class, 'obtenerPostPorCategoria']);
Route::GET('/post/user/{id}', [postcontroller::class, 'obtenerPostPorUsuarios']);
