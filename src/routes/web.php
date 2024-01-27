<?php declare(strict_types=1);

use App\Http\Controllers\Admin\AdminFixtureController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminPlayerController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('top');
});

//　ユーザーアカウント
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/latest', [FixtureController::class, 'latest'])->name('fixtures.latest');

    Route::get('/fixtures', [FixtureController::class, 'index'])->name('fixtures');
    Route::get('/fixtures/{fixtureId}', [FixtureController::class, 'find'])->name('fixtures.find');
});

// 管理者アカウント
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [AdminLoginController::class, 'showLoginPage'])->name('admin.login');
    Route::post('login', [AdminLoginController::class, 'login']);

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('fixtures', [AdminFixtureController::class, 'index'])->name('admin.fixtures');
        Route::get('players', [AdminPlayerController::class, 'index'])->name('admin.players');
    });
});

require __DIR__.'/auth.php';
