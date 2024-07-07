<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\FixtureInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;


final readonly class RegisterFixtureInfo
{
    public function __construct(private ApiFootballRepositoryInterface $apiFootballRepository)
    {
        //
    }

    public function execute(string $fixtureInfoId): FixtureInfo
    {
        try {
            /** @var FixtureInfo $fixtureInfo */
            $fixtureInfo = FixtureInfo::findOrFail($fixtureInfoId);

            $fixtureData = $this->apiFootballRepository->fetchFixture($fixtureInfo->external_fixture_id);
                        
            $updated = $fixtureInfo
                ->builder()
                ->update($fixtureData);
                        
            DB::transaction(function () use ($updated) {
                $updated->save();
            });

            $updated->builder()->dispatch();

            return $updated;
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}