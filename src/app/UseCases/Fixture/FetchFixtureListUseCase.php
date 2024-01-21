<?php declare(strict_types=1);

namespace App\UseCases\Fixture;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Http\Controllers\Util\FixtureFile;
use App\Http\Controllers\Util\FixturesFile;
use App\Http\Controllers\Util\PlayerOfTeamFile;
use App\Http\Controllers\Util\SquadsFile;
use App\Models\Fixture;
use App\UseCases\Player\RegisterPlayerOfTeamUseCase;
use App\UseCases\Util\Season;
use Database\Mocks\Fixture\MockRegisterFixtureUseCase;
use Database\Mocks\Player\MockRegisterPlayerOfTeamUseCase;

final readonly class FetchFixtureListUseCase
{    
    public function __construct(
        private Fixture $fixture,
        private Season $season,
        private FixtureFile $file,
        private FixturesFile $fixtures,
        private PlayerOfTeamFile $playerOfTeamFile,
        private SquadsFile $squadsFile,
        private RegisterPlayerOfTeamUseCase $registerPlayerOfTeam,
        private MockRegisterFixtureUseCase $mock)
    {
        //
    }

    public function execute(): LengthAwarePaginator
    {
        try {            
            $this->mock->execute(1035045);
            // $this->registerPlayerOfTeam->execute();
            
            // $fixture = $this->squadsFile->get();
            // dd($fixture);

            /** @var LengthAwarePaginator $fixture */
            $fixture = $this->fixture
                ->past()
                ->inSeason()
                ->paginate(20);

            $fixture->getCollection()
                ->transform(function (Fixture $model) {
                    $model->dataExists = !is_null($model->fixture);

                    unset($model->fixture);

                    return $model;
                });

            return $fixture;

        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('プロジェクトが見つかりませんでした。');

        } catch (Exception $e) {
            throw $e;
        }
    }
}