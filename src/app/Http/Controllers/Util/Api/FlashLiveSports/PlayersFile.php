<?php declare(strict_types=1);

namespace App\Http\Controllers\Util\Api\FlashLiveSports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;


class PlayersFile
{
    private const DIR_PATH  = 'Template/flashLiveSports/Players';
    private const FILE_PATH = '.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(int $apiPlayerId): Collection
    {
        if (!$this->exists($apiPlayerId)) {
            throw new Exception('PlayersFileが存在しません。');
        }
        
        $path = $this->generatePath($apiPlayerId);

        $squads = File::get($path);

        return collect(json_decode($squads));
    }

    public function write(int $apiPlayerId, Collection $playerData)
    {
        File::put($this->generatePath($apiPlayerId), $playerData->toJson());
    }

    public function exists(int $apiPlayerId): bool
    {
        $path = $this->generatePath($apiPlayerId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = app_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(int $apiPlayerId): string
    {                
        return app_path(self::DIR_PATH.'/'.$apiPlayerId.self::FILE_PATH);
    }
}