<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


final readonly class TeamImageFile
{
    private const DIR_PATH = 'teams';
    private const DEFAULT_IMAGE_PATH = 'question.png';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }

    public function write(int $teamId, string $teamImage)
    {
        File::put($this->generatePath($teamId), $teamImage);
    }

    public function exists(int $teamId): bool
    {
        $path = $this->generatePath($teamId);
        
        return File::exists($path);
    }

    public function generatePath(int $teamId): string
    {                
        return public_path(self::DIR_PATH.'/'.Season::current().'_'.$teamId);
    }

    public function generateViewPath(int $teamId): string
    {
        return self::DIR_PATH.'/'.Season::current().'_'.$teamId;
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