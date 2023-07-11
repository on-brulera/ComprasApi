<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedoresController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::controller(AuthController::class)->group(function () {
//     Route::post('login', 'login');
//     Route::post('register', 'register');
//     Route::post('logout', 'logout');
//     Route::post('refresh', 'refresh');
// });

#JWT
Route::post('/login', 'App\Http\Controllers\API\AuthController@login');
Route::post('/register', 'App\Http\Controllers\API\AuthController@register');
Route::post('/logout', 'App\Http\Controllers\API\AuthController@logout');
Route::post('/refresh', 'App\Http\Controllers\API\AuthController@refresh');


#PROVEEDORES - Get - Delete

Route::resource('proveedores', ProveedoresController::class);

#rutas personalizadas

Route::post('/proveedor', 'App\Http\Controllers\ProveedoresController@store');
Route::put('/proveedor/{proveedores}', 'App\Http\Controllers\ProveedoresController@update');

#FACTURAS

Route::get('/facturas', 'App\Http\Controllers\FacturasController@index');
Route::get('/factura/{id}', 'App\Http\Controllers\FacturasController@show');
Route::post('/factura', 'App\Http\Controllers\FacturasController@store');
Route::delete('/factura/{id}', 'App\Http\Controllers\FacturasController@destroy');
Route::put('/factura/{facturas}', 'App\Http\Controllers\FacturasController@update');
Route::put('/facturaestado/{facturas}', 'App\Http\Controllers\FacturasController@cambiarImpreso');

#DETALLES para Franco

Route::get('/detalles', 'App\Http\Controllers\DetalleFacturasController@index');
Route::get('/detalles/{id}', 'App\Http\Controllers\DetalleFacturasController@show');

#PARA AUDITORIA
Route::get('/auditoria', 'App\Http\Controllers\AuditoriasController@index');