<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Fixture as EqFixture;
use App\UseCases\Fixture\RegisterFixtureUseCase;
use Exception;
use Illuminate\Support\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;


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
                throw new Exception;
            }

            $registerFixture->execute($this->fixture->id);
    
            $this->dispatch('notify', message: self::SUCCESS_MESSAGE);
            $this->dispatch('close-fixture-modal');

        } catch (Exception $e) {
            $this->dispatch('notify', message: self::ERROR_MESSAGE);
        }
    }
}