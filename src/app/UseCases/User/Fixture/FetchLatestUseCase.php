<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;

use App\Models\Fixture;
use App\UseCases\User\PlayerInFixture;
use App\UseCases\User\Player\DecideManOfTheMatchUseCase;


final readonly class FetchLatestUseCase
{
    public function __construct(private DecideManOfTheMatchUseCase $decideManOfTheMatch)
    {
        //
    }

    public function execute(): Fixture
    {
        try {
            /** @var Fixture $latestFixture */  
            $latestFixture = Fixture::query()
                ->past()
                ->latest()
                ->first();
                
            if (!$latestFixture) {
                throw new Exception('Fixture Not Fount');
            }
            
            $fixture = PlayerInFixture::playedPlayersInFixture($latestFixture)->fetch();
            
            return $fixture;

        } catch (Exception $e) {
            throw $e;
        }
    }
}