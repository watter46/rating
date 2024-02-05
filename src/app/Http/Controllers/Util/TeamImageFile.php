<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;
use App\UseCases\Api\ApiFootball;


final readonly class TeamImageFile
{
    private const DIR_PATH = 'teams';
    
    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }
    
    public function get(int $teamId): string
    {
        if (!$this->exists($teamId)) {
            throw new Exception('TeamImageが存在しません。');
        }
        
        $path = $this->generatePath($teamId);

        $image = File::get($path);

        return $image ? base64_encode($image) : '';
    }

    public function getByPath(string $path)
    {
        try {
            $image = File::get($path);

            return 'data:image/png;base64,'.base64_encode($image);

        } catch (FileNotFoundException $e) {
            return '';
        }
    }

    /**
     * チームの画像を保存する
     *
     * @param  array $fixturesFile
     * @return void
     */
    public function registerAll(array $fixturesFile)
    {
        $uniqueTeams = collect($fixturesFile)
            ->flatMap(function ($fixture) {
                return [
                    $fixture->teams->away,
                    $fixture->teams->home
                ];
            })
            ->unique('id');

        foreach($uniqueTeams as $team) {
            if ($this->exists($team->id)) continue;

            $image = ApiFootball::teamImage($team->id)->fetchImage();
            
            $this->write($team->id, $image);
        }
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

    public function generatePath(int $teamId): string
    {                
        $season = $this->season->current();

        return public_path(self::DIR_PATH.'/'.$season.'_'.$teamId);
    }
}