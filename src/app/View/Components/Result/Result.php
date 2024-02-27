<?php declare(strict_types=1);

namespace App\View\Components\Result;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Result extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $fixture,
        public array $teams,
        public array $league,
        public array $score,
        public array $lineups,
        public string $fixtureId)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.result.result');
    }
}
