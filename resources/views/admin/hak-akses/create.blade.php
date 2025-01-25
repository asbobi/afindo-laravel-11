@extends('layouts.app')

@section('title')
    {{ $title }}
@endsection


@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-0">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-colored-controls">Data Akses</h4>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body">
                            <form class="form" id="form-input" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="KodeLevel" value="{{ my_encrypt(@$data->KodeLevel) }}" />
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="label-control">Nama Level</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Masukkan Nama Level" name="NamaLevel"
                                                    value="{{ @$data->NamaLevel }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md 12">
                                            <div class="form-group"></div>
                                            <label class="label-control">Hak Akses</label>
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Nama Fitur</th>
                                                        <th class="text-center">View</th>
                                                        <th class="text-center">Add</th>
                                                        <th class="text-center">Edit</th>
                                                        <th class="text-center">Delete</th>
                                                        <th class="text-center">Print</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php
                                                        $no = 1;
                                                    @endphp
                                                    @foreach ($fitur as $item)
                                                        <tr>
                                                            <td>{{ $no++ }} <input type="hidden" name="KodeFitur[]"
                                                                    value="{{ $item->KodeFitur }}"></td>
                                                            <td>{{ $item->NamaFitur }}</td>
                                                            <td class="text-center">
                                                                <div class="skin skin-square">
                                                                    <input type="checkbox" name="ViewData[]" value="1"
                                                                        {{ $item->ViewData ? 'checked' : '' }}>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="skin skin-square">
                                                                    <input type="checkbox" name="AddData[]" value="1"
                                                                        {{ $item->AddData ? 'checked' : '' }}>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="skin skin-square">
                                                                    <input type="checkbox" name="EditData[]" value="1"
                                                                        {{ $item->EditData ? 'checked' : '' }}>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="skin skin-square">
                                                                    <input type="checkbox" name="DeleteData[]"
                                                                        value="1"
                                                                        {{ $item->DeleteData ? 'checked' : '' }}>
                                                                </div>
                                                            </td>
                                                            <td class="text-center">
                                                                <div class="skin skin-square">
                                                                    <input type="checkbox" name="PrintData[]" value="1"
                                                                        {{ $item->PrintData ? 'checked' : '' }}>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-actions right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-check-square-o"></i> Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#form-input').on('submit', function(e) {
                e.preventDefault();

                // if (!validateField('input[name="NamaLayanan"]', 'Nama Layanan harus diisi')) return;
                // if (!validateField('#single_checkbox', 'Checkbox Is Aktif harus dipilih')) return;

                var formData = new FormData();

                formData.append('KodeLevel', $('input[name="KodeLevel"]').val());
                formData.append('NamaLevel', $('input[name="NamaLevel"]').val());
                $('#form-input').find('table tbody tr').each(function(i, v){
                    formData.append('KodeFitur[]', $(v).find('input[name="KodeFitur[]"]').val());
                    $(v).find('input[type="checkbox"]').each(function(i, v){
                        let val = $(v).is(':checked') ? 1 : 0;
                        formData.append($(v).attr('name'), val);
                    });
                });

                $.ajax({
                    url: "{{ url('admin/hak-akses/store') }}",
                    method: 'POST',
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    cache: false,
                    headers: {
                        'X-CSRF-TOKEN':  '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status === false) {
                            showSwal("warning", "Gagal", response.message);
                        } else {
                            showSwal("success", "Informasi", response.message).then(function() {
                                // window.location.href = "{{ url('admin/hak-akses') }}";
                                window.location.href='{{ route('logout') }}';
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function(field, message) {
                            Swal.fire('Error', message[0], 'error');
                        });
                    }
                });
            });
        });
    </script>
@endsection
