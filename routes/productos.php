<?php
Route::resource("productos","productoController");
Route::post("productos/update/state","productoController@updateState");
Route::post("productos/update/disponible","productoController@updateDisponible");
Route::get("producto/delete/{id}","productoController@destroy");



Route::get("producto/images/{id}","productoController@getImages");
Route::post("producto/images","productoController@saveImage");
Route::post("producto/images/delete","productoController@deleteImage");

Route::get("producto/images/movil/{id}","productoController@getImagesMovil");
Route::post("producto/images/movil","productoController@saveImageMovil");
Route::post("producto/images/movil/delete","productoController@deleteImageMovil");


Route::post("producto/update","productoController@update");
Route::post("producto/update/orden","productoController@updateOrden");

Route::post("producto/stock","productoController@save_stock");
Route::get("producto/{producto}/stock","productoController@get_stock");

Route::post("producto/delete/stock","productoController@deleteStock");

Route::post("producto/images/color","productoController@updateColor");
Route::post("producto/images/mobil/color","productoController@updateColorMobil");
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
Route::get("page/productos/","productoController@getProductosActivos");

