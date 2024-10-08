<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

use App\Models\Fixture;


final readonly class FixtureFile
{
    private const DIR_PATH  = 'Template/fixture';
    private const FILE_PATH = 'fixture.json';
    
    public function __construct()
    {
        $this->ensureDirExists();
    }
    
    public function get(int $fixtureId): Collection
    {
        if (!$this->exists($fixtureId)) {
            throw new Exception('fixtureFileが存在しません。');
        }
        
        $path = $this->generatePath($fixtureId);

        $fixture = File::get($path);

        return collect(json_decode($fixture));
    }

    public function write(int $fixtureId, Collection $fixture)
    {        
        File::put($this->generatePath($fixtureId), $fixture->toJson());
    }

    public function exists(int $fixtureId): bool
    {
        $path = $this->generatePath($fixtureId);

        return file_exists($path);
    }

    public function isFinished(int $fixtureId): bool
    {
        $fixture = $this->get($fixtureId);

        if ($fixture['fixture']->status->long === 'Not Started') {
            return false;
        }

        return true;
    }

    public function bulkWrite(Collection $fixtures)
    {
        $fixtures->each(function (Fixture $fixture) {
            if ($this->exists($fixture->external_fixture_id)) {
                return true;
            }
            
            $this->write($fixture->external_fixture_id, $fixture->fixture->toJson());
        });
    }

    public function getIdList(): Collection
    {
        $path = app_path(self::DIR_PATH);

        return collect(File::files($path))
            ->map(function (SplFileInfo $info) {
                $fileName = $info->getFilename();

                $id = Str::before($fileName, '_'.self::FILE_PATH);
                
                return (int) $id;
            });
    }

    private function ensureDirExists(): void
    {
        $path = app_path(self::DIR_PATH);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function generatePath(int $fixtureId): string
    {                
        return app_path(self::DIR_PATH.'/'.$fixtureId.'_'.self::FILE_PATH);
    }
}