<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FileInput extends Component
{
    public $name;
    public $url;
    public $label;

    /**
     * Create a new component instance.
     */
    public function __construct($name, $url = null, $label = 'Choose File')
    {
        $this->name = $name;
        $this->url = $url;
        $this->label = $label;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.file-input');
    }
}
