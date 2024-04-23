<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfosData;

use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\DataInterface;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDataBuilder;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDetailData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDataValidator;
use Exception;

readonly class FixtureInfosData implements DataInterface
{
    private FixtureInfosDataBuilder $builder;
    
    /**
     * __construct
     *
     * @param  Collection<int, FixtureData> $fixturesData
     * @return void
     */
    private function __construct(private Collection $fixturesData)
    {
        $this->builder = new FixtureInfosDataBuilder();
    }

    public static function create(Collection $fixturesData): self
    {
        return new self($fixturesData->map(function ($fixtureData) {
            return FixtureData::create(collect($fixtureData));
        }));
    }
    
    /**
     * getData
     *
     * @return Collection<int, FixtureData> $fixturesData
     */
    public function getData(): Collection
    {
        return $this->fixturesData;
    }

    public function format(): Collection
    {
        return $this->fixturesData->map(function (FixtureData $fixtureData) {
            return $fixtureData->build();
        });
        
        // return $this->fixturesData
        //     ->map(function (FixtureData $fixtureData) {
        //         dd($fixtureData->)
        //         return collect([
        //             'external_fixture_id' => $fixtureData->getFixtureId(),
        //             'external_league_id'  => $fixtureData->getLeagueId(),
        //             'season'              => $fixtureData->getSeason(),
        //             'date'                => $fixtureData->getDate(),
        //             'status'              => $fixtureData->getStatus(),
        //             'score'               => $fixtureData->getScore()->toJson(),
        //             'teams'               => $fixtureData->getTeams()->toJson(),
        //             'league'              => $fixtureData->getLeague()->toJson(),
        //             'fixture'             => $fixtureData->getFixture()->toJson()
        //         ]);
        //     });
    }

    public function build(): Collection
    {
        return $this->builder->build($this);
    }

    public function validated(): FixtureInfosDataValidator
    {
        return FixtureInfosDataValidator::validate($this);
    }

    public function checkRequiredData(): bool
    {
        return FixtureInfosDataValidator::validate($this)->checkRequiredData();
    }

    public function getUniqueTeamIds(): Collection
    {
        return $this->fixturesData
            ->map(function (FixtureData $fixtureData) {
                return $fixtureData->getTeamIds();
            })
            ->flatten()
            ->unique()
            ->values();
    }
        
    /**
     * リーグIDのリストを取得する
     *
     * @return Collection<int, int>
     */
    public function getUniqueLeagueIds(): Collection
    {
        return $this->fixturesData
            ->map(function (FixtureData $fixtureData) {
                return $fixtureData->getLeagueId();
            });
    }
}