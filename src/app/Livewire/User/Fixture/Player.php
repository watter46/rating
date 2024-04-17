<?php declare(strict_types=1);

namespace App\Livewire\User\Fixture;

use Livewire\Component;


class Player extends Component
{
    public string $fixtureId;
    public array $playerData;

    public string $name;
    public string $size;

    public ?float $defaultRating;

    use PlayerTrait;

    public function mount()
    {
        $this->defaultRating = $this->playerData['defaultRating'];
    }
    
    public function render()
    {
        return view('livewire.user.fixture.player');
    }
}