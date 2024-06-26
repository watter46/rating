<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;


use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


final readonly class PlayerImageFile
{
    private const DIR_PATH = 'images';
    private const DEFAULT_IMAGE_PATH = 'default_uniform.png';

    public function __construct()
    {
        $this->ensureDirExists();
    }

    public function getDefaultPath(): string
    {
        return self::DEFAULT_IMAGE_PATH;
    }

    public function get(int $playerId)
    {
        File::get($this->generatePath($playerId));
    }

    public function write(int $playerId, string $playerImage): void
    {        
        File::put($this->generatePath($playerId), $playerImage);
    }

    public function exists(int $playerId): bool
    {
        return File::exists($this->generatePath($playerId));
    }

    private function ensureDirExists(): void
    {
        $path = public_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function generatePath(int $playerId): string
    {
        return public_path(self::DIR_PATH.'/'.Season::current().'_'.$playerId);
    }
}