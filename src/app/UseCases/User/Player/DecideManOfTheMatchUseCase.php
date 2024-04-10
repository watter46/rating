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
            $fixture = $this->playerInFixture->request($request);

            if (!$fixture->canRate()) {
                throw new Exception($fixture::RATE_PERIOD_EXPIRED_MESSAGE);
            }

            $newMomPlayer = $fixture
                ->getPlayer()
                ->decideMOM();

            /** @var Player $oldMomPlayer */
            $oldMomPlayer = Player::query()
                ->mom($request->getFixtureId())
                ?->first()
                ?->unDecideMOM();

            DB::transaction(function () use ($newMomPlayer, $oldMomPlayer) {
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