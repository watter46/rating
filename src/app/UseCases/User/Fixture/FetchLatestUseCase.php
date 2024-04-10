<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;

use App\Models\Fixture;
use App\UseCases\User\PlayerInFixture;


final readonly class FetchLatestUseCase
{
    public function __construct(private PlayerInFixture $playerInFixture)
    {
        //
    }

    public function execute(): Fixture
    {
        try {
            return $this->playerInFixture
                ->latest()
                ->addPlayerInfosColumn()
                ->getFixture();

        } catch (Exception $e) {
            throw $e;
        }
    }
}