<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class ImpressaoLayout extends Component
{
    public function render(): View
    {
        return view('layouts.impressao');
    }
}
