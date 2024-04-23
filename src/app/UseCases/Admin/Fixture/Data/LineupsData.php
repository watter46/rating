<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Data;

use App\Http\Controllers\Util\PlayerImageFile;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\Data\PositionType;


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

    public function build(): Collection
    {
        return collect(['lineups' => $this->getLineups()]);
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
            ->map(function ($lineups) use ($players) {
                return collect($lineups)
                    ->map(function ($lineup) use ($players) {
                        $player = collect($lineup['player']);

                        $data = $player->merge($players->get($player->get('id')));

                        return collect([
                                'id'       => $data['id'],
                                'name'     => $data['name'],
                                'number'   => $data['number'],
                                'position' => PositionType::from($data['pos'])->name,
                                'grid'     => $data['grid'],
                                'img'      => $this->playerImage->generatePath($data['id'])
                            ]);
                    });
            });
    }

    public function playedPlayers(): Collection
    {
        return $this->filterChelsea($this->lineupsData->dataGet('players'))
            ->dataGet('players')
            ->reject(function ($players) {
                return !collect($players)->dataGet('statistics.0.games.minutes', false);
            })
            ->map(function ($players) {
                $data = collect($players);

                return [
                    'id' => $data->dataGet('player.id', false),
                    'name' => $data->dataGet('player.name', false),
                    'goal' => $data->dataGet('statistics.0.goals.total', false), 
                    'assists' => $data->dataGet('statistics.0.goals.assists', false), 
                    'defaultRating' => $data->dataGet('statistics.0.games.rating', false)
                ];
            });
    }
}