<?php

Route::group(['middleware' => ['web', 'auth']], function () {
    // Assets, this way we don't need to publish them to public
    Route::get(config('larapages.adminpath').'/all.js', 'LaraPages\Admin\Controllers\AssetController@js');
    Route::get(config('larapages.adminpath').'/all.css', 'LaraPages\Admin\Controllers\AssetController@css');

    Route::get(config('larapages.adminpath'), 'LaraPages\Admin\Controllers\BaseController@show');
    Route::get(config('larapages.adminpath').'/{slug}', 'LaraPages\Admin\Controllers\BaseController@show');
});
