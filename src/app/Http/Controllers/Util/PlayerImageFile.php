<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;


final readonly class PlayerImageFile
{
    private const DIR_PATH = 'images';

    private const SUMMER_SEASON = 'S';
    private const WINTER_SEASON = 'W';

    public function __construct()
    {
        $this->ensureDirExists();
    }

    public function get(int $playerId): string
    {        
        $path = $this->generatePath($playerId);
        
        $image = File::get($path);
        
        return $image ? base64_encode($image) : '';
    }

    public function write(int $playerId, string $image): void
    {
        $path = $this->generatePath($playerId);
        
        File::put($path, $image);
    }

    public function exists(int $playerId): bool
    {
        $path = $this->generatePath($playerId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = public_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(int $playerId): string
    {        
        $year  = now()->year;
        $month = now()->month;
        
        $season = (8 <= $month && $month <= 12) ? self::SUMMER_SEASON : self::WINTER_SEASON;

        $fileName = $year.'_'.$season.'_'.$playerId;
        
        return public_path(self::DIR_PATH.'/'.$fileName);
    }
}