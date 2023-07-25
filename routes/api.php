<?php

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

#JWT

Route::post('/login', 'App\Http\Controllers\API\AuthController@login');
Route::post('/register', 'App\Http\Controllers\API\AuthController@register');
Route::post('/logout', 'App\Http\Controllers\API\AuthController@logout');
Route::post('/refresh', 'App\Http\Controllers\API\AuthController@refresh');


#PROVEEDORES

Route::get('/proveedores', 'App\Http\Controllers\ModuloCompras\ProveedoresController@index');
Route::post('/proveedores/{id}', 'App\Http\Controllers\ModuloCompras\ProveedoresController@show');
Route::post('/proveedores', 'App\Http\Controllers\ModuloCompras\ProveedoresController@store');
Route::put('/proveedor/{proveedores}', 'App\Http\Controllers\ModuloCompras\ProveedoresController@update');
Route::post('/proveedor/estado/{id}', 'App\Http\Controllers\ModuloCompras\ProveedoresController@cambiarEstadoProveedor');

#FACTURAS

Route::get('/facturas', 'App\Http\Controllers\ModuloCompras\FacturasController@index');
Route::get('/factura/{id}', 'App\Http\Controllers\ModuloCompras\FacturasController@show');
Route::post('/factura', 'App\Http\Controllers\ModuloCompras\FacturasController@store');
Route::delete('/factura/{id}', 'App\Http\Controllers\ModuloCompras\FacturasController@destroy');
Route::put('/factura/{facturas}', 'App\Http\Controllers\ModuloCompras\FacturasController@update');
Route::post('/factura/estado/{id}', 'App\Http\Controllers\ModuloCompras\FacturasController@cambiarEstadoFactura');

#DETALLES

Route::get('/detalles', 'App\Http\Controllers\ModuloCompras\DetalleFacturasController@index');
Route::get('/detalles/{id}', 'App\Http\Controllers\ModuloCompras\DetalleFacturasController@show');

#AUDITORIA
Route::get('/auditoria', 'App\Http\Controllers\ModuloCompras\AuditoriasController@index');

#SEGURIDADES
Route::post('/compras/login', 'App\Http\Controllers\ModuloSeguridad\SeguridadController@login');