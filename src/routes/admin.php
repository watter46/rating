<?php declare(strict_types=1);

use App\Http\Controllers\Admin\AdminFixtureController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminPlayerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin'], function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginPage'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('admin.logout');

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('fixtures', [AdminFixtureController::class, 'index'])->name('admin.fixtures');
        Route::get('players', [AdminPlayerController::class, 'index'])->name('admin.players');
    });
});