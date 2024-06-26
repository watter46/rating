<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;


use Exception;
use SplFileInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use App\UseCases\Util\Season;


final readonly class PlayerFile
{
    private const DIR_PATH  = 'Template/players';
    private const FILE_PATH = '.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(int $playerId): Collection
    {
        if (!$this->exists($playerId)) {
            throw new Exception('PlayerFileが存在しません。');
        }
        
        $path = $this->generatePath($playerId);

        return collect(json_decode(File::get($path)));
    }

    public function getAll(): Collection
    {
        $files = File::files(app_path(self::DIR_PATH));
        
        return collect($files)->map(function (SplFileInfo $file) {
            return Str::before($file->getFilename(), '.json');
        });
    }

    public function write(int $playerId, Collection $player)
    {
        File::put($this->generatePath($playerId), $player->toJson());
    }

    public function exists(int $playerId): bool
    {
        $path = $this->generatePath($playerId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = app_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(int $playerId): string
    {                
        return app_path(self::DIR_PATH.'/'.$playerId.self::FILE_PATH);
    }
}