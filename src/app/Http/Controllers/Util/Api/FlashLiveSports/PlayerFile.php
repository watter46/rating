<?php declare(strict_types=1);

namespace App\Http\Controllers\Util\Api\FlashLiveSports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;


class PlayerFile
{
    private const DIR_PATH  = 'Template/flashLiveSports/Player';
    private const FILE_PATH = '.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(string $flashLiveSportsId): Collection
    {
        if (!$this->exists($flashLiveSportsId)) {
            throw new Exception('FlashLiveSportsPlayerFileが存在しません。');
        }
        
        $path = $this->generatePath($flashLiveSportsId);

        $squads = File::get($path);

        return collect(json_decode($squads));
    }

    public function write(string $flashLiveSportsId, Collection $playerData)
    {
        File::put($this->generatePath($flashLiveSportsId), $playerData->toJson());
    }

    public function exists(string $flashLiveSportsId): bool
    {
        $path = $this->generatePath($flashLiveSportsId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = app_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(string $flashLiveSportsId): string
    {                
        return app_path(self::DIR_PATH.'/'.$flashLiveSportsId.self::FILE_PATH);
    }
}