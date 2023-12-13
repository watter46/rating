<?php declare(strict_types=1);

namespace App\UseCases\Player;

use App\Http\Controllers\PositionType;
use Exception;

use App\UseCases\Player\Util\LineupJson;
use App\UseCases\Player\Util\PlayerImage;
use App\UseCases\Player\Util\RatingJson;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


final readonly class GetLineupUseCase
{
    public function execute(int $fixtureId)
    {
        try {
            $startingXI = $this->getStartingXI($fixtureId);
            $rating     = $this->getRating($fixtureId);

            $players = $startingXI['startingXI']
                ->map(function ($player) use ($rating) {
                    $image = PlayerImage::get($player->player->id);

                    $rating = collect($rating)->sole(fn ($rating) => $rating['id'] === $player->player->id)['rating'];
                                        
                    return (object) [
                        'id'       => $player->player->id,
                        'name'     => Str::after($player->player->name, ' '),
                        'number'   => $player->player->number,
                        'grid'     => $player->player->grid,
                        'position' => PositionType::from($player->player->pos)->name,
                        'rating'   => $rating,
                        'img'      => $image
                    ];
                })
                ->values();                
                
            return [
                'formation' => $startingXI['formation'],
                'players'   => $this->formation($players)
            ];

        } catch (Exception $e) {
            throw $e;
        }
    }

    private function getStartingXI(int $fixtureId)
    {
        $lineup = LineupJson::get($fixtureId);

        return collect([
            'formation'  => $lineup[0]->formation,
            'startingXI' => collect($lineup[0]->startXI)
        ]);
    }

    private function getRating(int $fixtureId)
    {
        $statistic = RatingJson::get($fixtureId);

        $ChelseaStatistic = collect($statistic)->filter(function ($team) {
            return $team->team->id === 49;
        })->sole();

        $ratings = collect($ChelseaStatistic->players)
            ->map(function ($player) {
                return [
                    'id'     => $player->player->id,
                    'name'   => $player->player->name,
                    'rating' => $player->statistics[0]->games->rating
                ];
            });

        return $ratings;
    }

    private function formation(Collection $players)
    {
        return $players
            ->groupBy(function ($player) {
                return Str::before($player->grid, ':');
            })
            ->values();
    }
}