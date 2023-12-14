<?php declare(strict_types=1);

use App\Http\Controllers\FootApiController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Livewire\PlayerDetail;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/players', [PlayerController::class, 'index']);
Route::get('/players/detail', PlayerDetail::class);
// Route::get('/fixtures', [PlayerController::class, 'fetchSeasonFixtures']);
// Route::get('/statistic', [PlayerController::class, 'fetchStatistic']);
// Route::get('/squads', [PlayerController::class, 'fetchSquads']);
Route::get('/statistic', [PlayerController::class, 'statistic']);
Route::get('/fixtures', [PlayerController::class, 'fixtures']);
Route::get('/squads', [PlayerController::class, 'squads']);
Route::get('/starting', [PlayerController::class, 'startingXI']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
