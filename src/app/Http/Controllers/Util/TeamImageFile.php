<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Facades\File;


final readonly class TeamImageFile
{
    private const DIR_PATH = 'teams';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(int $teamId): array
    {
        if (!$this->exists($teamId)) {
            throw new Exception('TeamImageが存在しません。');
        }
        
        $path = $this->generatePath($teamId);

        $json = File::get($path);

        return json_decode($json);
    }

    public function write(int $teamId, string $image)
    {
        File::put($this->generatePath($teamId), $image);
    }

    public function exists(int $teamId): bool
    {
        $path = $this->generatePath($teamId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = public_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(int $teamId): string
    {                
        $year = now()->year;

        return public_path(self::DIR_PATH.'/'.$year.'_'.$teamId);
    }
}