<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\Http\Controllers\FixtureResource;
use App\UseCases\Fixture\FetchFixtureUseCase;
use App\UseCases\Fixture\FetchLatestUseCase;


class FixtureController extends Controller
{    
    public function index()
    {
        return view('fixtures');
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

    public function latest(FetchLatestUseCase $fetchLatest, FixtureResource $resource)
    {
        try {
            $fixture = $fetchLatest->execute();
                        
            return view('players', $resource->format($fixture));

        } catch (Exception $e) {
            dd($e);
        }
    }
}
