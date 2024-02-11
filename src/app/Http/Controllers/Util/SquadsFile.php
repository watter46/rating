<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

use App\UseCases\Util\Season;


final readonly class SquadsFile
{
    private const DIR_PATH  = 'Template/squads';
    private const FILE_PATH = '.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(): Collection
    {
        if (!$this->exists()) {
            throw new Exception('SquadsFileが存在しません。');
        }
        
        $path = $this->generatePath();

        $squads = File::get($path);

        return collect(json_decode($squads));
    }

    public function write(Collection $squadsData)
    {
        File::put($this->generatePath(), $squadsData->toJson());
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
        return app_path(self::DIR_PATH.'/'.Season::current().self::FILE_PATH);
    }
}