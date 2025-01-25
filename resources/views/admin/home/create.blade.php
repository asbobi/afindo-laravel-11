@extends("layouts.app")

@section("title")
    {{ $title }}
@endsection

@section("content")
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title" id="horz-layout-colored-controls">Data Loket</h4>
                    </div>
                    <div class="card-content collpase show">
                        <div class="card-body">
                            <form class="form" id="form-input" method="POST">
                                @csrf
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="label-control">Nama Loket</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Masukkan Nama Loket" name="NamaLoket"
                                                    value="{{ @$data["NamaLoket"] }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control">File Gambar</label>
                                                <x-image-upload id="FileGambar" name="FileGambar" value="" />
                                            </div>
                                            <div class="form-group">
                                                <div class="controls">
                                                    <div class="skin skin-square">
                                                        <input type="checkbox" value="" id="single_checkbox" required>
                                                        <label for="single_checkbox">Is Aktif</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="label-control">No. Loket</label>
                                                <input type="number" class="form-control"
                                                    placeholder="Masukkan No Loket" name="NoLoket"
                                                    value="{{ @$data["NoLoket"] }}" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="label-control">File Audio</label>
                                                <x-file-input name="FileAudio" label="Upload Audio File" url="" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions right">
                                    <button type="button" class="btn btn-warning mr-1">
                                        <i class="feather icon-x"></i> Cancel
                                    </button>
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
@endsection
