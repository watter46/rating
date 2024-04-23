<?php

namespace Database\Seeders;

use Database\Stubs\Fixture\StubRegisterFixtureInfos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixtureInfosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var StubRegisterFixtureInfos $registerFixtureInfos */
        $registerFixtureInfos = app(StubRegisterFixtureInfos::class);

        $registerFixtureInfos->execute();
    }
}
