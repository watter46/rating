<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Util\PlayerImageFile;
use App\Http\Controllers\Util\SquadsFile;
use App\UseCases\Player\Util\FootApiFetcher;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Playerのイメージ画像を取得できるAPI
 * 
 * https://rapidapi.com/fluis.lacasse/api/footapi7
 * 
 * rate: 50req/day 4req/sec
 */
class FootApiController extends Controller
{
    public function fetchPlayerImages(SquadsFile $squadsFile, PlayerImageFile $imageFile)
    {
        if (!$squadsFile->exists()) {
            dd('not exists');
        }

        $playerIdList = collect($squadsFile->get()[0]->players)->pluck('id');
        
        $invalidIdList = $playerIdList
            ->filter(function ($playerId) use ($imageFile) {
                return !$imageFile->exists($playerId);
            });
                        
        foreach($invalidIdList as $playerId) {            
            if ($imageFile->exists($playerId)) {
                continue;
            }
            
            $image = FootApiFetcher::playerImage((string) $playerId)->fetch();

            $imageFile->write($playerId, $image);
        }
    }
}
