<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture\FixtureInfoData;

use Illuminate\Support\Collection;

use App\Http\Controllers\Util\LeagueImageFile;
use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\TeamImageFile;
use App\UseCases\Admin\Fixture\Data\FixtureData;
use App\UseCases\Admin\Fixture\ValidatorInterface;
use App\UseCases\Admin\Fixture\FixtureInfoData\FilterInvalidFootPlayerIds;


readonly class FixtureInfoDataValidator implements ValidatorInterface
{
    /** @var Collection<array{ id: int, name: string }> $invalidPlayers */
    private Collection $invalidPlayers;
    private Collection $invalidTeamIds;
    private Collection $invalidLeagueIds;
    private Collection $invalidPlayerImageIds;
    
    private function __construct(private FixtureData $fixtureData)
    {
        $this->validateTeamIds();
        $this->validateLeagueIds();
        $this->validatePlayers();
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
            $this->getInvalidPlayers(),
            $this->getInvalidPlayerImageIds()
        ])
        ->every(fn (Collection $ids) => $ids->isEmpty());
    }
    
    /**
     * FixtureData
     *
     * @param  FixtureData $fixtureData
     * @return self
     */
    public static function validate(FixtureData $fixtureData): self
    {
        return new self($fixtureData);
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
     * 保存されていないPlayerIDを取得する
     *
     * @return Collection
     */
    public function getInvalidPlayers(): Collection
    {
        return $this->invalidPlayers;
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

        $this->invalidTeamIds = $this->fixtureData->getTeams()
            ->map(fn ($team) => $team->get('id'))
            ->values()
            ->filter(fn (int $teamId) => !$file->exists($teamId));
    }
    
    public function validateLeagueIds(): void
    {
        $file = new LeagueImageFile();

        $leagueId = $this->fixtureData->getLeague()->get('id');

        $this->invalidLeagueIds = $file->exists($leagueId)
            ? collect()
            : collect($leagueId);
    }
    
    public function validatePlayers(): void
    {
        $players = $this->fixtureData->getPlayedPlayers();

        $invalidFootPlayerIds = (new FilterInvalidFootPlayerIds)->execute($players->pluck('id'));

        $this->invalidPlayers = $players
            ->whereIn('id', $invalidFootPlayerIds->toArray())
            ->map(fn(array $player) =>
                collect($player)->only(['id', 'name'])->toArray()
            );
    }
    
    public function validatePlayerImage(): void
    {
        $file = new PlayerImageFile();

        $this->invalidPlayerImageIds = $this->fixtureData->getPlayedPlayers()
            ->map(fn($player) => $player['id'])
            ->filter(fn($playerId) => !$file->exists($playerId));
    }
}