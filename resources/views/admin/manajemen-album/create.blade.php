@extends('layouts.app')


@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-colored-controls">Data Album</h4>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body">
                            <form class="form" id="form-input" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="IDKonten" id="IDKonten"
                                    value="{{ my_encrypt(@$data->IDKonten) }}" />
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="label-control">Nama Album</label>
                                                <input type="text" class="form-control" placeholder="Masukkan Nama Album"
                                                    name="JudulKonten" value="{{ @$data['JudulKonten'] }}" required>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="label-control">Galeri</label>
                                                <x-image-gallery 
                                                    id="gallery-1"
                                                    name="FileGambar"
                                                    var="uploadedFiles"
                                                    horizontalCount="4"
                                                    itemSpace="10"
                                                    :defaultValue="@$gambar"
                                                    ratio="12/9"
                                                    {{-- panjang="12" bisa manual --}}
                                                    {{-- lebar="5" bisa manual --}}
                                                />
                                            </div>

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
        let uploadedFiles = [];
        
        $(document).ready(function() {

            $('#form-input').on('submit', function(e) {
                e.preventDefault();

                if (!validateField('input[name="JudulKonten"]', 'Nama Album harus diisi')) return;

                var formData = new FormData(this);

                //masukkan semua gambar dari galeri
                uploadedFiles.forEach((file) => {
                    formData.append('FileGambar[]', file);
                });

                $.ajax({
                    url: "{{ url('admin/manajemen-album/store') }}",
                    method: 'POST',
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === false) {
                            showSwal("warning", "Gagal", response.message);
                        } else {
                            showSwal("success", "Informasi", response.message).then(function() {
                                window.location.href =
                                    "{{ url('admin/manajemen-album') }}";
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
