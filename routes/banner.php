<?php
Route::resource("banner","bannerController");
Route::post("banner/update","bannerController@update");
Route::get("banner/delete/{id}","bannerController@destroy");
Route::post("banner/update/state","bannerController@updateState");

Route::get("page/banner","bannerController@getBannerActivos");