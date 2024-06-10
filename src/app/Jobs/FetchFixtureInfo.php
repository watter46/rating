<?php declare(strict_types=1);

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

use App\UseCases\Admin\Fixture\RegisterFixtureInfo;


class FetchFixtureInfo implements ShouldQueue
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
    public function handle(): void
    {
        try {
            /** @var RegisterFixtureInfo $registerFixtureInfo */
            $registerFixtureInfo = app(RegisterFixtureInfo::class);
            
            $fixtureInfo = $registerFixtureInfo->execute(Cache::get('nextFixtureInfo')->id);
            
            if (!$fixtureInfo->isValid()) {
                $delay_min = 15;

                FetchFixtureInfo::dispatch()->delay(now('UTC')->addMinute($delay_min));
                return;
            }

            Cache::store('redis')->forget('nextFixtureInfo');

        } catch (Exception $e) {
            logger($e->getMessage());
        }
    }

    public function tries(): int
    {
        return 3;
    }
}