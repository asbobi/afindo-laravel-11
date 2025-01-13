<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    public $config;
    public $ajaxUrl;
    public $title;
    public $columns = [];
    public $paginate = true;
    public $addRow = false;
    public $buttons = [];

    public function __construct(
        $config,
    ) {
        $this->config = $config;
        $this->ajaxUrl = @$config['ajaxUrl'];
        $this->title = @$config['title'];
        $this->columns = $this->processColumns($config['columns']);
        $this->config['filters'] = $this->config['filters'] ?? [];
        $this->paginate = isset($config['paginate']) ? $config['paginate'] : true;
        $this->addRow = isset($config['addRow']) ? $config['addRow'] : false;
        $this->buttons = isset($config['buttons']) ? $config['buttons'] : [];
    }

    private function processColumns($columns)
    {
        foreach ($columns as $i => &$column) {
            if ($i == 0) {
                $column['orderable'] = @$column['orderable'] ?? false;
                $column['searchable'] = @$column['searchable'] ?? true;
                $column['class'] = @$column['class'] ?? 'text-center';
            } else {
                $column['orderable'] = @$column['orderable'] ?? true;
                $column['searchable'] = @$column['searchable'] ?? true;
                $column['class'] = @$column['class'] ?? 'text-left';
            }
        }
        return $columns;
    }

    public function getExportableColumns()
    {
        return array_keys(array_filter($this->columns, fn($column) => isset($column['cetak']) && $column['cetak'] === true));
    }

    public function render()
    {
        return view('components.data-table', [
            'exportableColumns' => $this->getExportableColumns()
        ]);
    }
}
