<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use Exception;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

use App\Livewire\MessageType;
use App\Models\Fixture as EqFixture;
use App\UseCases\Admin\Fixture\RegisterFixtureUseCase;


class Fixture extends Component
{
    #[Locked]
    public EqFixture $fixture;

    #[Validate('required')]
    public string $refreshKey;

    private const SUCCESS_MESSAGE = 'Please Reload!!';
    private const ERROR_MESSAGE = 'Incorrect key';

    public function render()
    {
        return view('livewire.admin.fixture');
    }

    public function refresh(RegisterFixtureUseCase $registerFixture): void
    {
        try {
            if ($this->refreshKey !== config('refreshKey.key')) {
                throw new Exception(self::ERROR_MESSAGE);
            }

            $registerFixture->execute($this->fixture->id);
    
            $this->dispatch('notify', message: MessageType::Success->toArray(self::SUCCESS_MESSAGE));
            $this->dispatch('close-fixture-modal');

        } catch (Exception $e) {
            $this->dispatch('notify', message: MessageType::Error->toArray($e->getMessage()));
        }
    }
}