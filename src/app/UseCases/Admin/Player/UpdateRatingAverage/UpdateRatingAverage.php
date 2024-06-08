<?php declare(strict_types=1);

namespace App\UseCases\Admin\Player\UpdateRatingAverage;

use Exception;
use Illuminate\Support\Facades\DB;

use App\Models\Average;
use App\Models\Fixture;
use App\UseCases\Admin\Player\UpdateRatingAverage\UpdateRatingAverageBuilder;


class UpdateRatingAverage
{
    public function __construct(private UpdateRatingAverageBuilder $builder)
    {
        //
    }

    /**
     * 
     *
     * @return void
     */
    public function execute(string $fixtureInfoId)
    {
        try {
            $averages = Average::query()
                ->fixtureInfoId($fixtureInfoId)
                ->get();
            
            $fixtures = Fixture::query()
                ->select('id')
                ->with('players:fixture_id,rating,mom,player_info_id')
                ->fixtureInfoId($fixtureInfoId)
                ->get();

            if ($fixtures->isEmpty()) return;

            $data = $this->builder->build($fixtureInfoId, $averages, $fixtures);
            
            DB::transaction(function () use ($data) {
                $unique = ['id'];
                $updateColumns = ['rating', 'mom'];
                
                Average::upsert($data->toArray(), $unique, $updateColumns);
            });

        } catch (Exception $e) {
            throw $e;
        }
    }
}