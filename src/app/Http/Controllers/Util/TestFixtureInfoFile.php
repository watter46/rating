<?php declare(strict_types=1);

namespace App\Http\Controllers\Util;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;
use Illuminate\Support\Str;

use Illuminate\Support\Collection;

use App\Models\FixtureInfo;


class TestFixtureInfoFile
{
    private const DIR_PATH = 'Template/tests/fixtureInfo/';

    private function dirPath()
    {
        return app_path(self::DIR_PATH);
    }

    private function fileName(int $api_fixture_id)
    {
        return $this->dirPath().$api_fixture_id.'.json';
    }

    public function get(int $api_fixture_id)
    {
        return collect(json_decode(File::get($this->fileName($api_fixture_id))));
    }
    
    /**
     * gets
     *
     * @param  int[] $api_fixture_ids
     * @return Collection
     */
    public function gets(array $api_fixture_ids)
    {
        return collect($api_fixture_ids)
            ->map(function ($api_fixture_id) {
                return $this->get($api_fixture_id);
            });
    }

    public function getAll(): Collection
    {
        return collect(File::files($this->dirPath()))
            ->map(function (SplFileInfo $file) {
                return collect(json_decode($file->getContents()));
            });
    }

    public function write(int $api_fixture_id)
    {
        $fixtureInfo = FixtureInfo::query()
            ->where('api_fixture_id', $api_fixture_id)
            ->first();
        
        File::put($this->fileName($api_fixture_id), $fixtureInfo->toJson());
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
            ->each(function (int $api_fixture_id) {
                $this->write($api_fixture_id);
            });
    }
}