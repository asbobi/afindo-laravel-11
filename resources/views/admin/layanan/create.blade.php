@extends('layouts.app')

@section('title')
    {{ $title }}
@endsection


@section('content')
    <section>
        @php
            $fotoLayanan = @$data['FotoLayanan'];
            $urlFotoLayanan = @$data['FotoLayanan'] ? uploadPath() . '/FotoLayanan/' . @$data['FotoLayanan'] : '';
            $urlFileAudio = @$data['FileAudio'] ? uploadPath() . '/FileAudio/' . @$data['FileAudio'] : '';
            $tglLayanan = @$data['TglLayanan'];
        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-colored-controls">Data Layanan</h4>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body">
                            <form class="form" id="form-input" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="IDLayanan" id="IDLayanan"
                                    value="{{ my_encrypt(@$data->IDLayanan) }}" />
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="label-control">Nama Layanan</label>
                                                <input type="text" class="form-control border-primary"
                                                    placeholder="Masukkan Nama Layanan" name="NamaLayanan"
                                                    value="{{ @$data['NamaLayanan'] }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="controls">
                                                    <div class="skin skin-square">
                                                        <input name="IsAktif" type="checkbox" value="1"
                                                            id="single_checkbox"
                                                            {{ @$data['IsAktif'] > 0 ? 'checked' : '' }} required>
                                                        <label for="single_checkbox">Is Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <x-table-input :config="$table_input_config" />
                                    </div>
                                </div>

                                <div class="form-actions right">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-check-square-o"></i> Save
                                    </button>
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

                if (!validateField('input[name="NamaLayanan"]', 'Nama Layanan harus diisi')) return;
                if (!validateField('#single_checkbox', 'Checkbox Is Aktif harus dipilih')) return;

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ url('admin/layanan/store') }}",
                    method: 'POST',
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(response) {
                        if (response.status === false) {
                            showSwal("warning", "Gagal", response.message);
                        } else {
                            showSwal("success", "Informasi", response.message).then(function() {
                                window.location.href = "{{ url('admin/layanan') }}";
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