<?php declare(strict_types=1);

namespace App\UseCases\Admin\Data\ApiFootball\FixtureData;

use App\Models\TournamentIdType;
use Illuminate\Support\Collection;


class FixtureData
{
    private const RESULT_DATA_KEYS = ['fixture', 'league', 'teams', 'goals', 'score'];
    private const LINEUPS_DATA_KEYS = ['lineups', 'statistics', 'players'];

    /**
     * ResultData
     *  - score
     *  - teams
     *  - league
     *  - fixture
     * 
     * LineupsData
     *  - lineups
     */
    private function __construct(private ResultData $resultData, private ?LineupsData $lineupsData)
    {
        //
    }
        
    /**
     * create
     *
     * @param  Collection $fixtureData
     * @return self
     */
    public static function create(Collection $fixtureData)
    {
        $resultData = ResultData::create($fixtureData->fromStd()->only(self::RESULT_DATA_KEYS));

        $lineupsData = $fixtureData->fromStd()->only(self::LINEUPS_DATA_KEYS)->isNotEmpty()
            ? LineupsData::create($fixtureData->fromStd()->only(self::LINEUPS_DATA_KEYS))
            : null;

        return new self($resultData, $lineupsData);
    }

    public function isSeasonTournament(): bool
    {
        return collect(TournamentIdType::inSeasonTournaments())
            ->contains(fn($id) => $id === $this->getLeagueId());
    }

    public function equal(int $fixtureId): bool
    {
        return $fixtureId === $this->getFixtureId();
    }

    public function exists(Collection $fixtureIds): bool
    {
        return $fixtureIds->some(fn (int $fixtureId) => $fixtureId === $this->getFixtureId());
    }
    
    public function isFinished(): bool
    {
        return $this->resultData->isFinished();
    }

    public function getLineups(): Collection
    {
        return $this->lineupsData->getLineups();
    }

    public function getScore(): Collection
    {
        return $this->resultData->getScore();
    }

    public function getTeamIds(): Collection
    {
        return $this->resultData->getTeamIds();
    }

    public function getTeams(): Collection
    {
        return $this->resultData->getTeams();
    }

    public function getLeagueId(): int
    {
        return $this->resultData->getLeagueId();
    }

    public function getLeague(): Collection
    {
        return $this->resultData->getLeague();
    }

    public function getFixtureId(): int
    {
        return $this->resultData->getFixtureId();
    }

    public function getFixture(): Collection
    {
        return $this->resultData->getFixture();
    }

    public function getResultData(): Collection
    {
        return $this->resultData->getAll();
    }
    
    public function getPlayedPlayers(): Collection
    {
        return $this->lineupsData->playedPlayers();
    }
}