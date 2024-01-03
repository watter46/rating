<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use App\UseCases\Util\Season;
use Exception;
use Illuminate\Support\Facades\File;


final readonly class SquadsFile
{
    private const DIR_PATH  = 'Template/squads';
    private const FILE_PATH = '.json';

    private const SUMMER_SEASON = 'post_summer';
    private const WINTER_SEASON = 'post_winter';
    
    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }
    
    public function get(): array
    {
        if (!$this->exists()) {
            throw new Exception('SquadsFileが存在しません。');
        }
        
        $path = $this->generatePath();

        $json = File::get($path);

        return json_decode($json)->response;
    }

    public function write(string $fixtures)
    {
        File::put($this->generatePath(), $fixtures);
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
        
        return app_path(self::DIR_PATH.'/'.$season.self::FILE_PATH);
    }
}