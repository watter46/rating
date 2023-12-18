<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Lineup;
use App\Models\Statistic;
use App\Http\Controllers\PositionType;
use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\Models\Fixture;

final readonly class GetLineupUseCase
{
    public function __construct(
        private PlayerImageFile $image,
        private Statistic $statistic,
        private Lineup $lineup,
        private Fixture $fixture,
        private TeamImageFile $teamImage,
        private LeagueImageFile $leagueImage,)
    {
        
    }
    
    public function execute(int $fixtureId): array
    {
        try {
            $lineup    = $this->fetchLineup($fixtureId);
            $statistic = $this->fetchStatistic($fixtureId);
            $fixture   = $this->fetchFixture($fixtureId);

            $players = $lineup
                ->map(function ($player) use ($statistic) {
                    $image = $this->image->get($player['player']['id']);

                    $rating = $statistic->sole(function ($statistic) use ($player) {
                            return $statistic['id'] === $player['player']['id'];
                        })['rating'];
                        
                    return (object) [
                        'id'       => $player['player']['id'],
                        'name'     => Str::after($player['player']['name'], ' '),
                        'number'   => $player['player']['number'],
                        'grid'     => $player['player']['grid'],
                        'position' => PositionType::from($player['player']['pos'])->name,
                        'rating'   => $rating,
                        'img'      => $image
                    ];
                })
                ->values()
                ->groupBy(function ($player) {
                    return Str::before($player->grid, ':');
                });
                
            return [
                'players' => $players,
                'fixture' => $fixture
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    private function fetchFixture(int $fixtureId)
    {
        /** @var Fixture $fixture */
        $fixture = $this->fixture
            ->where('external_fixture_id', $fixtureId)
            ->latest()
            ->first();

        if (!$fixture) {
            throw new ModelNotFoundException('Not Found Fixture: '.$fixtureId);
        }

        $result = collect();

        $result->put(
            $fixture->is_home ? 'home' : 'away', [
                'team_name' => 'Chelsea FC',
                'img' => $this->teamImage->get(49),
                'score' => $fixture->home
            ]
        );

        $result->put(
            !$fixture->is_home ? 'home' : 'away', [
                'team_name' => $fixture->team_name,
                'img' => $this->teamImage->get($fixture->external_team_id),
                'score' => $fixture->away
            ]
        );
        
        $result->put(
            'league', [
            'round' => $fixture->round,
            'league_name' => $fixture->league_name,
            'img' => $this->leagueImage->get($fixture->external_league_id)
        ]);

        return $result;
    }

    private function fetchLineup(int $fixtureId): Collection
    {
        /** @var Lineup $lineup */
        $lineup = $this->lineup
            ->where('fixture_id', $fixtureId)
            ->latest()
            ->first();

        if (!$lineup) {
            throw new ModelNotFoundException('Not Found Fixture: '.$fixtureId);
        }
        
        return $lineup->lineup;
    }

    private function fetchStatistic(int $fixtureId): Collection
    {
        /** @var Statistic $statistic */
        $statistic = $this->statistic->where('fixture_id', $fixtureId)->latest()->first();

        if (!$statistic) {
            throw new ModelNotFoundException('Not Found Statistic: '.$fixtureId);
        }

        return $statistic->statistic;
    }
}