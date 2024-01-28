<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\FixturesResource;
use App\Http\Controllers\TournamentType;
use App\UseCases\Fixture\FetchFixtureListUseCase;
use App\UseCases\Fixture\RegisterFixtureListUseCase;
use Exception;
use Illuminate\Http\Request;

class AdminFixtureController
{
    public function index()
    {
        try {
            return view('admin.auth.fixtures');

        } catch (Exception $e) {
            dd($e);
        }
    }
}