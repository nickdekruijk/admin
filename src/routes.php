<?php

// Set login and logout routes if not present and required
if (config('admin.auth_routes', true)) {
    Route::group(['middleware' => 'web'], function () {
        if (!Route::has('login')) {
            Route::get(config('admin.adminpath') . '/login', '\NickDeKruijk\Admin\Controllers\LoginController@showLoginForm')->name('login');
            Route::post(config('admin.adminpath') . '/login', '\NickDeKruijk\Admin\Controllers\LoginController@login');
        }
        if (!Route::has('logout')) {
            Route::post(config('admin.adminpath') . '/logout', '\NickDeKruijk\Admin\Controllers\LoginController@logout')->name('logout');
        }
    });
}

Route::group(['middleware' => ['web']], function () {
    Route::get(config('admin.adminpath') . '/all-js', 'NickDeKruijk\Admin\Controllers\AssetController@js');
    Route::get(config('admin.adminpath') . '/all-css', 'NickDeKruijk\Admin\Controllers\AssetController@css');
});
Route::group(['middleware' => ['web', 'auth']], function () {
    // Assets, this way we don't need to publish them to public

    Route::get(config('admin.adminpath'), 'NickDeKruijk\Admin\Controllers\BaseController@view');
    Route::get(config('admin.adminpath') . '/{slug}', 'NickDeKruijk\Admin\Controllers\BaseController@view');

    Route::get(config('admin.adminpath') . '/reports/{slug}/{id}', 'NickDeKruijk\Admin\Controllers\ReportController@show')->name('report');
    Route::get(config('admin.adminpath') . '/reports/{slug}/{id}/csv', 'NickDeKruijk\Admin\Controllers\ReportController@csv')->name('report_csv');

    Route::post(config('admin.adminpath') . '/media/{slug}/{folder}/folder', 'NickDeKruijk\Admin\Controllers\MediaController@newFolder');
    Route::delete(config('admin.adminpath') . '/media/{slug}/{folder}/folder', 'NickDeKruijk\Admin\Controllers\MediaController@destroyFolder');
    Route::get(config('admin.adminpath') . '/media/{slug}/{folder}', 'NickDeKruijk\Admin\Controllers\MediaController@show');
    Route::post(config('admin.adminpath') . '/media/{slug}/{folder}', 'NickDeKruijk\Admin\Controllers\MediaController@store');
    Route::patch(config('admin.adminpath') . '/media/{slug}/{folder}', 'NickDeKruijk\Admin\Controllers\MediaController@update');
    Route::delete(config('admin.adminpath') . '/media/{slug}/{folder}', 'NickDeKruijk\Admin\Controllers\MediaController@destroy');

    Route::get(config('admin.adminpath') . '/{slug}/{id}', 'NickDeKruijk\Admin\Controllers\ModelController@show');
    Route::post(config('admin.adminpath') . '/{slug}', 'NickDeKruijk\Admin\Controllers\ModelController@store');
    Route::patch(config('admin.adminpath') . '/{slug}/{id}/sort', 'NickDeKruijk\Admin\Controllers\ModelController@sort');
    Route::patch(config('admin.adminpath') . '/{slug}/{id}/changeparent', 'NickDeKruijk\Admin\Controllers\ModelController@changeParent');
    Route::patch(config('admin.adminpath') . '/{slug}/{id}', 'NickDeKruijk\Admin\Controllers\ModelController@update');
    Route::delete(config('admin.adminpath') . '/{slug}/{id}', 'NickDeKruijk\Admin\Controllers\ModelController@destroy');
    Route::get(config('admin.adminpath') . '/{slug}/{id}/download/{column}/{data}', 'NickDeKruijk\Admin\Controllers\ModelController@download');
});
