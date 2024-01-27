<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use App\UseCases\Util\Season;
use Exception;
use Illuminate\Support\Facades\File;


final readonly class PlayerOfTeamFile
{
    private const DIR_PATH  = 'Template/playerOfTeam';
    private const FILE_PATH = 'player_of_team.json';
    
    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }
    
    public function get(): string
    {
        if (!$this->exists()) {
            throw new Exception('PlayerOfTeamFileが存在しません。');
        }
        
        $path = $this->generatePath();

        return File::get($path);
    }

    public function write(string $json)
    {
        File::put($this->generatePath(), $json);
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