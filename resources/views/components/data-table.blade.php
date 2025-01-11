<style>
    .paging-false {
        margin-top: 40px !important;
    }
</style>
<div>
    <div class="filter-box">
        <div class="table-filter">
            <div class="row justify-content-end">
                @foreach ($config["filters"] as $filter)
                    @if ($filter["type"] == "daterange")
                        <div class="form-group col-4 filter-input">
                            {!! isset($filter["label"]) && $filter["label"] != "" ? "<label>" . $filter["label"] . "</label>" : "" !!}
                            <div class="input-group">
                                <input type="text" class="form-control showdropdowns" id="{{ $filter["id"] }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <span class="fa fa-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($filter["type"] == "select")
                        <div class="form-group col-4 filter-input">
                            {!! isset($filter["label"]) && $filter["label"] != "" ? "<label>" . $filter["label"] . "</label>" : "" !!}
                            <select class="form-control select2" id="{{ $filter["id"] }}">
                                @if (isset($filter["options"]))
                                    @foreach ($filter["options"] as $option)
                                        <option value="{{ $option["value"] }}">{{ $option["label"] }}</option>
                                    @endforeach
                                @else
                                    <option value="">-</option>
                                @endif
                            </select>
                        </div>
                    @endif
                    @if ($filter["type"] == "text")
                        <div class="form-group col-4">
                            {!! isset($filter["label"]) && $filter["label"] != "" ? "<label>" . $filter["label"] . "</label>" : "" !!}
                            <fieldset class="form-group position-relative mb-0 filter-input">
                                <input type="text" class="form-control form-control-xl input-xl"
                                    id="{{ $filter["id"] }}" placeholder="Pencarian ...">
                                <div class="form-control-position">
                                    <i class="feather icon-search font-medium-4"></i>
                                </div>
                            </fieldset>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="table-filter-button">
            <button id="btn-cari" class="btn btn-primary" style="height: 40px;" type="button"><i
                    class="feather icon-search font-medium-4"></i></button>
        </div>
    </div>
    <div class="btn-input-wrapper">
        {!! isset($importButton) && $importButton != ""
            ? '<a class="btn btn-warning" href="' . $importButton . '">Import</a>'
            : "" !!}
        {!! isset($excelButton)
            ? (!is_string($excelButton) && $excelButton != ""
                ? '<button id="exportExcelBtn" class="btn btn-primary">Export Excel</button>'
                : '<a class="btn btn-primary" href="' . $excelButton . '">Export Excel</a>')
            : "" !!}
        {!! isset($pdfButton)
            ? (!is_string($pdfButton) && $pdfButton != ""
                ? '<button id="printPdfBtn" class="btn btn-primary">Print Pdf</button>'
                : '<a class="btn btn-primary" href="' . $pdfButton . '">Print Pdf</a>')
            : "" !!}
        {!! isset($addButton) && $addButton != ""
            ? '<a class="btn btn-primary" href="' . $addButton . '">Tambah</a>'
            : "" !!}
    </div>
    <div class="row row-table">
        <div class="col-12">
            <table class="table table-striped table-bordered zero-configuration yajra-datatable display" width="100%">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column["name"] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

