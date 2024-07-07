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
    
    public function get(int $apiFootballId): Collection
    {
        if (!$this->exists($apiFootballId)) {
            throw new Exception('PlayersFileが存在しません。');
        }
        
        $path = $this->generatePath($apiFootballId);

        $squads = File::get($path);

        return collect(json_decode($squads));
    }

    public function write(int $apiFootballId, Collection $playerData)
    {
        File::put($this->generatePath($apiFootballId), $playerData->toJson());
    }

    public function exists(int $apiFootballId): bool
    {
        $path = $this->generatePath($apiFootballId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = app_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(int $apiFootballId): string
    {                
        return app_path(self::DIR_PATH.'/'.$apiFootballId.self::FILE_PATH);
    }
}