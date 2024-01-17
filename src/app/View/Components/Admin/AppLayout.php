<?php declare(strict_types=1);

namespace App\View\Components\Admin;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    public function render(): View
    {
        return view('layouts.admin.app');
    }
}