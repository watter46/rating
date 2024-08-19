<?php declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateApiFootBallIds;
use App\UseCases\Admin\Player\UpdatePlayerInfos\UpdateFlashIds;


class UpdatePlayerInfos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        UpdateApiFootBallIds $updateApiFootBallIds,
        UpdateFlashIds $updateFlashIds): void
    {
        $updateApiFootBallIds->execute();
        $updateFlashIds->execute();
    }
}
