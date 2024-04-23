<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\Data\FixtureStatusType;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoDataValidator;


readonly class FixtureInfoData
{
    private function __construct(private FixtureData $fixtureData)
    {
        //
    }

    public static function create(Collection $fixtureData): self
    {
        return new self(FixtureData::create($fixtureData));
    }

    public function buildLineups(): Collection
    {
        return $this->fixtureData->buildLineups();
    }

    public function build(): Collection
    {
        return $this->fixtureData->build();
    }

    /**
     * 試合を表示するのに必要なデータが存在しているか判定する
     *
     * @return bool
     */
    public function checkRequiredData(): bool
    {
        return FixtureInfoDataValidator::validate($this->fixtureData)->checkRequiredData();
    }
    
    public function validated(): FixtureInfoDataValidator
    {
        return FixtureInfoDataValidator::validate($this->fixtureData);
    }
    
    public function isFinished(): bool
    {
        return $this->fixtureData->isFinished();
    }
}