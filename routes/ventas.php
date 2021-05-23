<?php
Route::resource("ventas","ventasController");
Route::post("ventas/update/state","ventasController@updateState");
Route::get("ventas/{fechaInicial}/{fechaFinal}","ventasController@getByFechas");
Route::get("ventas/productos/{id}/get","ventasController@getProductosVentas");