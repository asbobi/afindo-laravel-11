<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Carbon\Carbon;

class DatePicker extends Component
{
    public $id;
    public $name;
    public $value;
    public $placeholder;

    public function __construct($id, $name, $value = null, $placeholder = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value ?? Carbon::now()->format('Y-m-d');;
        $this->placeholder = $placeholder;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.date-picker');
    }
}
