<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\UsersRating;
use App\Models\Fixture;
use App\Models\FixtureInfoPlayerInfo;
use App\Models\Player;
use App\Models\UsersPlayerStatistic;
use App\UseCases\Admin\Player\Processors\UsersRating\UsersRatingBuilder;


class UpdateUsersRating
{
    public function __construct(private UsersRatingBuilder $builder)
    {
        //
    }

    /**
     * 指定の試合のユーザー全体の平均評価点を保存する
     *
     * @return void
     */
    public function execute(string $fixtureInfoId)
    {
        try {
            $fixtures = Fixture::query()
                ->select(['id', 'fixture_info_id'])
                ->with([
                    'fixtureInfo:id' => ['playerInfos:id'],
                    'players:fixture_id,rating,mom,player_info_id'
                ])
                ->byFixtureInfoId($fixtureInfoId)
                ->get();

            if ($fixtures->isEmpty()) return;

            $data = $this->builder->build($fixtures->toCollection());
            
            DB::transaction(function () use ($data) {                
                UsersPlayerStatistic::upsert(
                    $data->toArray(),
                    UsersPlayerStatistic::UPSERT_UNIQUE
                );
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}