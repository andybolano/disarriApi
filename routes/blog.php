<?php
Route::get("blog","blogController@index");
Route::post("blog","blogController@store");
Route::post("blog/update","blogController@update");
Route::get("blog/delete/{id}","blogController@destroy");
Route::post("blog/update/state","blogController@updateState");

Route::get("page/blog","blogController@getBlogActivos");
