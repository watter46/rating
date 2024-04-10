<?php declare(strict_types=1);

namespace App\Livewire\User\Fixtures;

use Exception;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Pagination\Paginator;

use App\Models\TournamentType;
use App\Livewire\MessageType;
use App\Http\Controllers\FixturesPresenter;
use App\UseCases\User\Fixture\FetchFixturesUseCase;


class Fixtures extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $sort = '';

    private FetchFixturesUseCase $fetchFixtures;
    private FixturesPresenter $presenter;

    public function boot(FetchFixturesUseCase $fetchFixtures, FixturesPresenter $presenter)
    {
        $this->fetchFixtures = $fetchFixtures;
        $this->presenter = $presenter;
    }
    
    public function render()
    {
        return view('livewire.user.fixtures.fixtures', [
            'tournaments' => TournamentType::toText()
        ]);
    }

    #[Computed]
    public function fixtures(): Paginator
    {
        try {
            return $this->fetch();

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
            
            $this->sort = '';

            return $this->fetch();
        }
    }
    
    /**
     * 選択した試合に移動する
     *
     * @return void
     */
    public function toFixture(string $fixtureId): void
    {
        $this->redirect("/fixtures/$fixtureId");
    }
    
    /**
     * Fixtureのリストを取得する
     *
     * @return Paginator
     */
    private function fetch(): Paginator
    {
        $tournament = TournamentType::fromOrFail($this->sort);

        $fixtures = $this->fetchFixtures->execute($tournament);

        return $this->presenter->format($fixtures);
    }
    
    /**
     * Tournamentが変更されたらページを1にする
     *
     * @return void
     */
    public function updatedSort()
    {
        $this->resetPage();
    }
}
