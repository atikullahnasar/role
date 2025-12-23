<?php

use atikullahnasar\role\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('beft')->group(function () {
    // Route::get('roles', [RoleController::class, 'index']);
    Route::resource('roles', RoleController::class);
});
