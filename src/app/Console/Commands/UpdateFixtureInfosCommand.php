<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\UseCases\Admin\Fixture\RegisterFixtureInfos;


class UpdateFixtureInfosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtureInfos:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'FixtureInfoをアップデートする';

    /**
     * Execute the console command.
     */
    public function handle(RegisterFixtureInfos $registerFixtureInfos)
    {
        $registerFixtureInfos->execute();
    }
}
