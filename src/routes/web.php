<?php declare(strict_types=1);

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('top');
});

Route::middleware('auth')->group(function () {
    Route::prefix('profile')
        ->name('profile.')
        ->group(function () {
            Route::get('/', [ProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
        });

    Route::prefix('fixtures')
        ->group(function () {
            Route::get('/', [FixtureController::class, 'index'])->name('fixtures');
            Route::get('/latest', [FixtureController::class, 'latest'])->name('fixtures.latest');
            Route::get('/{fixtureId}', [FixtureController::class, 'find'])->name('fixtures.find');
        });
});

require __DIR__.'/auth.php';

require __DIR__.'/admin.php';