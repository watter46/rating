<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

use App\Models\Player;
use App\UseCases\User\PlayerInFixture;
use App\UseCases\User\PlayerInFixtureRequest;

final readonly class DecideManOfTheMatchUseCase
{
    public function __construct(private PlayerInFixture $playerInFixture)
    {
        //
    }
    
    public function execute(PlayerInFixtureRequest $request)
    {
        try {
            $playerInFixture = $this->playerInFixture->request($request);

            if ($playerInFixture->exceedRatePeriodDay()) {
                throw new Exception($playerInFixture::RATE_PERIOD_EXPIRED_MESSAGE);
            }

            if ($playerInFixture->exceedRateLimit()) {
                throw new Exception($playerInFixture::RATE_LIMIT_EXCEEDED_MESSAGE);
            }

            if ($playerInFixture->exceedMomLimit()) {
                throw new Exception($playerInFixture::MOM_LIMIT_EXCEEDED_MESSAGE);
            }

            $newMomPlayer = $playerInFixture
                ->getPlayer()
                ->decideMOM();

            /** @var Player $oldMomPlayer */
            $oldMomPlayer = Player::query()
                ->mom($request->getFixtureId())
                ?->first()
                ?->unDecideMOM();

            $fixture = $playerInFixture
                ->getFixture()
                ->incrementMomCount();

            DB::transaction(function () use ($newMomPlayer, $oldMomPlayer, $fixture) {
                $fixture->save();
                
                $newMomPlayer->save();

                if (!$oldMomPlayer) return;
                
                $oldMomPlayer->save();
            });

            return collect([
                'newMomPlayerInfoId' => $newMomPlayer->player_info_id,
                'oldMomPlayerInfoId' => $oldMomPlayer?->player_info_id,
            ]);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}