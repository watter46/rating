<?php declare(strict_types=1);

namespace App\UseCases\Admin\Fixture;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\FixtureInfo;
use App\UseCases\Admin\ApiFootballRepositoryInterface;
use Illuminate\Support\Facades\Cache;

final readonly class RegisterFixtureInfo
{
    /** キャッシュする期間(1週間) */
    private const CACHE_DURATION = 604800;

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

            // Cache::set('fixtureInfo_'.$fixtureInfoId, $fixtureInfo, self::CACHE_DURATION);

            $fixtureInfo->fixtureRegistered($fixtureInfoData);

            // dd(Cache::get('fixtureInfo_'.$fixtureInfoId));

            return $fixtureInfo;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('FixtureInfo Not Exists.');
 
        } catch (Exception $e) {
            throw $e;
        }
    }
}