<?php

namespace Database\Seeders;

use App\Http\Controllers\Util\FixtureFile;
use Database\Stubs\Fixture\StubRegisterFixtureInfo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixtureInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $list = (new FixtureFile)->getIdList();

        /** @var StubRegisterFixtureInfo $registerFixtureInfo */
        $registerFixtureInfo = app(StubRegisterFixtureInfo::class);
        
        $list->each(function ($id) use ($registerFixtureInfo) {
            $registerFixtureInfo->execute($id);
        });
    }
}
