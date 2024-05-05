<?php declare(strict_types=1);

namespace App\UseCases\User\Fixture;

use Exception;

use App\Models\Fixture;
use App\UseCases\User\FixtureBuilder;


final readonly class fetchLatestFixture
{
    public function __construct(private FixtureBuilder $builder)
    {
        //
    }

    public function execute(): Fixture
    {
        try {
            return $this->builder
                ->latest()
                ->loadAllInFixture()
                ->addMomLimit()
                ->addPlayers()
                ->get();

        } catch (Exception $e) {
            throw $e;
        }
    }
}