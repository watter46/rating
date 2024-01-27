<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use App\UseCases\Player\Util\ApiFootball;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

use App\UseCases\Util\Season;


final readonly class LeagueImageFile
{
    private const DIR_PATH = 'leagues';
    
    public function __construct(private Season $season)
    {
        $this->ensureDirExists();
    }
    
    public function get(int $leagueId): string
    {
        if (!$this->exists($leagueId)) {
            throw new Exception('LeagueImageが存在しません。');
        }
        
        $path = $this->generatePath($leagueId);

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
     * リーグの画像を保存する
     *
     * @param  array $fixturesFile
     * @return void
     */
    public function registerAll(array $fixturesFile): void
    {
        $uniqueLeagues = collect($fixturesFile)
            ->map(fn ($fixture) => $fixture->league)
            ->unique('id');

        foreach($uniqueLeagues as $league) {
            if ($this->exists($league->id)) continue;

            $image = ApiFootball::leagueImage($league->id)->fetchImage();

            $this->write($league->id, $image);
        }
    }

    public function write(int $leagueId, string $image)
    {
        File::put($this->generatePath($leagueId), $image);
    }

    public function exists(int $leagueId): bool
    {
        $path = $this->generatePath($leagueId);

        return file_exists($path);
    }

    private function ensureDirExists(): void
    {
        $path = public_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    public function generatePath(int $leagueId): string
    {                
        $season = $this->season->current();

        return public_path(self::DIR_PATH.'/'.$season.'_'.$leagueId);
    }
}