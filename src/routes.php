<?php

use NickDeKruijk\Admin\Controllers\AdminController;
use NickDeKruijk\Admin\Controllers\AssetController;
use NickDeKruijk\Admin\Livewire\Login;

Route::group(['middleware' => 'web'], function () {
    Route::get(config('admin.route_prefix') . '/admin.css', [AssetController::class, 'stylesheets'])->name('admin.css');
    Route::get(config('admin.route_prefix') . '/login', Login::class)->name('admin.login');
    Route::post(config('admin.route_prefix') . '/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get(config('admin.route_prefix') . '/{slug?}', [AdminController::class, 'index'])->name('admin.index');
});
