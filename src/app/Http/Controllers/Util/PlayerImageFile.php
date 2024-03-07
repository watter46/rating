<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

use App\Models\PlayerInfo;
use App\UseCases\Util\Season;
use App\UseCases\Api\SofaScore\PlayerImageFetcher;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

final readonly class PlayerImageFile
{
    private const DIR_PATH = 'images';
    private const DEFAULT_IMAGE_PATH = 'default_uniform.png';

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

    public function write(int $playerId, string $playerImage): void
    {
        $path = $this->generatePath($playerId);
        
        File::put($path, $playerImage);
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
        $fileName = Season::current().'_'.$playerId;

        return public_path(self::DIR_PATH.'/'.$fileName);
    }
    
    /**
     * registerAll
     *
     * @param  Collection<int, PlayerInfo> $playerInfos
     * @return void
     */
    public function registerAll(Collection $playerInfos): void
    {
        foreach($playerInfos as $player) {
            try {
                $playerImage = (new PlayerImageFetcher($this))->fetch($player->sofa_player_id);

                $this->write($player->foot_player_id, $playerImage);

            } catch(ClientException $e) {
                continue;
                
            } catch (Exception $e) {
                Log::alert($e->getMessage());
                
                throw $e;
            }
        }
    }
}