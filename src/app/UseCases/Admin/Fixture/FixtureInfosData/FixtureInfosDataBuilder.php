<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfosData;

use Illuminate\Support\Collection;

use App\Models\Fixture;
use App\Models\FixtureInfo;
use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDetailData;


final readonly class FixtureInfosDataBuilder
{    
    /**
     * build
     *
     * @param  FixtureInfosData $fixtureInfosData
     * @return Collection
     */
    public function build(FixtureInfosData $fixtureInfosData): Collection
    {
        /** @var Collection */
        $fixtureInfos = FixtureInfo::query()
            ->select([
                'external_fixture_id',
                'external_league_id',
                'season',
                'date',
                'status',
                'score',
                'teams',
                'league',
                'fixture',
                'lineups'
            ])
            ->currentSeason()
            ->get()
            ->map(function (FixtureInfo $fixtureInfo) {
                return $fixtureInfo->castsToJson();
            });
            
        return $fixtureInfos->isNotEmpty()
            ? $fixtureInfosData->getData()
                ->map(function (FixtureData $fixtureData) use ($fixtureInfos) {
                    $fixtureInfo = $fixtureInfos
                        ->keyBy('external_fixture_id')
                        ->get($fixtureData->getFixtureId());

                    return $fixtureInfo
                        ->merge($fixtureData->build());
                })
            : $fixtureInfosData->format();
    }
}