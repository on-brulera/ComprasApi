<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProveedoresController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

#PROVEEDORES - Get - Delete - Post

Route::resource('proveedores', ProveedoresController::class);

#rutas personalizadas

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