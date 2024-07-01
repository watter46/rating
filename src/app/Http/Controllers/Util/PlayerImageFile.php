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

    public function get(int $apiFootballId)
    {
        File::get($this->generatePath($apiFootballId));
    }

    public function write(int $apiFootballId, string $playerImage): void
    {        
        File::put($this->generatePath($apiFootballId), $playerImage);
    }

    public function exists(int $apiFootballId): bool
    {
        return File::exists($this->generatePath($apiFootballId));
    }

    public function generatePath(int $apiFootballId): string
    {
        return public_path(self::DIR_PATH.'/'.Season::current().'_'.$apiFootballId);
    }

    public function generateViewPath(int $apiFootballId)
    {
        return self::DIR_PATH.'/'.Season::current().'_'.$apiFootballId;
    }

    public function getDefaultPath(): string
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