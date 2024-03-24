<?php declare(strict_types=1);

namespace App\Jobs;

use App\Models\Fixture;
use App\Models\Stub;
use App\UseCases\Admin\Fixture\RegisterFixtureUseCase;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FetchFixture implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private readonly RegisterFixtureUseCase $registerFixture;
    
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
            /** @var RegisterFixtureUseCase $registerFixture */
            $registerFixture = app(RegisterFixtureUseCase::class);
            
            $fixture = $registerFixture->execute(Cache::get('nextFixture')->id);
            
            if (!$fixture->isValid()) {
                $delay_min = 15;

                FetchFixture::dispatch()->delay(now('UTC')->addMinute($delay_min));
                return;
            }

            Cache::store('redis')->forget('nextFixture');

        } catch (Exception $e) {
            logger($e->getMessage());
        }
    }

    public function tries(): int
    {
        return 3;
    }
}