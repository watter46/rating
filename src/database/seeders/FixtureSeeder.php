<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Http\Controllers\Util\FixtureFile;
use Illuminate\Database\Seeder;
use Database\Mocks\Fixture\MockRegisterFixtureUseCase;


class FixtureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {   
        $list = (new FixtureFile)->getIdList();

        /** @var MockRegisterFixtureUseCase $registerFixture */
        $registerFixture = app(MockRegisterFixtureUseCase::class);
        
        $list->each(function ($id) use ($registerFixture) {
            $registerFixture->execute($id);
        });
    }
}