@push("scripts")
    <script>
        $(document).ready(function() {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                dom: 'lfr<"table-wrapper" t>ipB',
                searching: false,
                paging: {{ isset($paginate) && $paginate != true ? "false" : "true" }},
                ajax: {
                    url: "{{ $ajaxUrl }}",
                    data: function(d) {
                        @if (isset($paginate) && $paginate != true)

                            d.length = -1;
                        @endif
                        $('.filter-input').each(function() {
                            var inputId = $(this).find('input, select').attr('id');
                            d[inputId] = $('#' + inputId)
                                .val();
                        });
                    }
                },
                columns: {!! json_encode($columns) !!},
                columnDefs: [
                    @foreach ($columns as $index => $column)
                        @if (isset($column["width"]))
                            {
                                width: "{{ $column["width"] }}",
                                targets: {{ $index }}
                            },
                        @endif
                    @endforeach
                ],
                lengthChange: true,
                buttons: [
                    @if (is_bool($excelButton))

                        {
                            extend: 'excelHtml5',
                            title: '{{ @$title }}',
                            exportOptions: {
                                columns: @json($exportableColumns),
                                footer: true
                            },
                            customize: function(xlsx) {
                                @php
                                    $excelColumns = array_filter($columns, function ($column) {
                                        return isset($column["cetak"]) && $column["cetak"] === true;
                                    });
                                @endphp
                                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                var totalColumns = {!! json_encode($excelColumns) !!};
                                var addRow = {{ $addRow ? "true" : "false" }};

                                if (addRow) {
                                    var totalRowExcel = '<row>';

                                    var lastColspanEnd = 0;
                                    var totalRowHtml = '';
                                    var colspanCount = 0;

                                    totalColumns.forEach(function(column, index) {
                                        if (column.total) {
                                            var totalValue = 0;
                                            var api = $('.yajra-datatable').DataTable();
                                            api.column(index, {
                                                page: 'current'
                                            }).data().each(function(value) {
                                                if (typeof value === 'string') {
                                                    value = value.replace(
                                                        /[^0-9,-]+/g, '');
                                                }
                                                totalValue += parseFloat(value) ||
                                                    0;
                                            });

                                            if (column.format && column.format
                                                .toLowerCase() === 'rp') {
                                                totalValue = 'Rp ' + totalValue
                                                    .toLocaleString('id-ID');
                                            }

                                            var colspan = 1;
                                            var label = column.label || 'Total ' + (index +
                                                1);
                                            totalRowHtml += '<c t="inlineStr" colspan="' +
                                                colspan + '"><is><t>' + label + ': ' +
                                                totalValue + '</t></is></c>';
                                            colspanCount +=
                                                colspan;
                                        } else {
                                            totalRowHtml +=
                                                '<c t="inlineStr"><is><t></t></is></c>';
                                            colspanCount += 1;
                                        }
                                    });

                                    totalRowExcel += totalRowHtml;
                                    totalRowExcel += '</row>';

                                    $(sheet).find('sheetData').append(totalRowExcel);

                                    var totalRow = $(sheet).find('sheetData').children().last();
                                    var cols = $(totalRow).find('c');
                                    for (var i = 0; i < colspanCount; i++) {
                                        $(cols[i]).attr('s', '2');
                                    }
                                }
                            }
                        },
                    @endif
                    @if (is_bool($pdfButton))

                        {
                            extend: 'pdfHtml5',
                            orientation: 'landscape',
                            title: '{{ @$title }}',
                            exportOptions: {
                                columns: @json($exportableColumns)
                            },
                            customize: function(doc) {
                                doc.styles.tableHeader = {
                                    color: 'black',
                                    background: 'white',
                                    alignment: 'center',
                                    bold: true,
                                };

                                var colCount = doc.content[1].table.body[0].length;
                                var columnWidth = 100 / colCount;
                                doc.content[1].table.widths = Array(colCount).fill(columnWidth +
                                    '%');

                                doc.styles.tableBodyEven.fontSize = 10;
                                doc.styles.tableBodyOdd.fontSize = 10;

                                var dtColumns = $('.yajra-datatable').DataTable().settings().init()
                                    .columns;

                                for (var rowIndex = 1; rowIndex < doc.content[1].table.body
                                    .length; rowIndex++) {
                                    var row = doc.content[1].table.body[rowIndex];

                                    for (var colIndex = 0; colIndex < row.length; colIndex++) {
                                        var columnClass = dtColumns[colIndex].class || '';
                                        if (columnClass.includes('text-right')) {
                                            row[colIndex].alignment = 'right';
                                        } else if (columnClass.includes('text-left')) {
                                            row[colIndex].alignment = 'left';
                                        } else {
                                            row[colIndex].alignment = 'center';
                                        }
                                    }
                                }

                                @php
                                    $pdfColumns = array_filter($columns, function ($column) {
                                        return isset($column["cetak"]) && $column["cetak"] === true;
                                    });
                                @endphp
                                var api = $('.yajra-datatable').DataTable();
                                var totalColumns = {!! json_encode($pdfColumns) !!};
                                var addRow = {{ $addRow ? "true" : "false" }};
                                var totalValues = {};
                                var totalIndexes = [];

                                totalColumns.forEach(function(column, index) {
                                    if (column.total) {
                                        totalValues[column.data] = 0;
                                        totalIndexes.push(index);
                                    }
                                });

                                api.rows({
                                    page: 'current'
                                }).every(function(rowIdx, tableLoop, rowLoop) {
                                    var data = this.data();
                                    totalColumns.forEach(function(column) {
                                        if (column.total) {
                                            var value = data[column.data];
                                            if (typeof value === 'string') {
                                                value = value.replace(/[^0-9,-]+/g,
                                                    '');
                                            }
                                            totalValues[column.data] += parseFloat(
                                                value) || 0;
                                        }
                                    });
                                });

                                if (addRow) {
                                    var totalRow = [];
                                    var lastColspanEnd = 0;

                                    totalIndexes.forEach(function(totalIndex, totalCount) {
                                        var colspan = totalIndex - lastColspanEnd;
                                        if (colspan > 0) {
                                            var label = totalColumns[totalIndex].label ||
                                                'Total Kolom ' + (totalCount + 1);
                                            totalRow.push({
                                                text: label + ':',
                                                colSpan: colspan,
                                                alignment: 'right'
                                            });

                                            for (var i = 1; i < colspan; i++) {
                                                totalRow.push({});
                                            }
                                        }

                                        var column = totalColumns[totalIndex];
                                        var totalFormatted;
                                        if (column.format && column.format.toLowerCase() ===
                                            'rp') {
                                            totalFormatted = 'Rp ' + totalValues[column
                                                .data].toLocaleString('id-ID');
                                        } else {
                                            totalFormatted = totalValues[column.data];
                                        }

                                        totalRow.push({
                                            text: totalFormatted,
                                            alignment: 'right',
                                            colSpan: 1
                                        });

                                        lastColspanEnd = totalIndex + 1;
                                    });

                                    var remainingColspan = totalColumns.length - lastColspanEnd;
                                    if (remainingColspan > 0) {
                                        totalRow.push({
                                            text: '',
                                            colSpan: remainingColspan
                                        });

                                        for (var i = 1; i < remainingColspan; i++) {
                                            totalRow.push({});
                                        }
                                    }

                                    doc.content[1].table.body.push(totalRow);
                                }
                            }
                        },
                    @endif
                ],
                drawCallback: function(settings) {
                    var api = this.api();
                    $('tr.custom-row').remove();
                    var totalColumns = {!! json_encode($columns) !!};
                    var addRow = {{ $addRow ? "true" : "false" }};
                    if (addRow) {
                        var totalValues = {};
                        var totalIndexes = [];
                        totalColumns.forEach(function(column, index) {
                            if (column.total) {
                                totalValues[column.data] = 0;
                                totalIndexes.push(index);
                            }
                        });

                        api.rows({
                            page: 'current'
                        }).every(function(rowIdx, tableLoop, rowLoop) {
                            var data = this.data();
                            totalColumns.forEach(function(column) {
                                if (column.total) {
                                    var value = data[column.data];

                                    if (typeof value === 'string') {
                                        value = value.replace(/[^0-9,-]+/g,
                                            ''
                                        );
                                    }
                                    totalValues[column.data] += parseFloat(value) || 0;
                                }
                            });
                        });
                        var totalRow = '<tr class="custom-row">';
                        var lastColspanEnd = 0;

                        totalIndexes.forEach(function(totalIndex, totalCount) {
                            var colspan = totalIndex - lastColspanEnd;
                            if (colspan > 0) {
                                var label = totalColumns[totalIndex].label || 'Total Kolom ' + (
                                    totalCount + 1);
                                totalRow += '<td colspan="' + colspan + '">' + label + ':</td>';
                            }

                            var column = totalColumns[totalIndex];

                            var totalFormatted;
                            if (column.format && column.format.toLowerCase() === 'rp') {
                                totalFormatted = 'Rp ' + totalValues[column.data]
                                    .toLocaleString(
                                        'id-ID');
                            } else {
                                totalFormatted = totalValues[column.data];
                            }

                            totalRow += '<td class="' + (column.class || '') + '">' +
                                totalFormatted + '</td>';
                            lastColspanEnd = totalIndex + 1;
                        });
                        var remainingColspan = totalColumns.length - lastColspanEnd;
                        if (remainingColspan > 0) {
                            totalRow += '<td colspan="' + remainingColspan + '"></td>';
                        }

                        totalRow += '</tr>';
                        $('tbody').append(totalRow);
                    }
                },
                language: {
                    url: "{{ asset("assets/js/language_id.json") }}",
                },
                initComplete: function(settings, json) {
                    let paging = {{ isset($paginate) && $paginate != true ? "false" : "true" }};
                    if (!paging && $(window).width() > 640) {
                        $('.dataTables_wrapper').addClass('paging-false');
                    }
                }
            });

            @if ($deleteButton)

                var deleteID = @json($deleteID);
                $('.yajra-datatable').on('draw.dt', function() {
                    $('.yajra-datatable tbody tr').each(function() {
                        var row = $(this);
                        var deleteParams = {};
                        var hasData = false;

                        deleteID.forEach(function(key) {
                            var dataKey = 'data' + key.charAt(0).toUpperCase() + key.slice(
                                1);
                            var dataValue = row.data(dataKey);
                            if (dataValue) {
                                hasData = true;
                            }
                            deleteParams[key] = dataValue;
                        });

                        if (hasData) {
                            var deleteButton =
                                '<a style="padding:5px;" href="javascript:void(0)" onclick="deleteItem(\'' +
                                encodeURIComponent(JSON.stringify(deleteParams)) +
                                '\')" class="text-danger"><i class="feather icon-trash-2"></i></a>';
                            row.find('td:last').append(deleteButton);
                        }
                    });
                });

                window.deleteItem = function(encodedParams) {
                    var deleteParams = JSON.parse(decodeURIComponent(encodedParams));
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Item ini akan dihapus secara permanen.",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.value) {
                            $.ajax({
                                url: '{{ $deleteUrl }}',
                                method: 'POST',
                                dataType: 'json',
                                data: {
                                    ...deleteParams,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(result) {
                                    if (result.status) {
                                        Swal.fire(
                                            'Dihapus!',
                                            'Item berhasil dihapus.',
                                            'success'
                                        );
                                        table.ajax.reload();
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            result.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus item.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                };
            @endif

            $('#exportExcelBtn').click(function() {
                table.button('.buttons-excel').trigger();
            });

            $('#printPdfBtn').click(function() {
                table.button('.buttons-pdf').trigger();
            });

            table.buttons().container().hide();

            $('#btn-cari').click(function() {
                table.ajax.reload();
            });
        });

        $('.showdropdowns').daterangepicker({
            startDate: moment().subtract(1, 'months'),
            endDate: moment(),
            showDropdowns: true,
            locale: {
                format: 'DD-MM-YYYY'
            }
        });
    </script>
@endpush
