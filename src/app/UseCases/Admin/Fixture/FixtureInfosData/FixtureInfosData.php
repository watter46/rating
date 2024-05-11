<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfosData;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\DataInterface;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDataBuilder;
use App\UseCases\Admin\Fixture\FixtureInfosData\FixtureInfosDataValidator;


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

    public function defaultFormat(): Collection
    {
        return $this->fixturesData->map(function (FixtureData $fixtureData) {
            return $fixtureData->build();
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