<?php

Route::group(['middleware' => ['web', 'auth']], function () {
    // Assets, this way we don't need to publish them to public
    Route::get(config('larapages.adminpath').'/all.js', 'NickDeKruijk\LaraPages\Controllers\AssetController@js');
    Route::get(config('larapages.adminpath').'/all.css', 'NickDeKruijk\LaraPages\Controllers\AssetController@css');

    Route::get(config('larapages.adminpath'), 'NickDeKruijk\LaraPages\Controllers\BaseController@show');
    Route::get(config('larapages.adminpath').'/{slug}', 'NickDeKruijk\LaraPages\Controllers\BaseController@show');
});
