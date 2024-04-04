<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\Models\Player;


final readonly class CountRatedPlayerUseCase
{
    public function __construct()
    {
        //
    }

    public function execute(string $fixtureId)
    {
        try {
            return Fixture::query()
                ->withCount('ratedPlayers as ratedCount')
                ->findOrFail($fixtureId);

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}