<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Facades\File;


final readonly class LeagueImageFile
{
    private const DIR_PATH = 'leagues';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(int $leagueId): array
    {
        if (!$this->exists($leagueId)) {
            throw new Exception('LeagueImageが存在しません。');
        }
        
        $path = $this->generatePath($leagueId);

        $json = File::get($path);

        return json_decode($json);
    }

    public function write(int $leagueId, string $image)
    {
        File::put($this->generatePath($leagueId), $image);
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

    private function generatePath(int $leagueId): string
    {                
        $year = now()->year;

        return public_path(self::DIR_PATH.'/'.$year.'_'.$leagueId);
    }
}