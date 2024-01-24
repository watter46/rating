<?php declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use Carbon\Carbon;

final readonly class FixturesResource
{
    public function __construct(
        private LeagueImageFile $leagueImage,
        private TeamImageFile   $teamImage)
    {
        
    }
    
    /**
     * チーム、リーグ、プレイヤーのファイルパスの画像を取得する
     *
     * @param  Paginator $fixtures
     * @return Paginator
     */
    public function format(Paginator $fixtures): Paginator
    {
        $fixtures
            ->getCollection()
            ->transform(function ($fixture) {
                $fixture->score = $fixture
                    ->score
                    ->map(function ($fixture, $key) {
                        return match($key) {
                            'teams'   => $this->addTeamImage($fixture),
                            'league'  => $this->addLeagueImage($fixture),
                            'fixture' => $this->addDate($fixture)
                        };
                    });

                return $fixture;
            });

        return $fixtures;
    }

    private function addTeamImage(array $teams): array
    {
        return collect($teams)
            ->map(function ($team) {
                return collect($team)
                    ->put('img', $this->teamImage->getByPath($team['img']));
            })
            ->toArray();
    }

    private function addLeagueImage(array $league): array
    {
        return collect($league)
            ->put('img', $this->leagueImage->getByPath($league['img']))
            ->toArray();
    }

    private function addDate(array $fixture)
    {
        return [
            'date' => Carbon::parse($fixture['date'])->format('m/d/Y'),
            'status' => $fixture['status']
        ];
    }
}