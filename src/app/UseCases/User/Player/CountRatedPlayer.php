<?php declare(strict_types=1);

namespace App\UseCases\User\Player;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\UseCases\User\FixtureRequest;


final readonly class CountRatedPlayer
{
    public function __construct()
    {
        //
    }

    public function execute(FixtureRequest $request)
    {
        try {
            return $request
                ->buildFixture()
                ->loadAllIdInFixture()
                ->getRatedCount();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Player Not Found');

        } catch (Exception $e) {
            throw $e;
        }
    }
}