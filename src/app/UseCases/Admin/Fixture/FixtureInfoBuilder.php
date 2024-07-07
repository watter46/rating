<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use App\Events\FixtureInfoRegistered;
use App\Models\FixtureInfo;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureStatusType;
use App\UseCases\Admin\Data\ApiFootball\FixturesData;
use App\UseCases\Admin\Fixture\FixtureInfoData\FilterInvalidPlayerInfos;
use App\UseCases\Admin\Fixture\FixtureInfoData\FixtureInfoDataValidator;
use Illuminate\Support\Collection;

class FixtureInfoBuilder
{
    private FilterInvalidPlayerInfos $filterInvalidPlayerInfos;

    private function __construct(private FixtureInfo $fixtureInfo)
    {
        $this->filterInvalidPlayerInfos = new filterInvalidPlayerInfos;
    }

    public static function create(FixtureInfo $fixtureInfo)
    {
        return new self($fixtureInfo);
    }

    /**
     * ApiFootballから取得したFixtureのデータを更新する
     *
     * @param  FixtureData $fixtureData
     * @return FixtureInfo
     */
    public function update(FixtureData $fixtureData): FixtureInfo
    {
        $this->fixtureInfo->lineups = $fixtureData->getLineups();
        $this->fixtureInfo->score   = $fixtureData->getScore();
        $this->fixtureInfo->fixture = $fixtureData->getFixture();
        $this->fixtureInfo->status  = FixtureStatusType::MatchFinished->value;

        return $this->fixtureInfo;
    }
    
    /**
     * buildFixtureInfosForUpsert
     *
     * @param  FixturesData $fixturesData
     * @return Collection<PlayerInfo>
     */
    public static function buildFixtureInfosForUpsert(FixturesData $fixturesData): Collection
    {
        return $fixturesData
            ->get()
            ->filter(function (FixtureData $fixtureData) {
                return $fixtureData->isSeasonTournament();
            })
            ->map(function (FixtureData $fixtureData) {
                $data = $fixtureData->getResultData();
                
                return new FixtureInfo([
                    'external_fixture_id' => $data['fixtureId'],
                    'external_league_id'  => $data['leagueId'],
                    'season'              => $data['season'],
                    'date'                => $data['date'],
                    'status'              => $data['status'],
                    'score'               => $data['score'],
                    'teams'               => $data['teams'],
                    'league'              => $data['league'],
                    'fixture'             => $data['fixture']
                ]);
            });
    }
    
    /**
     * Lineupsの数がLineupsDataの数と一致しているか
     *
     * @return bool
     */
    private function equalLineupCount(): bool
    {
        $fixtureInfo = $this->fixtureInfo->loadCount('playerInfos as lineupCount');

        $lineupsCount = $fixtureInfo->lineups->flatten(1)->count();

        $playerInfosCount = $fixtureInfo->lineupCount;

        return $lineupsCount === $playerInfosCount;
    }

    private function toData(): FixtureInfoData
    {
        return FixtureInfoData::create($this->fixtureInfo);
    }

    public function validator(): FixtureInfoDataValidator
    {
        return FixtureInfoDataValidator::validate($this->toData());
    }
    
    /**
     * invalidPlayerInfos
     *
     * @return Collection<array{ id: string, api_football_id: int, name: string, number: int }>
     */
    public function invalidPlayerInfos()
    {
        $players = $this->toData()->getPlayedPlayers();

        $invalidPlayerInfos = $this->filterInvalidPlayerInfos
            ->execute($players->pluck('id'))
            ->keyBy('api_football_id');

        return $players
            ->whereIn('id', $invalidPlayerInfos->pluck('api_football_id'))
            ->map(function (Collection $player) use ($invalidPlayerInfos) {
                $invalidPlayer = $invalidPlayerInfos->get($player['id']);
                
                return collect([
                    'id' => $invalidPlayer['player_info_id'],
                    'api_football_id' => $invalidPlayer['api_football_id'],
                    'name' => $player['name'],
                    'number' => $player['number']
                ]);
            });
    }

    /**
     * 試合を表示するのに必要なデータが存在しているか判定する
     *
     * @return bool
     */
    private function checkRequiredData(): bool
    {
        return $this->equalLineupCount()
            && $this->validator()->checkRequiredData()
            && $this->invalidPlayerInfos()->isNotEmpty();
    }
    
    public function dispatch(): void
    {
        if ($this->checkRequiredData()) {
            return;
        }

        FixtureInfoRegistered::dispatch($this);
    }
    
    // public function validated(): FixtureInfoDataValidator
    // {
    //     return FixtureInfoDataValidator::validate($this->fixtureData);
    // }

    // public function getPlayer(int $apiFootballId): array
    // {
    //     return $this->fixtureData
    //         ->getPlayedPlayers()
    //         ->filter(fn(array $player) => $player['id'] === $apiFootballId)
    //         ->first();
    // }

    // public function getPlayerInfoFotUpdate(int $apiFootballId): PlayerInfo
    // {
    //     // dd($this->fixtureData->);
        
    //     $player = $this->fixtureData
    //         ->getPlayedPlayers()
    //         ->filter(fn(array $player) => $player['id'] === $apiFootballId)
    //         ->first();

    //     dd($player);
    // }
    
    // public function isFinished(): bool
    // {
    //     return $this->fixtureData->isFinished();
    // }
}