<?php declare(strict_types=1);

use App\Http\Controllers\Admin\AdminFixtureController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\FixtureController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Livewire\PlayerDetail;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

//　ユーザーアカウント
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  
    Route::get('/players', [PlayerController::class, 'index'])->name('players');
    Route::get('/players/detail', PlayerDetail::class);

    Route::get('/fixtures', [FixtureController::class, 'index'])->name('fixtures');
    Route::get('/fixtures/{fixtureId}', [FixtureController::class, 'find'])->name('fixtures.find');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');
}); 

// 管理者アカウント
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [AdminLoginController::class, 'showLoginPage'])->name('admin.login');
    Route::post('login', [AdminLoginController::class, 'login']);

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [AdminFixtureController::class, 'index'])->name('admin.dashboard');
        Route::get('dashboard/update', [AdminFixtureController::class, 'update']);
    });
});

Route::get('/rg', [PlayerController::class, 'register']);
Route::get('/rg2', [PlayerController::class, 'register2']);
Route::get('/rg3', [PlayerController::class, 'register3']);

require __DIR__.'/auth.php';
