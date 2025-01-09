<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableInput extends Component
{
    public $label;
    public $class;
    public $cover;
    public $columns;
    public $config;
    public $items;


    public function __construct(
        $label = null,
        $class = null,
        $cover = null,
        $columns = null ,
        $config = null,
        $items = null
    )
    {
        $this->config = $config;

        $label = $label ?? $this->config['label'] ?? '';
        $class = $class ?? $this->config['class'] ?? '';
        $cover = $cover ?? $this->config['cover'] ?? ['<div class="col-md-12">', '</div>'];
        $columns = $columns ?? $this->config['columns'];
        $items = $items ?? $this->config['items'] ?? [];

        if($label){
            $this->label = '<label>'.$label.'</label>';
        }
        if($class){
            $this->class = $class;
        }
        if($cover){
            $this->cover = $cover ?? 'col-md-12';
        }
        if($columns){
            $this->columns = $columns;
        }
        if($items){
            $this->items = $items;
        }

        $this->fillColumnKey();

    }

    function fillColumnKey(){
        //lengkapi atribut
        $this->columns = array_map(function ($column) {
            $column = [
                'header' => $column['header'],
                'data' => $column['data'],
                'class' => $column['class'] ?? '',
                'editable' => $column['editable'] ?? true,
                'type' => $column['type'] ?? 'text',
                'append' => $column['append'] ?? '',
                'options' => $column['options'] ?? []
            ];
            return $column;
        }, $this->columns);
    }

    public function render(): View|Closure|string
    {
        return view('components.table-input');
    }
}
