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
    public $panjang;
    public $lebar;

    /**
     * Buat instance komponen.
     *
     * @param string $id
     * @param string $name
     * @param string $label
     * @param string $value
     * @param string $url
     * @param int $panjang
     * @param int $lebar
     */
    public function __construct($id, $name, $value = "", $url = "", $panjang = 9, $lebar = 16)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->url = $url;
        $this->panjang = $panjang;
        $this->lebar = $lebar;
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
