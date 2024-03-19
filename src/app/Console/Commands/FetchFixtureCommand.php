<?php declare(strict_types=1);

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

use App\Models\Fixture;
use App\UseCases\Admin\Fixture\RegisterFixtureUseCase;


class FetchFixtureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixture:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixtureを取得する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            /** @var Fixture $fixture */
            $fixture = Cache::get('nextFixture');

            /** @var RegisterFixtureUseCase $registerFixture */
            $registerFixture = app(RegisterFixtureUseCase::class);

            $registerFixture->execute($fixture->id);
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}