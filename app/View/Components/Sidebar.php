<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public $nav_option_1;
    public $nav_option_2;
    public $nav_option_3;
    public $nav_option_4;
    public $nav_option_5;
    public $nav_option_6;
    public $team_name_1;
    public $team_name_2;
    public $team_name_3;

    public function __construct(
        $nav_option_1 = null, // Change this
        $nav_option_2 = null, // Change this
        $nav_option_3 = null, // Change this
        $nav_option_4 = null, // Change this
        $nav_option_5 = null, // Change this
        $nav_option_6 = null, // Change this
        $team_name_1 = null,  // Change this
        $team_name_2 = null,  // Change this
        $team_name_3 = null   // Change this
    ) {
        $this->nav_option_1 = $nav_option_1;
        $this->nav_option_2 = $nav_option_2;
        $this->nav_option_3 = $nav_option_3;
        $this->nav_option_4 = $nav_option_4;
        $this->nav_option_5 = $nav_option_5;
        $this->nav_option_6 = $nav_option_6;
        $this->team_name_1 = $team_name_1;
        $this->team_name_2 = $team_name_2;
        $this->team_name_3 = $team_name_3;
    }

    public function render()
    {        
        return view('components.sidebar', [
            'nav_option_1' => $this->nav_option_1,
            'nav_option_2' => $this->nav_option_2,
            'nav_option_3' => $this->nav_option_3,
            'nav_option_4' => $this->nav_option_4,
            'nav_option_5' => $this->nav_option_5,
            'nav_option_6' => $this->nav_option_6,
            'team_name_1' => $this->team_name_1,
            'team_name_2' => $this->team_name_2,
            'team_name_3' => $this->team_name_3,
        ]);        // return view('components.sidebar');
    }
}

?>