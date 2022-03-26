<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CartasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/registro',[UsersController::class,'registro']);
Route::post('/login',[UsersController::class,'login']);
Route::post('/recuperarContraseña',[UsersController::class,'recuperarContraseña']);
Route::post('/buscarCarta',[CartasController::class,'buscarCarta']);
Route::post('/buscarOferta',[CartasController::class,'buscarOferta']);

Route::middleware(['token','userpermission'])->prefix('cartas')->group(function(){
    Route::post('/subirCarta',[CartasController::class,'subirCarta']);
    Route::post('/subirColección',[CartasController::class,'subirColección']);
});

Route::middleware(['token','ofertaspermission'])->prefix('cartas')->group(function(){
    Route::post('/crearOferta',[CartasController::class,'crearOferta']);
});
