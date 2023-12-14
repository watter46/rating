<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;


final readonly class PlayerImage
{
    private const DIR_PATH = 'images';

    private const SUMMER_SEASON = 'S';
    private const WINTER_SEASON = 'W';
    

    public function get(int $playerId): string
    {
        $year  = now()->year;
        $month = now()->month;
        
        $season = (8 <= $month && $month <= 12) ? self::SUMMER_SEASON : self::WINTER_SEASON;

        $fileName = $year.'_'.$season.'_'.$playerId;
        
        $path = public_path(self::DIR_PATH.'/'.$fileName);

        $image = File::get($path);
        
        return $image ? base64_encode($image) : '';
    }

    public function set()
    {
        //   
    }
}