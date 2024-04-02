<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixturesData;

use App\UseCases\Admin\Fixture\DataInterface;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\FixturesData\FixturesDetailData;


readonly class FixturesData implements DataInterface
{
    private FixturesDataBuilder $builder;
    
    /**
     * __construct
     *
     * @param  Collection<int, FixturesDetailData> $fixturesData
     * @return void
     */
    private function __construct(private Collection $fixturesData)
    {
        $this->builder = new FixturesDataBuilder();
    }

    public static function create(Collection $fixturesData): self
    {
        return new self($fixturesData->map(function ($data) {
            return FixturesDetailData::create(collect($data));
        }));
    }
    
    /**
     * getData
     *
     * @return Collection<int, FixturesDetailData> $fixturesData
     */
    public function getData(): Collection
    {
        return $this->fixturesData;
    }

    public function format(): Collection
    {
        return $this->fixturesData
            ->map(function (FixturesDetailData $fixturesDetailData) {
                return collect([
                    'external_fixture_id' => $fixturesDetailData->getFixtureId(),
                    'external_league_id'  => $fixturesDetailData->getLeagueId(),
                    'score'               => $fixturesDetailData->getScore()->toJson(),
                    'season'              => $fixturesDetailData->getSeason(),
                    'date'                => $fixturesDetailData->getDate(),
                    'status'              => $fixturesDetailData->getStatus()
                ]);
            });
    }

    public function build(): Collection
    {
        return $this->builder->build($this);
    }

    public function validated(): FixturesDataValidator
    {
        return FixturesDataValidator::validate($this);
    }

    public function checkRequiredData(): bool
    {
        return FixturesDataValidator::validate($this)->checkRequiredData();
    }

    public function getUniqueTeamIds(): Collection
    {
        return $this->fixturesData
            ->map(function (FixturesDetailData $fixturesDetailData) {
                return $fixturesDetailData->getTeamIds();
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
            ->map(function (FixturesDetailData $fixturesDetailData) {
                return $fixturesDetailData->getLeagueId();
            });
    }
}