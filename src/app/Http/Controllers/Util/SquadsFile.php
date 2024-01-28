<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


final readonly class SquadsFile
{
    private const DIR_PATH  = 'Template/squads';
    private const FILE_PATH = '.json';
    
    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }
    
    public function get(): string
    {
        if (!$this->exists()) {
            throw new Exception('SquadsFileが存在しません。');
        }
        
        $path = $this->generatePath();

        return File::get($path);
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