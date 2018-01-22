<?php

// Set login and logout routes if not present and required
if (config('larapages.auth_routes', true)) {
    Route::group(['middleware' => 'web'], function () {
        if (!Route::has('login')) {
            Route::get(config('larapages.adminpath').'/login', '\LaraPages\Admin\Controllers\LoginController@showLoginForm')->name('login');
            Route::post(config('larapages.adminpath').'/login', '\LaraPages\Admin\Controllers\LoginController@login');
        }
        if (!Route::has('logout')) {
            Route::post(config('larapages.adminpath').'/logout', '\LaraPages\Admin\Controllers\LoginController@logout')->name('logout');
        }
    });
}

Route::group(['middleware' => ['web']], function () {
    Route::get(config('larapages.adminpath').'/all.js', 'LaraPages\Admin\Controllers\AssetController@js');
    Route::get(config('larapages.adminpath').'/all.css', 'LaraPages\Admin\Controllers\AssetController@css');
});
Route::group(['middleware' => ['web', 'auth']], function () {
    // Assets, this way we don't need to publish them to public

    Route::get(config('larapages.adminpath'), 'LaraPages\Admin\Controllers\BaseController@view');
    Route::get(config('larapages.adminpath').'/{slug}', 'LaraPages\Admin\Controllers\BaseController@view');

    Route::get(config('larapages.adminpath').'/{slug}/{id}', 'LaraPages\Admin\Controllers\ModelController@show');
    Route::post(config('larapages.adminpath').'/{slug}', 'LaraPages\Admin\Controllers\ModelController@store');
    Route::patch(config('larapages.adminpath').'/{slug}/{id}/sort', 'LaraPages\Admin\Controllers\ModelController@sort');
    Route::patch(config('larapages.adminpath').'/{slug}/{id}/changeparent', 'LaraPages\Admin\Controllers\ModelController@changeParent');
    Route::patch(config('larapages.adminpath').'/{slug}/{id}', 'LaraPages\Admin\Controllers\ModelController@update');
    Route::delete(config('larapages.adminpath').'/{slug}/{id}', 'LaraPages\Admin\Controllers\ModelController@destroy');
});
