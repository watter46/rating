<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfosData;

use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\DataInterface;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDataBuilder;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDetailData;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDataValidator;


readonly class FixtureInfosData2 implements DataInterface
{
    private FixtureInfosDataBuilder $builder;
    
    /**
     * __construct
     *
     * @param  Collection<int, FixtureInfosDetailData> $fixturesData
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

            // return FixtureInfosDetailData::create(collect($fixtureData));
        }));
    }
    
    /**
     * getData
     *
     * @return Collection<int, FixtureInfosDetailData> $fixturesData
     */
    public function getData(): Collection
    {
        return $this->fixturesData;
    }

    public function format(): Collection
    {
        return $this->fixturesData
            ->map(function (FixtureInfosDetailData $FixtureInfosDetailData) {
                return collect([
                    'external_fixture_id' => $FixtureInfosDetailData->getFixtureId(),
                    'external_league_id'  => $FixtureInfosDetailData->getLeagueId(),
                    'season'              => $FixtureInfosDetailData->getSeason(),
                    'date'                => $FixtureInfosDetailData->getDate(),
                    'status'              => $FixtureInfosDetailData->getStatus(),
                    'score'               => $FixtureInfosDetailData->getScore()->toJson(),
                    'teams'               => $FixtureInfosDetailData->getTeams()->toJson(),
                    'league'              => $FixtureInfosDetailData->getLeague()->toJson(),
                    'fixture'             => $FixtureInfosDetailData->getFixture()->toJson()
                ]);
            });
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
            ->map(function (FixtureInfosDetailData $FixtureInfosDetailData) {
                return $FixtureInfosDetailData->getTeamIds();
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
            ->map(function (FixtureInfosDetailData $FixtureInfosDetailData) {
                return $FixtureInfosDetailData->getLeagueId();
            });
    }
}