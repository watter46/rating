<?php declare(strict_types=1);

namespace App\Livewire\Fixtures;

use Exception;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Pagination\Paginator;

use App\Http\Controllers\FixturesResource;
use App\Http\Controllers\TournamentType;
use App\Livewire\MessageType;
use App\UseCases\Fixture\FetchFixturesUseCase;


class Fixtures extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $sort = '';

    private FetchFixturesUseCase $fetchFixtures;
    private FixturesResource $resource;

    public function boot(FetchFixturesUseCase $fetchFixtures, FixturesResource $resource)
    {
        $this->fetchFixtures = $fetchFixtures;
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
        return view('livewire.fixtures.fixtures', [
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

        $fixtures = $this->fetchFixtures->execute($tournament);

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
