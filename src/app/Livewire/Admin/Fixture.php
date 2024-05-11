<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

use App\Livewire\MessageType;
use App\Livewire\User\Data\FixturesDataPresenter;
use App\Models\FixtureInfo;
use App\UseCases\Admin\Fixture\RegisterFixtureInfo;


class Fixture extends Component
{
    #[Locked]
    public FixtureInfo $fixtureInfo;

    #[Validate('required')]
    public string $refreshKey;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE = 'Incorrect key';

    public function render()
    {
        return view('livewire.admin.fixture');
    }

    public function refresh(RegisterFixtureInfo $registerFixtureInfo): void
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }

            $registerFixtureInfo->execute($this->fixtureInfo->id);
    
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close-fixture-modal');

        } catch (Exception $e) {
            dd($e);
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}