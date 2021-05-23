<?php
Route::resource("coleccion","coleccionesController");
Route::post("coleccion/update","coleccionesController@update");
Route::get("coleccion/delete/{id}","coleccionesController@destroy");
Route::post("coleccion/update/state","coleccionesController@updateState");
Route::post("coleccion/update/orden","coleccionesController@updateOrden");
Route::get("coleccion/images/{id}","coleccionesController@getImages");
Route::post("coleccion/images","coleccionesController@saveImage");
Route::post("coleccion/images/delete","coleccionesController@deleteImage");

Route::get("page/colecciones/","coleccionesController@getColeccionesActivas");