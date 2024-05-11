<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Illuminate\Pagination\Paginator;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

use App\Livewire\MessageType;
use App\Http\Controllers\FixturesPresenter;
use App\UseCases\Admin\Fixture\FetchFixtureInfos;
use App\UseCases\Admin\Fixture\RegisterFixtureInfos;


class Fixtures extends Component
{
    use WithPagination;

    #[Validate('required')]
    public string $refreshKey;

    private FetchFixtureInfos $fetchFixtureInfos;
    private RegisterFixtureInfos $registerFixtureInfos;
    private FixturesPresenter $presenter;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE = 'Incorrect key';

    public function boot(
        FetchFixtureInfos $fetchFixtureInfos,
        RegisterFixtureInfos $registerFixtureInfos,
        FixturesPresenter $presenter)
    {
        $this->fetchFixtureInfos = $fetchFixtureInfos;
        $this->registerFixtureInfos = $registerFixtureInfos;
        $this->presenter = $presenter;
    }

    public function render()
    {
        return view('livewire.admin.fixtures');
    }

    #[Computed()]
    public function fixtureInfos(): Paginator
    {
        try {
            return $this->fetchFixtureInfos();

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
    private function fetchFixtureInfos(): Paginator
    {
        try {
            $fixtures = $this->fetchFixtureInfos->execute();

            return $this->presenter->format($fixtures);

        } catch (Exception $e) {            

            return $this->fetch();
        }
    }
    
    /**
     * 試合一覧情報を更新する
     *
     * @return void
     */
    public function refreshFixtures(): void
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }
            
            $this->registerFixtureInfos->execute();
            
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close-fixtures-modal');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}
