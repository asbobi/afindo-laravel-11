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
                ajax: {
                    url: "{{ $ajaxUrl }}",
                    data: function(d) {
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
                                columns: @json($exportableColumns)
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
                                // Atur gaya header tabel
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

                                // Ambil informasi dari DataTables API
                                var dtColumns = $('.yajra-datatable').DataTable().settings()
                                    .init().columns;

                                for (var rowIndex = 1; rowIndex < doc.content[1].table.body
                                    .length; rowIndex++) {
                                    var row = doc.content[1].table.body[rowIndex];

                                    for (var colIndex = 0; colIndex < row.length; colIndex++) {
                                        // Ambil class dari DataTables kolom saat ini
                                        var columnClass = dtColumns[colIndex].class || '';

                                        // Atur alignment berdasarkan class
                                        if (columnClass.includes('text-right')) {
                                            row[colIndex].alignment = 'right';
                                        } else if (columnClass.includes('text-left')) {
                                            row[colIndex].alignment = 'left';
                                        } else {
                                            row[colIndex].alignment =
                                                'center'; // Default ke center jika tidak ada class
                                        }
                                    }
                                }
                            }
                        },
                    @endif
                ],
                language: {
                    url: "{{ asset("assets/js/language_id.json") }}",
                },
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
