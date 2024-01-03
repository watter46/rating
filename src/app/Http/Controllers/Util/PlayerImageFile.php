<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use App\UseCases\Util\Season;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;


final readonly class PlayerImageFile
{
    private const DIR_PATH = 'images';
    private const DEFAULT_IMAGE_PATH = 'default_uniform.png';

    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }

    public function get(int $playerId): string
    {        
        $path = $this->generatePath($playerId);
        
        $image = File::get($path);
        
        return $image ? base64_encode($image) : '';
    }

    public function getByPath(string $path)
    {
        try {            
            $image = File::get($path);

            return [
                'exists' => true,
                'data'   => 'data:image/png;base64,'.base64_encode($image)
            ];
            
        } catch (FileNotFoundException $e) {
            $image = File::get(self::DEFAULT_IMAGE_PATH);

            return [
                'exists' => false,
                'data'   => 'data:image/png;base64,'.base64_encode($image)
            ];
        }
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

    public function generatePath(int $playerId): string
    {                
        $fileName = $this->season->current().'_'.$playerId;

        return public_path(self::DIR_PATH.'/'.$fileName);
    }

    public function findMissingFiles(array $idList): array
    {
        return collect($idList)
            ->filter(function ($player) {
                return !$this->exists($player['foot_player_id']);
            })
            ->toArray();
    }
}