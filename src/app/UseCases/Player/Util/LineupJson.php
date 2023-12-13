<?php declare(strict_types=1);

namespace App\UseCases\Player\Util;

use Illuminate\Support\Facades\File;


final readonly class LineupJson
{
    private const DIR_PATH  = 'Template/startingXI';
    private const FILE_PATH = '_starting_xi.json';

    public static function get(int $fixtureId): array
    {
        $path = app_path(self::DIR_PATH.'/'.$fixtureId.self::FILE_PATH);

        $json = File::get($path);

        return json_decode($json)->response;
    }

    public function set()
    {
        //
    }
}