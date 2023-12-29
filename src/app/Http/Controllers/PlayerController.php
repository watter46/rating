<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\UseCases\Player\FetchFixtureUseCase;
use App\UseCases\Player\RegisterFixtureListUseCase;
use App\UseCases\Player\RegisterFixtureUseCase;
use App\UseCases\Player\RegisterPlayerOfTeamUseCase;

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
            // $fixtureId = 1035359;
            
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
            // $fixtureId = 1035327;
            // $fixtureId = 1141105;
            // $fixtureId = 1035338;
            // $fixtureId = 1035353;
            $fixtureId = 1035359;
        
            // $registerFixture->execute($fixtureId);
            $registerFixture->execute($fixtureId);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function register2(RegisterPlayerOfTeamUseCase $registerPlayerOfTeam)
    {
        try {
            $registerPlayerOfTeam->execute();
        } catch (Exception $e) {

        }
    }

    public function register3(RegisterFixtureListUseCase $registerFixtureList)
    {
        try {
            $registerFixtureList->execute();
        } catch (Exception $e) {

        }
    }
}