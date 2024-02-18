<?php declare(strict_types=1);

namespace App\View\Components\Fixture;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Player extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $fixtureId,
        public array $player,
        public array $lineups
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.fixture.player');
    }
}
