<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Processors\FixtureInfos;

use App\Events\FixtureInfosRegistered;
use App\Models\FixtureInfo;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use App\UseCases\Admin\Data\ApiFootball\FixturesData;
use App\UseCases\Admin\Fixture\Processors\FixtureInfos\FixtureInfosDataValidator;
use App\UseCases\Admin\Fixture\Processors\FixtureInfos\FixtureInfosData;
use Illuminate\Database\Eloquent\Collection;


class FixtureInfosBuilder
{
    private function __construct(private Collection $fixtureInfos)
    {
        
    }

    public static function create(): self
    {
        $fixtureInfos = FixtureInfo::query()
            ->select(['league', 'teams'])
            ->get();
        
        return new self($fixtureInfos);
    }

    /**
     * ApiFootballから取得した全ての試合のデータを更新する
     *
     * @param  FixturesData $fixturesData
     * @return Collection
     */
    public function bulkUpdate(FixturesData $fixturesData)
    {
        $fixtureInfos = FixtureInfo::query()
            ->whereIn(
                'external_fixture_id',
                $fixturesData
                    ->getFixtureIds()
                    ->toArray()
            )
            ->get()
            ->keyBy('external_fixture_id');

        $formatForUpsert = function (FixtureData $fixtureData) {
            $data = $fixtureData->getResultData();
        
            $fixtureInfo = new FixtureInfo([
                    'external_fixture_id' => $data['fixtureId'],
                    'external_league_id'  => $data['leagueId'],
                    'season'              => $data['season'],
                    'date'                => $data['date'],
                    'status'              => $data['status'],
                    'score'               => $data['score'],
                    'teams'               => $data['teams'],
                    'league'              => $data['league'],
                    'fixture'             => $data['fixture']
                ]);

            return $fixtureInfo->castsToJson();
        };
            
        return $fixturesData
            ->keyByFixtureId()
            ->filter(function (FixtureData $fixtureData) {
                return $fixtureData->isSeasonTournament();
            })
            ->map(function (FixtureData $fixtureData, $fixtureId) use ($fixtureInfos, $formatForUpsert) {
                $fixtureInfo = $fixtureInfos->get($fixtureId);

                if (!$fixtureInfo) {
                    return $formatForUpsert($fixtureData);
                }

                return $formatForUpsert($fixtureData)->put('id', $fixtureInfo->id);
            });
    }

    private function toData(): FixtureInfosData
    {
        return FixtureInfosData::create($this->fixtureInfos);
    }

    public function validator(): FixtureInfosDataValidator
    {
        return FixtureInfosDataValidator::validate($this->toData());
    }

    /**
     * 試合を表示するのに必要なデータが存在しているか判定する
     *
     * @return bool
     */
    private function checkRequiredData(): bool
    {
        return $this->validator()->checkRequiredData();
    }

    public function dispatch(): void
    {
        if ($this->checkRequiredData()) {
            return;
        }
        
        FixtureInfosRegistered::dispatch($this);
    }
}