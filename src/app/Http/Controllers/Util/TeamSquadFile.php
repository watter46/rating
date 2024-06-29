<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


class TeamSquadFile
{
    private const DIR_PATH  = 'Template/teamSquad';
    private const FILE_PATH = 'player_of_team.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(): Collection
    {
        if (!$this->exists()) {
            throw new Exception('TeamSquadFileが存在しません。');
        }
        
        $path = $this->generatePath();

        $teamSquad = File::get($path);

        return collect(json_decode($teamSquad));
    }

    public function write(Collection $teamSquad)
    {
        File::put($this->generatePath(), $teamSquad->toJson());
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
        return app_path(self::DIR_PATH.'/'.Season::current().'_'.self::FILE_PATH);
    }
}