<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\UseCases\User\PlayerInFixture;
use App\UseCases\User\PlayerInFixtureRequest;


final readonly class RatePlayerUseCase
{
    public function __construct(private PlayerInFixture $playerInFixture)
    {
        //
    }

    public function execute(PlayerInFixtureRequest $request, float $rating)
    {
        try {
            $fixture = $this->playerInFixture->request($request);

            if ($fixture->exceedPeriodDay()) {
                throw new Exception($fixture::RATE_PERIOD_EXPIRED_MESSAGE);
            }

            if ($fixture->exceedRateLimit()) {
                throw new Exception($fixture::RATE_LIMIT_EXCEEDED_MESSAGE);
            }

            $player = $fixture
                ->getPlayer()
                ->rate($rating);
                
            DB::transaction(function () use ($player) {
                $player->save();
            });

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}