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
            $fixtureInfo = FixtureInfo::query()
                ->withCount('playerInfos as lineupCount')
                ->findOrFail($fixtureInfoId);
            
            $fixtureInfoData = $this->apiFootballRepository->fetchFixture($fixtureInfo->external_fixture_id);
            
            $fixtureInfo->updateLineups($fixtureInfoData);
            
            DB::transaction(function () use ($fixtureInfo) {
                $fixtureInfo->save();
            });

            $fixtureInfo->fixtureRegistered($fixtureInfoData);

            return $fixtureInfo;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('FixtureInfo Not Exists.');
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}