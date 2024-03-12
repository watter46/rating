<?php declare(strict_types=1);

namespace App\Console\Commands;

use App\UseCases\Admin\Fixture\RegisterFixturesUseCase;
use Illuminate\Console\Command;

class UpdateFixturesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixtures:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixtureをアップデートする';

    /**
     * Execute the console command.
     */
    public function handle(RegisterFixturesUseCase $registerFixtures)
    {
        $registerFixtures->execute();
    }
}
