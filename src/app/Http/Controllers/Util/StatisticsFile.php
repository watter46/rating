<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Facades\File;


final readonly class StatisticsFile
{
    private const DIR_PATH  = 'Template/statistics';
    private const FILE_PATH = 'player_statistic.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(int $fixtureId): array
    {
        if (!$this->exists($fixtureId)) {
            throw new Exception('StatisticsFileが存在しません。');
        }
        
        $path = $this->generatePath($fixtureId);

        $json = File::get($path);

        return json_decode($json)->response;
    }

    public function json(int $fixtureId)
    {
        if (!$this->exists($fixtureId)) {
            throw new Exception('StatisticsFileが存在しません。');
        }
        
        $path = $this->generatePath($fixtureId);

        $json = File::get($path);

        return $json;
    }

    public function write(int $fixtureId, string $statistic)
    {
        File::put($this->generatePath($fixtureId), $statistic);
    }

    public function exists(int $fixtureId): bool
    {
        $path = $this->generatePath($fixtureId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = app_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(int $fixtureId): string
    {        
        return app_path(self::DIR_PATH.'/'.$fixtureId.'_'.self::FILE_PATH);
    }
}