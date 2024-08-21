<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

use App\Models\FixtureInfo as FixtureInfoModel;
use App\Http\Controllers\Util\FixtureInfoFile;


class FixtureInfosSeeder extends Seeder
{
    /** 2023年の試合をすべて保存する */
    public function run(): void
    {
        /** @var FixtureInfoFile $file */
        $file = app(FixtureInfoFile::class);

        $fixtureInfos = $file->get(2024)
            ->map(function (Collection $fixture) {
                $model = new FixtureInfoModel($fixture->toArray());

                return $model->castsToJson();
            });

        FixtureInfoModel::upsert(
            $fixtureInfos->toArray(),
            FixtureInfoModel::UPSERT_UNIQUE
        );
    }
}