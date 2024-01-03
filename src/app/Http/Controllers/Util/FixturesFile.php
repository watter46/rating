<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use App\UseCases\Util\Season;
use Exception;
use Illuminate\Support\Facades\File;


final readonly class FixturesFile
{
    private const DIR_PATH  = 'Template/fixtures';
    private const FILE_PATH = 'fixtures.json';
    
    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }
    
    public function get(): array
    {
        if (!$this->exists()) {
            throw new Exception('fixtureFileが存在しません。');
        }
        
        $path = $this->generatePath();

        $json = File::get($path);

        return json_decode($json);
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
        
        return app_path(self::DIR_PATH.'/'.$season.'_'.self::FILE_PATH);
    }
}