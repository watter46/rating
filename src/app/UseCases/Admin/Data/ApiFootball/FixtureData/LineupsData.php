<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\ApiFootball\FixtureData;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Data\ApiFootball\FixtureData\PositionType;
use App\UseCases\Util\PlayerName;
use App\Http\Controllers\Util\PlayerImageFile;


class LineupsData
{
    private PlayerImageFile $playerImage;

    public function __construct(private Collection $lineupsData)
    {
        $this->playerImage = new PlayerImageFile;
    }

    public static function create(Collection $lineupsData) 
    {
        return new self($lineupsData);
    }

    public function lineupsExists(): bool
    {
        return collect($this->lineupsData)
            ->every(fn($data) => !empty($data));
    }

    private function filterChelsea(Collection $teams): Collection
    {
        $chelsea = $teams->sole(function (array $team) {
            return collect($team)->dataGet('team.id', false) === config('api-football.chelsea-id');
            });

        return collect($chelsea);
    }

    public function getLineups(): Collection
    {        
        $players = $this->playedPlayers()->keyBy('id');

        return $this->filterChelsea($this->lineupsData->dataGet('lineups'))
            ->only(['startXI', 'substitutes'])
            ->map(fn (array $lineups) => collect($lineups)->flatten(1))
            ->map(function (Collection $lineups) use ($players) {
                return $lineups->filter(fn (array $player) => $players->get($player['id']));
            })
            ->map(function ($lineups) use ($players) {
                return collect($lineups)
                    ->map(function (array $player) use ($players) {
                        $data = collect($player)->merge($players->get($player['id']));
                        
                        return collect([
                                'id'       => $data['id'],
                                'name'     => $data['name'],
                                'number'   => $data['number'],
                                'position' => PositionType::from($data['pos'])->name,
                                'grid'     => $data['grid'],
                                'img'      => $this->playerImage->generatePath($data['id']),
                                'goal'     => $data['goal'], 
                                'assists'  => $data['assists'], 
                                'rating'   => $data['rating'],
                                'minutes'  => $data['minutes']
                            ]);
                    })
                    ->map(fn(Collection $player) => $player->except('minutes'));
            });
    }

    public function playedPlayers(): Collection
    {
        return $this->filterChelsea($this->lineupsData->dataGet('players'))
            ->dataGet('players')
            ->map(function (array $playerData) {                
                $player = collect($playerData);

                return [
                    'id'      => $player->dataGet('player.id', false),
                    'name'    => PlayerName::create($player->dataGet('player.name', false))->getFullName(),
                    'number'  => $player->dataGet('statistics.0.games.number', false),
                    'goal'    => $player->dataGet('statistics.0.goals.total', false), 
                    'assists' => $player->dataGet('statistics.0.goals.assists', false), 
                    'rating'  => $player->dataGet('statistics.0.games.rating', false),
                    'minutes' => $player->dataGet('statistics.0.games.minutes', false)
                ];
            })
            ->filter(fn(array $player) => !is_null($player['minutes']));
    }
}