<?php declare(strict_types=1);

namespace App\View\Components\Util;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ModalButton extends Component
{   
    /**
     * Create a new component instance.
     */
    public function __construct(public ?string $dispatchName = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.util.modal-button');
    }
}