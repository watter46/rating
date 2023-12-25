<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;


final readonly class PlayerListFile
{
    private const DIR_PATH  = 'Template/playerList';
    private const FILE_PATH = 'player_list.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get()
    {
        if (!$this->exists()) {
            throw new Exception('playerListFileが存在しません。');
        }
        
        $path = $this->generatePath();

        $json = File::get($path);

        return json_decode($json);
    }

    public function write(string $playerListJson)
    {
        File::put($this->generatePath(), $playerListJson);
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
        $year = now()->year;

        return app_path(self::DIR_PATH.'/'.$year.'_'.self::FILE_PATH);
    }
}