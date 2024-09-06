<?php declare(strict_types=1);

namespace App\Http\Controllers\Presenters;

use Illuminate\Support\Collection;

class PlayerPresenter
{
    private PlayerImageFile $playerImage;

    public function __construct(private Collection $player)
    {
        dd($player);

        $this->playerImage = new PlayerImageFile;
    }

    public function format()
    {
        $players = $this->fixture->players->keyBy('player_info_id');

        $formatPlayer = function (array $playerData) use ($playerInfos, $players) {
            /** @var PlayerInfo $playerInfo */
            $playerInfo = $playerInfos->keyBy('api_player_id')->get($playerData['id']);
            
            /** @var Player $player */
            $player = $players->get($playerInfo->id);

            return collect([
                'fixture_info_id' => $this->fixture->fixture_info_id,
                'player_info_id' => $player->player_info_id,
                'canRate' => $player->canRate,
                'canMom' => $player->canMom,
                'momCount' => $this->fixture->mom_count,
                'momLimit' => $this->fixture->momLimit,
                'rateCount' => $player->rate_count,
                'rateLimit' => $player->rateLimit,
                'img' => [
                    'exists' => $this->playerImage->exists($playerData['id']),
                    'img' => $this->playerImage->exists($playerData['id'])
                        ? $this->playerImage->generateViewPath($playerData['id'])
                        : $this->playerImage->getDefaultPath(),
                    'number' => $playerData['number']
                ],
                'goals' => $playerData['goal'],
                'grid' => $playerData['grid'],
                'name' => $this->toLastName($playerData['name']),
                'number' => $playerData['number'],
                'assists' => $playerData['assists'],
                'position' => $playerData['position'],
                'ratings' => [
                    'my' => [
                        'rating' => $player->rating,
                        'mom' => $player->mom
                    ],
                    'users' => [
                        'rating' => $player->usersRating['rating'],
                        'mom' => $player->usersRating['mom']
                    ],
                    'machine' => $playerData['rating']
                ]
            ]);
        };

        $playerData = $this->fixture->fixtureInfo->lineups
            ->map(function ($lineups, $key) use ($formatPlayer) {
                if ($key === 'substitutes') {
                    return collect($lineups)->map(fn ($player) => $formatPlayer($player));
                }

                return collect($lineups)
                    ->map(fn($players) => collect($players)
                    ->map(fn ($player) => $formatPlayer($player)));
            });
    }
}