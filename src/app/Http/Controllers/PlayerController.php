<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\Http\Controllers\Util\FixturesFile;
use App\UseCases\Player\FetchFixtureUseCase;
use App\UseCases\Player\RegisterFixtureListUseCase;
use App\UseCases\Player\RegisterFixtureUseCase;
use App\UseCases\Player\RegisterLineupUseCase;
use App\UseCases\Player\RegisterPlayerListUseCase;
use App\UseCases\Player\RegisterStatisticUseCase;

final class PlayerController extends Controller
{
    public function index(FetchFixtureUseCase $fetchFixture, FixtureResource $resource)
    {
        try {            
            // Mancester United
            // $fixtureId = 1035323;

            // Everton
            // $fixtureId = 1035327;

            $fixtureId = 1035338;

            // $fixtureId = 1141105;
            // $fixtureId = 1035353;

            $fixture = $fetchFixture->execute($fixtureId);
            
            return view('players', $resource->format($fixture));

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function register(RegisterFixtureUseCase $registerFixture)
    {
        try {            
            // $fixtureId = 1035323;
            $fixtureId = 1035327;
            // $fixtureId = 1141105;
            // $fixtureId = 1035338;
            // $fixtureId = 1035353;

        
            // $registerFixture->execute($fixtureId);
            $registerFixture->execute($fixtureId);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function fixtures(RegisterFixtureListUseCase $registerFixtureList)
    {
        try {
            $registerFixtureList->execute();
        } catch (Exception $e) {

        }
    }
}