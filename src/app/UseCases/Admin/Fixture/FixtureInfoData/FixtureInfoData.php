<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use App\Models\TournamentIdType;
use App\Models\TournamentType;
use Illuminate\Support\Collection;

use App\UseCases\Admin\Fixture\Data\FixtureData;
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

    public function isSeasonTournament()
    {
        return collect(TournamentIdType::inSeasonTournaments())
            ->contains(fn($id) => $id === $this->fixtureData->getLeagueId());
    }

    public function buildLineups(): Collection
    {
        return $this->fixtureData->buildLineups();
    }

    public function buildScore(): Collection
    {
        return $this->fixtureData->getScore();
    }

    public function buildFixture(): Collection
    {
        return $this->fixtureData->getFixture();
    }

    public function build(): Collection
    {
        return $this->fixtureData->build();
    }
    
    /**
     * Lineupsの数がLineupsDataの数と一致しているか
     *
     * @param  int $lineupCount
     * @return bool
     */
    public function equalLineupCount(int $lineupCount): bool
    {
        return $this->fixtureData->getPlayedPlayers()->count() === $lineupCount;
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