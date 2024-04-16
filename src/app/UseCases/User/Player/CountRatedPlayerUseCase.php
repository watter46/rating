<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;


final readonly class CountRatedPlayerUseCase
{
    public function __construct()
    {
        //
    }

    public function execute(string $fixtureId): Fixture
    {
        try {
            return Fixture::query()
                ->select(['fixture'])
                ->withCount('ratedPlayers as ratedCount')
                ->findOrFail($fixtureId);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}