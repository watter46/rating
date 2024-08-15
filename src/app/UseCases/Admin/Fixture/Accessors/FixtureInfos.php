<?php declare(strict_types=1);


namespace App\UseCases\Admin\Fixture\Accessors;

use App\Events\FixtureInfosRegistered;
use Illuminate\Support\Collection;

use App\Models\FixtureInfo as FixtureInfoModel;


class FixtureInfos
{    
    /**
     * __construct
     *
     * @param  Collection<FixtureInfo> $fixtureInfos
     * @return void
     */
    private function __construct(private Collection $fixtureInfos)
    {
        //
    }

    public static function create(Collection $rawData): self
    {
        return new self(
            $rawData
                ->map(function ($fixtureInfo) {
                    return FixtureInfo::create(collect($fixtureInfo));
                })
        );
    }

    public static function reconstruct(Collection $fixtureInfos): self
    {
        return new self(
            $fixtureInfos
                ->map(function (FixtureInfoModel $fixtureInfo) {
                    return FixtureInfo::reconstruct($fixtureInfo);
                })
        );
    }
    
    /**
     * bulkUpdate
     *
     * @param  Collection<FixtureInfoModel> $models
     * @return self
     */
    public function bulkUpdate(Collection $models): self
    {
        if ($models->isEmpty()) {
            return new self($this->fixtureInfos);
        }
        
        $modelsByFixtureId = $models->keyBy('api_fixture_id');

        return new self(
            $this->fixtureInfos
                ->map(function (FixtureInfo $fixtureInfo) use ($modelsByFixtureId) {
                    $fixtureInfoId = $modelsByFixtureId
                        ->get($fixtureInfo->getFixtureId())
                        ?->get('id');

                    if (!$fixtureInfoId) {
                        return $fixtureInfo;
                    }

                    return $fixtureInfo->assignId($fixtureInfoId);
                })
        );
    }

    public function get()
    {
        return $this->fixtureInfos;
    }
    
    public function getInvalidTeamImageIds()
    {
        return $this->fixtureInfos->map(function (FixtureInfo $fixtureInfo) {
            return $fixtureInfo->getInvalidTeamImageIds();
        })
        ->flatten()
        ->unique();
    }

    public function getInvalidLeagueImageIds()
    {
        return $this->fixtureInfos->map(function (FixtureInfo $fixtureInfo) {
            return $fixtureInfo->getInvalidLeagueImageIds();
        })
        ->flatten()
        ->unique();
    }

    public function toArray(): array
    {        
        return $this->fixtureInfos
            ->map(function (FixtureInfo $fixtureInfo) {
                return $fixtureInfo
                    ->toModel()
                    ->castsToJson()
                    ->except('lineups');
            })
            ->toArray();
    }

    public function shouldDispatch(): bool
    {
        return $this->fixtureInfos
            ->some(function (FixtureInfo $fixtureInfo) {
                return $fixtureInfo->shouldDispatch();
            });
    }

    public function dispatch()
    {
        FixtureInfosRegistered::dispatch($this);
    }
}