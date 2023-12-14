<?php declare(strict_types=1);

namespace App\UseCases\Player\Get;

use App\UseCases\Player\Get\Util\File;
use Exception;


final readonly class GetStartingXIUseCase
{
    const DIR_PATH  = 'Template/startingXI/';
    const FILE_PATH = '_starting_xi.json';
    
    public function __construct()
    {
        //
    }

    public function execute(int $fixtureId)
    {
        try {            
            $startingXI = File::appPath(
                    dir: self::DIR_PATH,
                    fileName: self::FILE_PATH,
                    arg: $fixtureId
                )->get();

            return collect([
                'formation' => $startingXI[0]->formation,
                'startingXI' => collect($startingXI[0]->startXI)
            ]);
            
        } catch (Exception $e) {
            throw $e;
        }
    }
}