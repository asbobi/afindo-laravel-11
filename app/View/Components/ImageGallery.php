<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageGallery extends Component
{
    public $id = ''; // id component
    public $name = ''; // nama input file
    public $var = ''; // variabel javascript yang akan disi file-file
    public $class = ''; //tambahan class pada item jika perlu
    public $horizontalCount = 4; // jumlah item menyamping
    public $itemSpace = 10; //jarak antar item (menyamping dan ke bawah) (satuan px)
    public $defaultValue = []; //array berisi data gambar
    public $ratio = '4/4'; // rasio
    public $panjang = 4; // opsional jika tidak ada rasio
    public $lebar = 4; // opsional jika tidak ada rasio

    //tidak boleh diisi dari view
    private $col = ''; //jumlah kolom

    public function __construct(
        $id = null,
        $name = null,
        $var = null,
        $class = null,
        $horizontalCount = null,
        $itemSpace = null,
        $defaultValue = [],
        $ratio = null,
        $panjang = null,
        $lebar = null,
    ) {
        $this->id = $id ?? 'gallery-1';
        $this->name = $name ?? 'file';
        $this->var = $var ?? 'files';
        $this->class = $class ?? '';
        $this->horizontalCount = $horizontalCount ?? 4;
        $this->itemSpace = $itemSpace ?? 10;
        $this->defaultValue = $defaultValue ?? [];
        $this->ratio = $ratio ?? '4/4';
        $this->panjang = $panjang ?? explode('/', $ratio)[0] ?? 4;
        $this->lebar = $lebar ?? explode('/', $ratio)[1] ?? 4;

        $this->proccess();
    }

    public function proccess()
    {
        $this->id = str_replace(' ', '', $this->id);
        $this->id = str_replace('-', '', $this->id);

        $this->name = str_replace(' ', '', $this->name);
        $this->name = str_replace('-', '', $this->name);

        $this->col = 12 / $this->horizontalCount;
        $this->col = round($this->col);
    }

    public function render(): View|Closure|string
    {
        return view('components.image-gallery', [
            'col' => $this->col
        ]);
    }
}
