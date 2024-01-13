<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\UseCases\Fixture\FetchFixtureListUseCase;
use App\UseCases\Player\FetchFixtureUseCase;
use App\Http\Controllers\FixtureResource;


class FixtureController extends Controller
{    
    public function index(FetchFixtureListUseCase $fetchFixtureList, FixturesResource $resource)
    {
        try {
            $fixtures = $fetchFixtureList->execute();

            return view('fixtures', ['fixtures' => $resource->format($fixtures)]);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function find(string $fixtureId, FetchFixtureUseCase $fetchFixture, FixtureResource $resource)
    {
        try {
            $fixture = $fetchFixture->execute($fixtureId);

            return view('players', $resource->format($fixture));

        } catch (Exception $e) {
            dd($e);
        }
    }
}
