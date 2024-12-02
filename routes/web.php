<?php

//use Illuminate\Support\Facades\Route;
use Buki\AutoRoute\Facades\Route;

Route::get('login', 'App\Http\Controllers\Auth\LoginController@index')->name('login');
Route::get('admin/home', 'App\Http\Controllers\Admin\HomeController@index');
Route::auto('admin/home', 'App\Http\Controllers\Admin\HomeController');
Route::auto('test', 'App\Http\Controllers\TestController');
Route::get('/', function () {
    return view('welcome');
});
