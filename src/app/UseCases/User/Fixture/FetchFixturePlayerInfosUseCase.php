<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Fixture;
use App\UseCases\User\PlayerInFixture;
use App\UseCases\User\PlayerInFixtureRequest;


final readonly class FetchFixturePlayerInfosUseCase
{
    public function __construct(private PlayerInFixture $playerInFixture)
    {
        //
    }
    
    public function execute(PlayerInFixtureRequest $request): Fixture
    {
        try {            
            return $this->playerInFixture
                ->request($request)
                ->addPlayerInfosColumn()
                ->getFixture();

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Fixture Not Found.');
                                    
        } catch (Exception $e) {
            throw $e;
        }
    }
}