<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $navOptions;
    public $teams;

    public function __construct($navOptions = [], $teams = [])
    {
        $this->navOptions = $navOptions; // Expect array like [['label' => 'Home', 'icon' => 'fa-house'], ...]
        $this->teams = $teams;           // Expect array like ['HR Team', 'Tech Team', ...]
    }

    public function render()
    {
        return view('components.sidebar');
    }
}
