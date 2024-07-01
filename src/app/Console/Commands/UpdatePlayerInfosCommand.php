<?php declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateApiFootBallIds;
use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateFlashLiveSportsIds;


class UpdatePlayerInfosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'playerInfos:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'PlayerInfoをアップデートする';

    /**
     * Execute the console command.
     */
    public function handle(
        UpdateApiFootBallIds $updateApiFootBallIds,
        UpdateFlashLiveSportsIds $updateFlashLiveSportsIds)
    {
        $updateApiFootBallIds->execute();
        $updateFlashLiveSportsIds->execute();
    }
}
