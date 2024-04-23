<?php declare(strict_types=1);

namespace App\View\Components\Fixture;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;


class Score extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Collection $fixture,
        public Collection $teams,
        public Collection $league,
        public Collection $score,
        public bool $isRate
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.fixture.score');
    }
}
