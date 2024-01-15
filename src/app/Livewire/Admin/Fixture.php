<?php declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Fixture as EqFixture;
use App\UseCases\Fixture\RegisterFixtureUseCase;
use Illuminate\Support\Collection;
use Livewire\Component;

class Fixture extends Component
{
    public string $fixtureId;
    public Collection $score;
    public bool $dataExists;

    private const SUCCESS_MESSAGE = 'Refreshed!!';

    public function render()
    {
        return view('livewire.admin.fixture');
    }

    public function refresh(RegisterFixtureUseCase $registerFixture): void
    {
        $registerFixture->execute($this->fixtureId);

        $this->dispatch('notify', message: self::SUCCESS_MESSAGE);
    }
}