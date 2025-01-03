@extends("layouts.app")

@section("title")
    {{ $title }}
@endsection

@section("content")
    <section>
        @php
            $fotoLoket = @$data["FotoLoket"];
            $urlFotoLoket = @$data["FotoLoket"] ? uploadPath() . "/FotoLoket/" . @$data["FotoLoket"] : "";
            $urlFileAudio = @$data["FileAudio"] ? uploadPath() . "/FileAudio/" . @$data["FileAudio"] : "";
            $tglLoket = @$data["TglLoket"];
        @endphp
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-colored-controls">Data Loket</h4>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body">
                            <form class="form" id="form-input" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="IDLoket" id="IDLoket"
                                    value="{{ my_encrypt(@$data->IDLoket) }}" />
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="label-control">Nama Loket</label>
                                                <input type="text" class="form-control border-primary"
                                                    placeholder="Masukkan Nama Loket" name="NamaLoket"
                                                    value="{{ @$data["NamaLoket"] }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control">File Gambar</label>
                                                <x-image-upload id="FotoLoket" name="FotoLoket" :value="$fotoLoket"
                                                    :url="$urlFotoLoket" />
                                            </div>
                                            <div class="form-group">
                                                <div class="controls">
                                                    <div class="skin skin-square">
                                                        <input name="IsAktif" type="checkbox" value="1"
                                                            id="single_checkbox"
                                                            {{ @$data["IsAktif"] > 0 ? "checked" : "" }} required>
                                                        <label for="single_checkbox">Is Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="label-control">No. Loket</label>
                                                <input type="number" class="form-control border-primary"
                                                    placeholder="Masukkan No Loket" name="NoLoket"
                                                    value="{{ @$data["NoLoket"] }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control">File Audio</label>
                                                <x-file-input name="FileAudio" label="Upload Audio File"
                                                    :url="$urlFileAudio" />
                                            </div>
                                            <div class="form-group">
                                                <label>Tanggal Loket</label>
                                                <x-datepicker id="TglLoket" name="TglLoket" :value="$tglLoket" />

                                            </div>
                                        </div>
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

@section("scripts")
    <script>
        $(document).ready(function() {
            $('#form-input').on('submit', function(e) {
                e.preventDefault();

                if (!validateField('input[name="NamaLoket"]', 'Nama Loket harus diisi')) return;
                if (!validateField('input[name="NoLoket"]', 'No. Loket harus diisi')) return;
                if (!validateField('#single_checkbox', 'Checkbox Is Aktif harus dipilih')) return;

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ url("admin/loket/store") }}",
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
                                window.location.href = "{{ url("admin/loket") }}";
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
