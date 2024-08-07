<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\ApiFootball;

use App\Models\FixtureInfo;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use Illuminate\Support\Collection;

class FixturesData
{    
    /**
     * __construct
     *
     * @param  Collection<FixtureData> $fixturesData
     * @return void
     */
    private function __construct(private Collection $fixturesData)
    {
        //
    }

    public static function create(Collection $fixturesData): self
    {
        return new self($fixturesData->map(function ($fixtureData) {
            return FixtureData::create(collect($fixtureData));
        }));
    }
    
    // /**
    //  * getData
    //  *
    //  * @return Collection<FixtureData> $fixturesData
    //  */
    // public function get(): Collection
    // {
    //     return $this->fixturesData;
    // }

    // public function partition(Collection $fixtureIds)
    // {        
    //     return $this->fixturesData
    //         ->partition(function (FixtureData $fixtureData) use ($fixtureIds) {
    //             return $fixtureData->exists($fixtureIds);
    //         });
    // }
    
    /**
     * keyByFixtureId
     *
     * @return Collection<FixtureData>
     */
    public function keyByFixtureId(): Collection
    {
        return $this->fixturesData
            ->keyBy(function (FixtureData $fixtureData) {
                return $fixtureData->getFixtureId();
            });
    }

    public function get(int $fixtureId): ?FixtureData
    {
        return $this->fixturesData
            ->first(function (FixtureData $fixtureData) use ($fixtureId) {
                return $fixtureData->equal($fixtureId);
            });
    }

    public function getFixtureIds()
    {
        return $this->fixturesData
            ->map(function (FixtureData $fixtureData) {
                return $fixtureData->getFixtureId();
            });
    }

    // public function defaultFormat(): Collection
    // {
    //     return $this->fixturesData->map(function (FixtureData $fixtureData) {
    //         return $fixtureData->build();
    //     });
    // }

    // // Modelを持ち込まない方が良い
    // public function build(): Collection
    // {
    //     /** @var Collection */
    //     $fixtureInfos = FixtureInfo::query()
    //         ->selectWithoutTimestamps()   
    //         ->currentSeason()
    //         ->get()
    //         ->map(function (FixtureInfo $fixtureInfo) {
    //             return $fixtureInfo->castsToJson();
    //         });

    //     return $fixtureInfos->isNotEmpty()
    //         ? $fixtureInfosData->getData()
    //             ->map(function (FixtureData $fixtureData) use ($fixtureInfos) {
    //                 $fixtureInfo = $fixtureInfos
    //                     ->keyBy('external_fixture_id')
    //                     ->get($fixtureData->getFixtureId());

    //                 dd($fixtureData->build(), $fixtureInfo, $fixtureInfos, $fixtureData->getFixtureId());
                        
    //                 return $fixtureInfo
    //                     ->merge($fixtureData->build());
    //             })
    //         : $fixtureInfosData->defaultFormat();
    // }

    // public function validated(): FixtureInfosDataValidator
    // {
    //     return FixtureInfosDataValidator::validate($this);
    // }

    // public function checkRequiredData(): bool
    // {
    //     return FixtureInfosDataValidator::validate($this)->checkRequiredData();
    // }

    // public function getUniqueTeamIds(): Collection
    // {
    //     return $this->fixturesData
    //         ->map(function (FixtureData $fixtureData) {
    //             return $fixtureData->getTeamIds();
    //         })
    //         ->flatten()
    //         ->unique()
    //         ->values();
    // }
        
    // /**
    //  * リーグIDのリストを取得する
    //  *
    //  * @return Collection<int, int>
    //  */
    // public function getUniqueLeagueIds(): Collection
    // {
    //     return $this->fixturesData
    //         ->map(function (FixtureData $fixtureData) {
    //             return $fixtureData->getLeagueId();
    //         });
    // }
}