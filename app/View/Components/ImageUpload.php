<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageUpload extends Component
{
    public $id;
    public $name;
    public $value;
    public $url;

    /**
     * Buat instance komponen.
     *
     * @param string $id
     * @param string $name
     * @param string $label
     * @param string $value
     * @param string $url
     */
    public function __construct($id, $name, $value = "", $url = "")
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->url = $url;
    }

    /**
     * Render view komponen.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('components.image-upload');
    }
}
