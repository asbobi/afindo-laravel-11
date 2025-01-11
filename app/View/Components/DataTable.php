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
    public $importButton = '';
    public $addButton = '';
    public $excelButton = '';
    public $pdfButton = '';
    public $deleteButton = false;
    public $deleteID = [];
    public $deleteUrl = "";

    public function __construct(
        $config,
    ) {
        $this->config = $config;
        $this->ajaxUrl = @$config['ajaxUrl'];
        $this->title = @$config['title'];
        $this->columns = $this->processColumns($config['columns']);
        $this->importButton = isset($config['importButton']) ? $config['importButton'] : false;
        $this->addButton = isset($config['addButton']) ? $config['addButton'] : false;
        $this->excelButton = isset($config['excelButton']) ? $config['excelButton'] : false;
        $this->pdfButton = isset($config['pdfButton']) ? $config['pdfButton'] : false;
        $this->deleteButton = isset($config['deleteButton']['status']) ? $config['deleteButton']['status'] : false;
        $this->deleteID = $this->deleteButton ? $config['deleteButton']['param'] : '';
        $this->deleteUrl = $this->deleteButton ? $config['deleteButton']['url'] : '';
        $this->config['filters'] = $this->config['filters'] ?? [];
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
