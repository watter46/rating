<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Exception;

use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\LineupFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Http\Controllers\Util\StatisticsFile;
use App\UseCases\Player\Util\ApiFootballFetcher;

/**
 * 試合のデータなどを取得できる
 * 
 * https://rapidapi.com/api-sports/api/api-football
 * 
 * rate: 100req/day 30req/min
 */
class ApiFootballController extends Controller
{
    // vs Everton    fixtureId: 1035327
    // vs Manchester fixtureId: 1035323
    public function fetchStatistic(StatisticsFile $file): void
    {
        try {
            $fixtureId = 1035323;

            if ($file->exists($fixtureId)) return;
            
            $lineup = ApiFootballFetcher::statistic($fixtureId)->fetch();

            $file->write($fixtureId, $lineup);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function fetchLineup(LineupFile $file): void
    {
        try {
            // Everton: id
            // $fixtureId = 1035327;

            // Manchester ID: 1035323
            $fixtureId = 1035323;

            if ($file->exists($fixtureId)) return;
            
            $lineup = ApiFootballFetcher::lineup($fixtureId)->fetch();

            $file->write($fixtureId, $lineup);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function fetchFixtures(FixturesFile $file): void
    {        
        try {
            if ($file->exists()) return;
            
            $fixtures = ApiFootballFetcher::fixtures()->fetch();

            $file->write($fixtures);

        } catch (Exception $e) {
            dd($e);
        }
    }

    public function fetchSquads(SquadsFile $file)
    {
        try {
            if ($file->exists()) return;
            
            $fixtures = ApiFootballFetcher::squads()->fetch();

            $file->write($fixtures);

        } catch (Exception $e) {
            dd($e);
        }
    }
}
