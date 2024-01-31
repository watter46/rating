<?php declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;

class EvaluationResult extends Component
{
    public array $lineups;
    public array $player;
    public string $fixtureId;

    public bool $isOpen;

    public function render()
    {
        return view('livewire.evaluation-result');
    }
}