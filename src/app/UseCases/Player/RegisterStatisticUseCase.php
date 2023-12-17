<?php declare(strict_types=1);

namespace App\UseCases\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Statistic;
use App\UseCases\Player\Util\ApiFootballFetcher;


final readonly class RegisterStatisticUseCase
{
    const CHELSEA_TEAM_ID = 49;

    public function __construct(private Statistic $statistic)
    {
        //
    }
    
    /**
     * 選手の試合の統計を保存する
     *
     * @return void
     */
    public function execute(int $fixtureId)
    {
        try {                        
            if ($this->statistic->where('fixture_id', $fixtureId)->exists()) {
                return;
            };
            
            $json = ApiFootballFetcher::statistic($fixtureId)->fetch();
            
            $statisticJson = collect(json_decode($json));

            $team = $statisticJson->sole(fn ($teams) => $teams->team->id === self::CHELSEA_TEAM_ID);
            
            $data = collect($team->players)
                ->map(function ($player) {
                    return [
                        'id'     => $player->player->id,
                        'rating' => $player->statistics[0]->games->rating
                    ];
                });

            $statistic = $this
                ->statistic
                ->setStatistic(
                    fixture_id: $fixtureId,
                    statistic: json_encode($data)
                );

            DB::transaction(function () use ($statistic) {
                $statistic->save();
            });
            
        } catch (Exception $e) {
            dd($e);
        }
    }
}