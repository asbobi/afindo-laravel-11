<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    public $ajaxUrl;
    public $title;
    public $columns = [];
    public $addButton = false;
    public $excelButton = false;
    public $pdfButton = false;

    public function __construct(
        $columns,
        $ajaxUrl,
        $title,
        $addButton = false,
        $excelButton = false,
        $pdfButton = false
    ) {
        $this->ajaxUrl = $ajaxUrl;
        $this->title = $title;
        $this->columns = $this->processColumns($columns);
        $this->addButton = (bool)$addButton;
        $this->excelButton = (bool)$excelButton;
        $this->pdfButton = (bool)$pdfButton;
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
        return view('components.data-table');
    }
}
