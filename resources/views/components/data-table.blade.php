<style>
    .paging-false {
        margin-top: 40px !important;
    }

    table.dataTable {
        width: 100%;
        margin: 0 auto;
        clear: both;
        border-collapse: collapse;
        border-spacing: 0;
    }
</style>
<div>
    @if ($config["filters"])
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
                <button id="btn-cari" class="btn btn-primary" style="height: 40px;" type="button">
                    <i class="feather icon-search font-medium-4"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="btn-input-wrapper">
        @foreach (array_reverse($buttons) as $button)
            @if ($button["type"] == "import" && $button["url"] != "")
                <a class="btn btn-warning"
                    href="{{ $button["url"] }}">{{ empty($button["label"]) ? "Import Excel" : $button["label"] }}</a>
            @endif

            @if ($button["type"] == "excel")
                @if ($button["url"] != "")
                    <a class="btn btn-primary" id="exportExcelLink"
                        href="{{ $button["url"] }}">{{ empty($button["label"]) ? "Export Excel" : $button["label"] }}</a>
                @else
                    <button id="exportExcelBtn"
                        class="btn btn-primary">{{ empty($button["label"]) ? "Export Excel" : $button["label"] }}</button>
                @endif
            @endif

            @if ($button["type"] == "pdf")
                @if ($button["url"] != "")
                    <a class="btn btn-primary" id="printPdfLink"
                        href="{{ $button["url"] }}">{{ empty($button["label"]) ? "Print Pdf" : $button["label"] }}</a>
                @else
                    <button id="printPdfBtn"
                        class="btn btn-primary">{{ empty($button["label"]) ? "Print Pdf" : $button["label"] }}</button>
                @endif
            @endif

            @if ($button["type"] == "action")
                @if (isset($button["options"]))
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-min-width dropdown-toggle"
                            data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">{{ empty($button["label"]) ? "Aksi" : $button["label"] }}</button>
                        <div class="dropdown-menu" x-placement="bottom-start"
                            style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 41px, 0px);">
                            @foreach ($button["options"] as $option)
                                @if (!empty($button["method"]))
                                    @if ($option["method"] == "get")
                                        <a class="dropdown-item" href="javascript:void(0)"
                                            onclick="handleGetAction('{{ $option["url"] }}')">
                                            {{ $option["label"] }}
                                        </a>
                                    @elseif ($option["method"] == "post")
                                        <a class="dropdown-item" href="javascript:void(0)"
                                            onclick="handlePostAction('{{ $option["url"] }}')">
                                            {{ $option["label"] }}
                                        </a>
                                    @endif
                                @else
                                    <a class="dropdown-item" href="{{ $option["url"] }}">
                                        {{ $option["label"] }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @else
                    @if (!empty($button["method"]))
                        @if ($option["method"] == "get")
                            <a class="btn btn-primary" href="javascript:void(0)"
                                onclick="handleGetAction('{{ $option["url"] }}')">
                                {{ $option["label"] }}
                            </a>
                        @elseif ($option["method"] == "post")
                            <a class="btn btn-primary" href="javascript:void(0)"
                                onclick="handlePostAction('{{ $option["url"] }}')">
                                {{ $option["label"] }}
                            </a>
                        @endif
                    @else
                        <a href="{{ $button["url"] }}"
                            class="btn btn-primary">{{ empty($button["label"]) ? "Aksi" : $button["label"] }}</a>
                    @endif
                @endif
            @endif

            @if ($button["type"] == "add" && $button["url"] != "")
                <a class="btn btn-primary"
                    href="{{ $button["url"] }}">{{ empty($button["label"]) ? "Tambah" : $button["label"] }}</a>
            @endif
        @endforeach
    </div>
    {{-- @if ($title)
        <h4 class="mb-1">{{ $title }}</h4>
    @endif --}}
    <div class="row row-table">
        <div class="col-12">
            <table class="table table-striped table-bordered zero-configuration yajra-datatable display" width="100%">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            @if ($column["name"] == "checkbox")
                                <th><input type="checkbox" id="checkAll" /></th>
                            @else
                                <th>{{ $column["name"] }}</th>
                            @endif
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
        var selectedIds = [];

        $(document).ready(function() {

            //buat gaya gayaan saja
            let cols = document.querySelectorAll('.filter-box .col-4');
            let perRow = 3;
            let totalCols = cols.length;
            let lastRowStartIndex = totalCols - (totalCols % perRow || perRow);
            let lastRowCols = totalCols % perRow === 0 ? perRow : totalCols % perRow;

            cols.forEach(function(col, index) {
                var rowIndex = index % perRow;
                if (rowIndex === 0) {
                    col.style.paddingLeft = '15px';
                } else {
                    col.style.paddingLeft = '0px';
                }

                if (index >= lastRowStartIndex && lastRowCols < 3) {
                    col.style.paddingLeft = '0px';
                }

                if (index >= lastRowStartIndex && lastRowCols === 3) {
                    if (rowIndex === 0) {
                        col.style.paddingLeft = '15px';
                    } else {
                        col.style.paddingLeft = '0px';
                    }
                }
            });

            var table = $('.yajra-datatable').DataTable({
                order: [],
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
                // columns: {!! json_encode($columns) !!},
                columns: [
                    @foreach ($columns as $index => $column)
                        @if (isset($column["name"]) && $column["name"] === "checkbox")
                            {
                                data: null,
                                orderable: false,
                                searchable: false,
                                class: "text-center",
                                render: function(data, type, row) {
                                    return '<input type="checkbox" class="chebok" />';
                                }
                            },
                        @else
                            {!! json_encode($column) !!},
                        @endif
                    @endforeach
                ],
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
                    @php
                        $hasExcelButton = collect($buttons)->contains(function ($button) {
                            return $button["type"] === "excel" && (!isset($button["url"]) || $button["url"] === "");
                        });
                    @endphp
                    @if ($hasExcelButton)

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
                    @php
                        $hasPdfButton = collect($buttons)->contains(function ($button) {
                            return $button["type"] === "pdf" && (!isset($button["url"]) || $button["url"] === "");
                        });
                    @endphp
                    @if (is_bool($hasPdfButton))

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
                /* language: {
                    url: "{{ asset("assets/js/language_id.json") }}",
                }, */
                initComplete: function(settings, json) {
                    let paging = {{ isset($paginate) && $paginate != true ? "false" : "true" }};
                    if (!paging && $(window).width() > 640) {
                        $('.dataTables_wrapper').addClass('paging-false');
                    }
                }
            });
            @php
                $hasDeleteButton = collect($buttons)->contains(function ($button) {
                    return $button["type"] === "delete" && (!isset($button["url"]) || $button["url"] !== "");
                });
            @endphp
            @if ($hasDeleteButton)

                @php
                    $deleteButton = collect($buttons)->firstWhere("type", "delete");
                    $deleteID = isset($deleteButton) && isset($deleteButton["param"]) ? $deleteButton["param"] : [];
                    $deleteUrl = isset($deleteButton) && isset($deleteButton["url"]) ? $deleteButton["url"] : null;
                @endphp
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

            $('#checkAll').on('click', function() {
                var checked = $(this).prop('checked');
                $('.chebok').each(function() {
                    $(this).prop('checked', checked);

                    //var id = $(this).data('id');
                    var id = $(this).closest('tr').data('id');
                    if (checked) {
                        if (!selectedIds.includes(id)) {
                            selectedIds.push(id);
                        }
                    } else {
                        selectedIds = selectedIds.filter(item => item !== id);
                    }

                    console.log('selectedIds : ', selectedIds);
                });
            });

            $('.yajra-datatable').on('change', '.chebok', function() {
                var id = $(this).closest('tr').data('id');
                //var id = $(this).data('id');
                if ($(this).prop('checked')) {
                    if (!selectedIds.includes(id)) {
                        selectedIds.push(id);
                    }
                } else {
                    selectedIds = selectedIds.filter(item => item !== id);
                }
                console.log('selectedIds : ', selectedIds);
                var allChecked = ($('.chebok:checked').length === $('.chebok').length);
                $('#checkAll').prop('checked', allChecked);
            });

            $('.yajra-datatable').on('draw.dt', function() {
                $('.chebok').each(function() {
                    var rowId = $(this).closest('tr').data('id');

                    if (selectedIds.includes(rowId)) {
                        $(this).prop('checked', true);
                    }
                });

                var allChecked = $('.chebok:checked').length === $('.chebok').length;
                $('#checkAll').prop('checked', allChecked);
            });

            $('#exportExcelLink').on('click', function(e) {
                e.preventDefault();
                var baseUrl = $(this).attr('href');
                var params = {};
                $('.filter-input').each(function() {
                    var input = $(this).find('input, select');
                    var inputId = input.attr('id');
                    var inputValue = input.val();

                    if (inputValue) {
                        params[inputId] = inputValue;
                    }
                });

                var queryString = $.param(params);
                var fullUrl = baseUrl + (baseUrl.indexOf('?') === -1 ? '?' : '&') + queryString;
                window.open(fullUrl, '_blank');
            });

            $('#printPdfLink').on('click', function(e) {
                e.preventDefault();
                var baseUrl = $(this).attr('href');
                var params = {};
                $('.filter-input').each(function() {
                    var input = $(this).find('input, select');
                    var inputId = input.attr('id');
                    var inputValue = input.val();

                    if (inputValue) {
                        params[inputId] = inputValue;
                    }
                });

                var queryString = $.param(params);
                var fullUrl = baseUrl + (baseUrl.indexOf('?') === -1 ? '?' : '&') + queryString;
                window.open(fullUrl, '_blank');
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

        function handleGetAction(baseUrl) {
            if (selectedIds.length > 0) {
                var queryString = selectedIds.map(function(id) {
                    return 'ids[]=' + id;
                }).join('&');
                window.location.href = baseUrl + '?' + queryString;
            } else {
                Swal.fire(
                    'Error!',
                    'Silakan pilih setidaknya satu item.',
                    'error'
                );
            }
        }

        function handlePostAction(url) {
            if (selectedIds.length > 0) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ids: selectedIds,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === false) {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                            return false;
                        }

                        Swal.fire(
                            'Berhasil!',
                            response.message,
                            'success'
                        );

                        $('.yajra-datatable').DataTable().ajax.reload();
                        resetSelections();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'Terjadi kesalahan: ' + error,
                            'error'
                        );
                    }
                });
            } else {
                Swal.fire(
                    'Error!',
                    'Silakan pilih setidaknya satu item.',
                    'error'
                );
            }
        }

        function resetSelections() {
            selectedIds = [];
            $('#checkAll').prop('checked', false);
            $('.chebok').prop('checked', false);
        }
    </script>
@endpush
