<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


final readonly class LeagueImageFile
{
    private const DIR_PATH = 'leagues';
    private const DEFAULT_IMAGE_PATH = 'question.png';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }

    public function write(int $leagueId, string $image)
    {
        File::put($this->generatePath($leagueId), $image);
    }
    
    public function existsOrDefault(int $leagueId): string
    {
        if ($this->exists($leagueId)) {
            return $this->generatePath($leagueId);
        }

        return self::DEFAULT_IMAGE_PATH;
    }

    public function exists(int $leagueId): bool
    {
        $path = $this->generatePath($leagueId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = public_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function generatePath(int $leagueId): string
    {                
        return self::DIR_PATH.'/'.Season::current().'_'.$leagueId;
    }
}