<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


final readonly class FixturesFile
{
    private const DIR_PATH  = 'Template/fixtures';
    private const FILE_PATH = 'fixtures.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(): Collection
    {        
        if (!$this->exists()) {
            throw new Exception('fixtureFileが存在しません。');
        }
        
        $path = $this->generatePath();

        $fixtures = File::get($path);

        return collect(json_decode($fixtures));
    }

    public function toJson()
    {
        $path = $this->generatePath();

        return File::get($path);
    }

    public function write(Collection $fixtures): void
    {
        File::put($this->generatePath(), $fixtures->toJson());
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