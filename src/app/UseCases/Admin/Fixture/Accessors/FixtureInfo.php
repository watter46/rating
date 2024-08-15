<?php declare(strict_types=1);


namespace App\UseCases\Admin\Fixture\Accessors;

use Illuminate\Support\Collection;

use App\Events\FixtureInfoRegistered;
use App\Models\FixtureInfo as FixtureInfoModel;
use App\Models\PlayerInfo as PlayerInfoModel;


class FixtureInfo
{
    private function __construct(
        private ?string $id,
        private Score $score,
        private Teams $teams,
        private League $league,
        private Fixture $fixture,
        private ?Lineups $lineups
    ) {
        //
    }

    public static function create(Collection $rawData)
    {
        $data = $rawData->fromStd()->toCollection();
        
        return new self(
            null,
            Score::create($data),
            Teams::create($data),
            League::create($data),
            Fixture::create($data),
            Lineups::create($data)
        );
    }

    public static function reconstruct(FixtureInfoModel $fixtureInfoModel): self
    {
        return new self(
            $fixtureInfoModel->id,
            Score::reconstruct($fixtureInfoModel->score),
            Teams::reconstruct($fixtureInfoModel->teams),
            League::reconstruct($fixtureInfoModel->league),
            Fixture::reconstruct($fixtureInfoModel->fixture),
            $fixtureInfoModel->lineups
                ? Lineups::reconstruct($fixtureInfoModel->lineups, $fixtureInfoModel->playerInfos)
                : null
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getFixtureId(): int
    {
        return $this->fixture->getFixtureId();
    }

    public function getPlayerIds()
    {
        return $this->lineups->getPlayerIds();
    }

    public function getInvalidPlayers()
    {
        return $this->lineups->getInvalidPlayers();
    }

    public function getInvalidTeamImageIds()
    {
        return $this->teams->getInvalidImageIds();
    }
    
    public function getInvalidLeagueImageIds()
    {
        if ($this->league->hasImage()) {
            return collect();
        }

        return collect($this->league->getLeagueId());
    }

    public function getInvalidImagePlayers()
    {
        return $this->lineups->getInvalidImagePlayers();
    }
    
    /**
     * @param  FixtureInfo $fixtureInfo
     * @return self
     */
    public function update(FixtureInfo $fixtureInfo): self
    {
        return new self(
            $this->id,
            $fixtureInfo->score,
            $fixtureInfo->teams,
            $fixtureInfo->league,
            $fixtureInfo->fixture,
            $fixtureInfo->lineups
        );
    }
    
    /**
     * 更新されたPlayerInfoのみを最新のモデルにする
     *
     * @return self
     */
    public function refreshPlayerInfos()
    {
        $playerIds = $this->lineups
            ->getInvalidPlayers()
            ->map(fn (Player $player) => $player->getPlayerId());

        $playerInfoModels = PlayerInfoModel::query()
            ->whereIn('api_football_id', $playerIds)
            ->get();
        
        return new self(
            $this->id,
            $this->score,
            $this->teams,
            $this->league,
            $this->fixture,
            $this->lineups->updatePlayerInfos($playerInfoModels)
        );
    }
    
    /**
     * FixtureInfoに紐づいていないPlayerInfoを取得する
     *
     * @return self
     */
    public function updatePlayerInfos()
    {
        $playerIds = $this->lineups->getNeedsRegisterPlayerIds();

        $playerInfoModels = PlayerInfoModel::query()
            ->whereIn('api_football_id', $playerIds)
            ->get();
        
        return new self(
            $this->id,
            $this->score,
            $this->teams,
            $this->league,
            $this->fixture,
            $this->lineups->updatePlayerInfos($playerInfoModels)
        );
    }

    public function assignId(string $fixtureInfoId): self
    {
        return new self(
            $fixtureInfoId,
            $this->score,
            $this->teams,
            $this->league,
            $this->fixture,
            $this->lineups
        );
    }

    public function buildFill(): array
    {
        return [
            'date'    => $this->fixture->getDate(),
            'is_end'  => $this->fixture->isEnd(),
            'score'   => $this->score->toModel(),
            'teams'   => $this->teams->toModel(),
            'league'  => $this->league->toModel(),
            'fixture' => $this->fixture->toModel(),
            'lineups' => $this->lineups ? $this->lineups->toModel() : collect()
        ];
    }

    public function toModel()
    {
        $model = new FixtureInfoModel($this->toArray());
        
        if ($this->id) {
            return $model->setAttribute('id', $this->id);
        }

        return $model;
    }

    private function toArray()
    {
        return collect([
            'api_fixture_id' => $this->getFixtureId(),
            'api_league_id'  => $this->league->getLeagueId(),
            'season'         => $this->fixture->getSeason(),
            'date'           => $this->fixture->getDate(),
            'is_end'         => $this->fixture->isEnd(),
            'score'          => $this->score->toModel(),
            'teams'          => $this->teams->toModel(),
            'league'         => $this->league->toModel(),
            'fixture'        => $this->fixture->toModel(),
            'lineups'        => $this->lineups ? $this->lineups->toModel() : collect()
        ])
        ->toArray();
    }

    public function shouldDispatch(): bool
    {
        return !$this->teams->hasImages()
            || !$this->league->hasImage()
            || !$this->lineups->hasImages()
            || !$this->lineups->equalLineupsPlayerInfosCount();
    }

    public function dispatch(): void
    {
        FixtureInfoRegistered::dispatch($this);
    }
}