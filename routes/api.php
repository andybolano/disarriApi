<?php

use Illuminate\Http\Request;

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

Route::get("paises/","ubicacionController@getPaises");
Route::get("ciudades/{pais}","ubicacionController@getCiudades");
Route::get("departament/{ciudad}","ubicacionController@getDepartament");

include 'user.php';
include 'productos.php';
include 'blog.php';
include 'colecciones.php';
include 'authenticate.php';
include 'compra.php';
include 'banner.php';
include 'cliente.php';
include 'ventas.php';

