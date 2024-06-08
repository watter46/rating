<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\FixtureInfo;


final readonly class CountRatedPlayer
{
    public function execute(string $fixtureInfoId)
    {
        try {
            $fixtureInfo = FixtureInfo::query()
                ->select(['id'])
                ->with([
                    'fixture' => fn($query) => $query
                        ->select(['id','fixture_info_id'])
                        ->withCount('ratedPlayers as ratedCount')
                ])
                ->withCount(['playerInfos as playerCount'])
                ->find($fixtureInfoId);

            return [
                'ratedCount'  => $fixtureInfo->getRelation('fixture')->ratedCount ?? 0,
                'playerCount' => $fixtureInfo->playerCount
            ];

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}