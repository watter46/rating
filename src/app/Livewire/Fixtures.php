<?php declare(strict_types=1);

namespace App\Livewire;

use Exception;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Pagination\Paginator;

use App\Http\Controllers\FixturesResource;
use App\Http\Controllers\TournamentType;
use App\UseCases\Fixture\FetchFixtureListUseCase;


class Fixtures extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $sort = '';

    private FetchFixtureListUseCase $fetchFixtureList;
    private FixturesResource $resource;

    public function boot(FetchFixtureListUseCase $fetchFixtureList, FixturesResource $resource)
    {
        $this->fetchFixtureList = $fetchFixtureList;
        $this->resource = $resource;
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
    
    public function render()
    {
        return view('livewire.fixtures', [
            'tournaments' => TournamentType::toText()
        ]);
    }
    
    /**
     * Fixtureのリストを取得する
     *
     * @return Paginator
     */
    private function fetch()
    {
        $tournament = TournamentType::fromOrFail($this->sort);

        $fixtures = $this->fetchFixtureList->execute($tournament);

        return $this->resource->format($fixtures);
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
