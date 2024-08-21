<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;


final readonly class LeagueImageFile
{
    private const DIR_PATH = 'leagues';
    private const DEFAULT_IMAGE_PATH = 'question.png';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }

    public function get(int $leagueId)
    {        
        return File::get($this->generatePath($leagueId));
    }

    public function write(int $leagueId, string $image)
    {
        File::put($this->generatePath($leagueId), $image);
    }

    public function exists(int $leagueId): bool
    {
        return File::exists($this->generatePath($leagueId));
    }

    public function generatePath(int $leagueId): string
    {                
        return public_path(self::DIR_PATH.'/'.$leagueId);
    }

    public function generateViewPath(int $leagueId): string
    {
        return self::DIR_PATH.'/'.$leagueId;
    }

    public function defaultPath(): string
    {
        return self::DEFAULT_IMAGE_PATH;
    }

    private function ensureDirExists(): void
    {
        $path = public_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}