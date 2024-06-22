<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;

use Illuminate\Support\Collection;

use App\Models\FixtureInfo;


class TestFixtureInfoFile
{
    private const DIR_PATH = 'Template/tests/fixtureInfos/';

    private function dirPath()
    {
        return app_path(self::DIR_PATH);
    }

    private function fileName(int $external_fixture_id)
    {
        return $this->dirPath().$external_fixture_id.'.json';
    }

    public function get(int $external_fixture_id)
    {
        return json_decode(File::get($this->fileName($external_fixture_id)));
    }
    
    /**
     * gets
     *
     * @param  int[] $external_fixture_ids
     * @return Collection
     */
    public function gets(array $external_fixture_ids)
    {
        return collect($external_fixture_ids)
            ->map(function ($external_fixture_id) {
                return $this->get($external_fixture_id);
            });
    }

    public function getAll(): Collection
    {
        return collect(File::files($this->dirPath()))
            ->map(function (SplFileInfo $file) {
                return json_decode($file->getContents());
            });
    }

    public function write(int $external_fixture_id)
    {
        $fixtureInfo = FixtureInfo::query()
            ->where('external_fixture_id', $external_fixture_id)
            ->first();
        
        File::put($this->fileName($external_fixture_id), $fixtureInfo->toJson());
    }

    public function idList()
    {
        return collect(File::files($this->dirPath()))
            ->map(function (SplFileInfo $info) {
                $fileName = $info->getFilename();

                $id = Str::before($fileName, '.json');
                
                return (int) $id;
            });
    }

    public function writeAll()
    {
        $file = new FixtureFile;
        $file->getIdList();

        $testExternalFixtureIds = collect(File::files($this->dirPath()))
            ->map(function (SplFileInfo $info) {
                $fileName = $info->getFilename();

                $id = Str::before($fileName, '.json');
                
                return (int) $id;
            });

        $invalidExternalFixtureIds = $file->getIdList()->diff($testExternalFixtureIds);

        $invalidExternalFixtureIds
            ->each(function (int $external_fixture_id) {
                $this->write($external_fixture_id);
            });
    }
}