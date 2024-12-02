<div>
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

@push("scripts")
    <script>
        $(document).ready(function() {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                dom: 'lBfrtip',
                searching: false,
                ajax: {
                    url: "{{ $ajaxUrl }}",
                    data: function(d) {
                        //d.search = $('input[type="search"]').val();
                        $('.filter-input').each(function() {
                            var inputId = $(this).find('input, select').attr('id');
                            d[inputId] = $('#' + inputId)
                                .val();
                        });
                    }
                },
                columns: {!! json_encode($columns) !!},
                /* columnDefs: [{
                    className: "text-center",
                    targets: [0]
                }], */
                lengthChange: true,
                buttons: [
                    @if ($excelButton)

                        {
                            extend: 'excelHtml5',
                            title: '{{ @$title }}',
                            exportOptions: {
                                columns: @json($this->getExportableColumns())
                            }
                        },
                    @endif
                    @if ($pdfButton)

                        {
                            extend: 'pdfHtml5',
                            title: '{{ @$title }}',
                            exportOptions: {
                                columns: @json($this->getExportableColumns())
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
                    @if ($addButton)

                        {
                            text: 'Tambah',
                            action: function(e, dt, node, config) {
                                $('#form-data')[0].reset();
                                $('#ModalTambah').modal('show');
                                $('#defaultModalLabel').html('Tambah Data');
                                $('#text_kode').val('');
                            }
                        }
                    @endif

                ],
                language: {
                    url: "{{ asset("assets/js/language_id.json") }}",
                },
            });
        });
    </script>
@endpush
