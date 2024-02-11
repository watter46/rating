<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

use App\UseCases\Util\Season;


final readonly class PlayerOfTeamFile
{
    private const DIR_PATH  = 'Template/playerOfTeam';
    private const FILE_PATH = 'player_of_team.json';
    
    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }
    
    public function get(): Collection
    {
        if (!$this->exists()) {
            throw new Exception('PlayerOfTeamFileが存在しません。');
        }
        
        $path = $this->generatePath();

        $playersOfTeam = File::get($path);

        return collect(json_decode($playersOfTeam));
    }

    public function write(Collection $playersOfTeamData)
    {
        File::put($this->generatePath(), $playersOfTeamData->toJson());
    }

    public function exists(): bool
    {
        $path = $this->generatePath();

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = app_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(): string
    {                
        $season = $this->season->current();

        return app_path(self::DIR_PATH.'/'.$season.'_'.self::FILE_PATH);
    }
}