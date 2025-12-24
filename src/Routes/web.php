<?php

use atikullahnasar\role\Http\Controllers\RoleController;
use atikullahnasar\role\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('beft')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});
