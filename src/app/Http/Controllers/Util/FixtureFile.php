<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Facades\File;


final readonly class FixtureFile
{
    private const DIR_PATH  = 'Template/fixture';
    private const FILE_PATH = 'fixture.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(int $fixtureId): array
    {
        if (!$this->exists($fixtureId)) {
            throw new Exception('fixtureFileが存在しません。');
        }
        
        $path = $this->generatePath($fixtureId);

        $json = File::get($path);

        return json_decode($json);
    }

    public function write(int $fixtureId, string $fixtureJson)
    {
        File::put($this->generatePath($fixtureId), $fixtureJson);
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