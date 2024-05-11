<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Data;

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

    public function build(): Collection
    {
        $fixtureData = collect([
            'external_fixture_id' => $this->resultData->getFixtureId(),
            'external_league_id'  => $this->resultData->getLeagueId(),
            'season'              => $this->resultData->getSeason(),
            'date'                => $this->resultData->getDate(),
            'status'              => $this->resultData->getStatus(),
            'score'               => $this->resultData->getScore()->toJson(),
            'teams'               => $this->resultData->getTeams()->toJson(),
            'league'              => $this->resultData->getLeague()->toJson(),
            'fixture'             => $this->resultData->getFixture()->toJson()
        ]);

        if ($this->lineupsData) {
            return $fixtureData->merge($this->lineupsData->build()->toJson());
        }

        return $fixtureData;
    }

    public function isFinished(): bool
    {
        return $this->resultData->isFinished();
    }

    public function buildLineups(): Collection
    {
        return $this->lineupsData->build();
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

    public function getPlayedPlayers(): Collection
    {
        return $this->lineupsData->playedPlayers();
    }
}