<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\UseCases\User\Fixture\FetchFixturePlayerInfosUseCase;
use App\UseCases\User\Fixture\FetchLatestUseCase;
use App\UseCases\User\PlayerInFixtureRequest;

class FixtureController extends Controller
{
    public function index()
    {
        return view('fixtures');
    }

    public function find(string $fixtureId, FetchFixturePlayerInfosUseCase $fetchFixture, FixturePresenter $presenter)
    {
        try {
            $fixture = $fetchFixture->execute(PlayerInFixtureRequest::make($fixtureId));
            
            return view('fixture', $presenter->format($fixture));

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function latest(FetchLatestUseCase $fetchLatest, FixturePresenter $presenter)
    {
        try {
            $fixture = $fetchLatest->execute();
                        
            return view('fixture', $presenter->format($fixture));

        } catch (Exception $e) {
            throw $e;
        }
    }
}