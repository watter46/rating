<?php declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\FixturesResource;
use App\UseCases\Fixture\FetchFixtureListUseCase;
use App\UseCases\Fixture\RegisterFixtureListUseCase;
use Exception;

class AdminFixtureController
{
    public function index(FetchFixtureListUseCase $fetchFixtureList, FixturesResource $resource)
    {
        try {
            $fixtures = $fetchFixtureList->execute();
            
            return view('admin.auth.dashboard', ['fixtures' => $resource->format($fixtures)]);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function update(RegisterFixtureListUseCase $registerFixtureList)
    {
        try {
            $registerFixtureList->execute();
        } catch (Exception $e) {

        }
    }
}