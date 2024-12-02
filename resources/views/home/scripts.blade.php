<script type="text/javascript">
    $(function() {
        var table = $('.yajra-datatable').DataTable({
            processing: true,
            serverSide: true,
            dom: 'lBfrtip',
            searching: false,
            ajax: {
                url: "{{ url("manajemen-loket/listdata") }}",
                data: function(d) {
                    d.search = $('input[type="search"]').val()
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'NamaLoket',
                    name: 'NamaLoket'
                },
                {
                    data: 'NoLoket',
                    name: 'NoLoket'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    class: "text-center",
                    render: function(data, type, row) {
                        let available = data.IsAvailable == 1 ? `&#10004;`: `&#x2716;`;
                        return `${available}`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    class: "text-center",
                    visible: false,
                    render: function(data, type, row) {
                        let available = data.IsAvailable == 1 ? `Ya`: `Tidak`;
                        return `${available}`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        let status = data.IsAktif == 1 ? 
                            `<div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckChecked" checked onclick="set_status('${btoa(data.IDLoket)}', '${btoa(0)}')" style="opacity: 1">
                                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                            </div>` :  
                            `<div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" onclick="set_status('${btoa(data.IDLoket)}', '${btoa(1)}')" id="flexSwitchCheckChecked">
                                <label class="form-check-label" for="flexSwitchCheckChecked"></label>
                            </div>`;
                        return `${status}`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    class: "text-center",
                    render: function(data, type, row) {
                        let edit =
                            `<a href="javascript:void(0);" data-model=\'${JSON.stringify(data)}\' class="text-primary btn-edit" style="margin-right:8px;"><i class="fa fa-edit"></i></a>`
                        let del =
                            `<a href="#" data-id="${btoa(data.IDLoket)}" class="text-danger btn-del"><i class="fa fa-trash"></i></a>`
                        return `${edit + del}`;
                    }
                }
            ],
            columnDefs: [{
                className: "text-center",
                targets: [0, 2]
            }],
            "lengthChange": true,
            "buttons": {
                "dom": {
                    "button": {
                        "tag": "button",
                        "className": "btn-datatabel waves-effect waves-light btn btn-primary"
                    }
                },
                "buttons": [{
                    extend: 'excelHtml5',
                    title: '{{ @$title }}',
                    exportOptions: {
                        columns: [0,1,2,4]
                    }
                }, {
                    extend: 'pdfHtml5',
                    title: '{{ @$title }}',
                    exportOptions: {
                        columns: [0,1,2,4]
                    },
                    customize: function(doc) {
                        doc.styles.tableHeader = {
                            color: 'black',
                            background: 'white',
                            alignment: 'left',
                            bold: true,
                        }

                    }
                }, {
                    text: 'Tambah',
                    action: function(e, dt, node, config) {
                        $('#form-data')[0].reset();
                        $('#ModalTambah').modal('show');
                        $('#defaultModalLabel').html('Tambah Data');
                        $('#text_kode').val('');
                    }
                }]
            },
            language: {
                url: "{{ asset('assets/js/language_id.json') }}",
            },
        });

        $(".yajra-datatable").on("click", ".btn-del", function() {
            const kode = $(this).data('id');
            Swal.fire({
                title: 'Apa anda yakin?',
                text: "data terhapus tidak dapat di kembalikan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus data!'
            }).then((result) => {
                if (result.isConfirmed) {
                    let deleteURL = `{{ url('manajemen-loket/delete/${kode}') }}`;
                    get_response(deleteURL, '', function(response) {
                        if (response.status === false) {
                            showSwal('warning', 'Peringatan', response.msg);
                            return false;
                        } else {
                            table.ajax.reload();
                            showSwal('success', 'Informasi', response.msg);
                        }
                    })
                }
            })
        });

        $(".yajra-datatable").on("click", ".btn-edit", function() {
            const model = $(this).data('model');
            $('#form-data')[0].reset();
            $('#IDLoket').val(model.IDLoket);
            $('#NamaLoket').val(model.NamaLoket);
            $('#NoLoket').val(model.NoLoket);
            model.IsAvailable == 1 ? $("#IsAvailable").prop("checked", true) : $("#IsAvailable").prop("checked", false);
            $('#ModalTambah').modal('show');
            $('#defaultModalLabel').html('Edit Data');
        });

        $('#form-data').submit(function(e) {
            e.preventDefault();
            type = 'post';
            url  = $(this).attr('action');
            var self = $(this)
            let data_post = new FormData(self[0]);

            post_response(url, data_post, function(response) {
                if (response.status) {
                    showSwal("success", "Informasi", response.msg).then(function() {
                        $('#ModalTambah').modal('hide');
                        table.ajax.reload();
                    });
                } else {
                    showSwal("error", "Gagal", response.msg);
                }
            });
        });
    });

    function set_status(kode, status){
        let statusURL = `{{ url('manajemen-loket/status/${kode}/${status}') }}`;
        get_response(statusURL, '', function(response) {
            if (response.status === false) {
                showSwal('warning', 'Peringatan', response.msg);
                return false;
            } else {
                showSwal('success', 'Informasi', response.msg);
            }
        })
    } 
</script>