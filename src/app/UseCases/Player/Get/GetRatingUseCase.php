<?php declare(strict_types=1);

namespace App\UseCases\Player\Get;

use App\UseCases\Player\Get\Util\File;
use Exception;


final readonly class GetRatingUseCase
{
    public function __construct()
    {
        //
    }

    public function execute(int $fixtureId)
    {
        try {
            $statistic = File::appPath(
                    dir: 'Template/statistics',
                    fileName: '_player_statistic.json',
                    arg: $fixtureId
                )->get();

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

        } catch (Exception $e) {
            throw $e;
        }
    }
}