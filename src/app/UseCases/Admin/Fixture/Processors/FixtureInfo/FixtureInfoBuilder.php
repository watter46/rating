<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Processors\FixtureInfo;

use Illuminate\Support\Collection;

use App\Events\FixtureInfoRegistered;
use App\Models\FixtureInfo;
use App\Models\PlayerInfo;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureData;
use App\UseCases\Admin\Data\ApiFootball\FixtureData\FixtureStatusType;
use App\UseCases\Admin\Fixture\Processors\FixtureInfo\FilterInvalidPlayerInfos;
use App\UseCases\Admin\Fixture\Processors\FixtureInfo\FixtureInfoDataValidator;


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

    public function getFixtureInfo(): FixtureInfo
    {
        return $this->fixtureInfo;
    }
    
    /**
     * getApiFootballIds
     *
     * @return Collection<int>
     */
    public function getApiFootballIds(): Collection
    {
        return $this->toData()->getPlayedPlayers()->pluck('id');
    }
    
    /**
     * getTeamImageIds
     *
     * @return Collection<int>
     */
    public function getInvalidTeamImageIds(): Collection
    {
        return $this->validator()->getInvalidTeamIds();
    }

    /**
     * getTeamImageIds
     *
     * @return Collection<int>
     */
    public function getInvalidLeagueImageIds(): Collection
    {
        return $this->validator()->getInvalidLeagueIds();
    }

    /**
     * getTeamImageIds
     *
     * @return Collection<PlayerInfo>
     */
    public function getInvalidPlayerImageIds(): Collection
    {
        $invalidPlayerImageIds = $this->validator()->getInvalidPlayerImageIds();
        
        $playerInfos = $this->fixtureInfo
            ->load('playerInfos:api_football_id,flash_live_sports_image_id')
            ->playerInfos;
        
        return $playerInfos
            ->whereIn('api_football_id', $invalidPlayerImageIds->toArray())
            ->filter(fn (PlayerInfo $playerInfo) => $playerInfo->flash_live_sports_image_id);
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
}