<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\Processors\FixtureInfo;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\Fixture\Processors\FixtureInfo\FixtureInfoData;
use App\UseCases\Admin\Fixture\ValidatorInterface;


readonly class FixtureInfoDataValidator implements ValidatorInterface
{
    private Collection $invalidTeamIds;
    private Collection $invalidLeagueIds;
    private Collection $invalidPlayerImageIds;
    
    private function __construct(private FixtureInfoData $fixtureInfoData)
    {
        $this->validateTeamIds();
        $this->validateLeagueIds();
        $this->validatePlayerImage();
    }

    /**
     * データがすべて存在しているか判定する
     *
     * @return bool
     */
    public function checkRequiredData(): bool
    {
        return collect([
            $this->getInvalidTeamIds(),
            $this->getInvalidLeagueIds(),
            $this->getInvalidPlayerImageIds()
        ])
        ->every(fn (Collection $ids) => $ids->isEmpty());
    }
    
    /**
     * FixtureData
     *
     * @param  FixtureInfoData $fixtureInfoData
     * @return self
     */
    public static function validate(FixtureInfoData $fixtureInfoData): self
    {
        return new self($fixtureInfoData);
    }
    
    /**
     * 保存されていないチームIDを取得する
     *
     * @return Collection
     */
    public function getInvalidTeamIds(): Collection
    {
        return $this->invalidTeamIds;
    }
    
    /**
     * 保存されていないリーグIDを取得する
     *
     * @return Collection
     */
    public function getInvalidLeagueIds(): Collection
    {
        return $this->invalidLeagueIds;
    }
    
    /**
     * 保存されていない選手の画像のPlayerIDを取得する
     *
     * @return Collection
     */
    public function getInvalidPlayerImageIds(): Collection
    {
        return $this->invalidPlayerImageIds;
    }
    
    public function validateTeamIds(): void
    {
        $file = new TeamImageFile();

        $this->invalidTeamIds = $this->fixtureInfoData
            ->getTeamIds()
            ->filter(fn (int $teamId) => !$file->exists($teamId));
    }
    
    public function validateLeagueIds(): void
    {
        $file = new LeagueImageFile();

        $leagueId = $this->fixtureInfoData->getLeagueId();

        $this->invalidLeagueIds = $file->exists($leagueId)
            ? collect()
            : collect($leagueId);
    }
    
    public function validatePlayerImage(): void
    {
        if (!$this->fixtureInfoData->lineupsExists()) {
            $this->invalidPlayerImageIds = collect();
            return;
        }
        
        $file = new PlayerImageFile();

        $this->invalidPlayerImageIds = $this->fixtureInfoData->getPlayedPlayers()
            ->map(fn($player) => $player['id'])
            ->filter(fn($playerId) => !$file->exists($playerId));
    }
}