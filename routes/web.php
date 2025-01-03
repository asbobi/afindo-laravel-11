<?php

use Buki\AutoRoute\Facades\Route;

Route::get('login', 'App\Http\Controllers\Auth\LoginController@index')->name('login');
Route::post('proses_login', 'App\Http\Controllers\Auth\LoginController@proses_login')->name('proses_login');
Route::get('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::get('/', 'App\Http\Controllers\Admin\HomeController@index')->name('home');
Route::auto('test', 'App\Http\Controllers\TestController');
Route::middleware('auth')->group(function () {
    Route::auto('admin/home', 'App\Http\Controllers\Admin\HomeController');
    $routes = cache()->get('akses_user') ?? [];
    foreach ($routes as $row) {
        $method = explode("|", $row->Method ?? "auto");
        if ($row->Url != '') {
            foreach ($method as $met) {
                Route::$met($row->Slug, "$row->Url");
            }
        }
    }
});
