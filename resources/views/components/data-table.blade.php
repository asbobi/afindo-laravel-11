<div>
    <div class="filter-box">
        <div class="table-filter">
            <div class="row justify-content-end">
                @foreach ($config["filters"] as $filter)
                    @if ($filter["type"] == "daterange")
                        <div class="form-group col-4 filter-input">
                            {{-- <label>{{ $filter["label"] }}</label> --}}
                            <div class="input-group">
                                <input type="text" class="form-control daterange">
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
                            {{-- <label>{{ $filter["label"] }}</label> --}}
                            <select class="form-control select2">
                                <option value="">All</option>
                                <option value="">All1</option>
                                <option value="">All2</option>
                                <option value="">All3</option>
                            </select>
                        </div>
                    @endif
                    @if ($filter["type"] == "text")
                        <div class="form-group col-4">
                            {{-- <label>{{ $filter["label"] }}</label> --}}
                            <fieldset class="form-group position-relative mb-0 filter-input">
                                <input type="text" class="form-control form-control-xl input-xl" id="iconLeft1"
                                    placeholder="Explore Modern ...">
                                <div class="form-control-position">
                                    <i class="feather icon-mic font-medium-4"></i>
                                </div>
                            </fieldset>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="table-filter-button">
            <button class="btn btn-primary" type="button" onclick="filterTable()"><i
                    class="fas fa-search"></i></button>
        </div>
    </div>
    <div class="btn-input-wrapper">
        <button class="btn btn-warning">Import</button>
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
            <table class="table table-striped table-bordered zero-configuration yajra-datatable">
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
                            title: '{{ @$title }}',
                            exportOptions: {
                                columns: @json($exportableColumns)
                            },
                            customize: function(doc) {
                                doc.styles.tableHeader = {
                                    color: 'black',
                                    background: 'white',
                                    alignment: 'left',
                                    bold: true,
                                }
                            }
                        },
                    @endif
                ],
                language: {
                    url: "{{ asset("assets/js/language_id.json") }}",
                },
            });

            $('#exportExcelBtn').click(function() {
                table.button('.buttons-excel').trigger();
            });

            $('#printPdfBtn').click(function() {
                table.button('.buttons-pdf').trigger();
            });

            table.buttons().container().hide();
        });

        $('.daterange').daterangepicker();

        function filterTable() {
            table.ajax.reload();
        }
    </script>
@endpush
