<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

use App\Http\Controllers\FixturesResource;
use App\Http\Controllers\TournamentType;
use App\Livewire\MessageType;
use App\UseCases\Fixture\FetchFixtureListUseCase;
use App\UseCases\Fixture\RegisterFixtureListUseCase;


class Fixtures extends Component
{
    use WithPagination;

    #[Validate('required')]
    public string $refreshKey;

    private FetchFixtureListUseCase $fetchFixtureList;
    private RegisterFixtureListUseCase $registerFixtureList;
    private FixturesResource $resource;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE = 'Incorrect key';

    public function boot(
        FetchFixtureListUseCase $fetchFixtureList,
        RegisterFixtureListUseCase $registerFixtureList,
        FixturesResource $resource)
    {
        $this->fetchFixtureList = $fetchFixtureList;
        $this->registerFixtureList = $registerFixtureList;
        $this->resource = $resource;
    }

    public function render()
    {
        return view('livewire.admin.fixtures');
    }

    #[Computed()]
    public function fixtures(): Paginator
    {
        try {
            return $this->fetch();

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
            
            return $this->fetch();
        }
    }

    /**
     * Fixtureのリストを取得する
     *
     * @return Paginator
     */
    private function fetch()
    {
        $tournament = TournamentType::ALL;

        $fixtures = $this->fetchFixtureList->execute($tournament);

        return $this->resource->format($fixtures);
    }
    
    /**
     * 試合一覧情報を更新する
     *
     * @return void
     */
    public function refreshFixture(): void
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }
            
            // $this->registerFixtureList->execute();
            
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close-fixtures-modal');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
