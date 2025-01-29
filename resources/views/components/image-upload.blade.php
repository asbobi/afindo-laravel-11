<div class="image-upload-wrapper">
    <input type="hidden" name="{{ $name }}Lama" value="{{ @$value ?? "" }}">

    <input type="file" class="dropify" id="{{ $id }}" name="{{ $name }}"
        data-default-file="{{ $url != null && $url != "" ? $url : asset("app-assets/images/noimage.png") }}"
        data-max-file-size="2M" data-allowed-file-extensions="jpeg jpg png gif">

    @if ($value != null && $value != "")
        <div class="button-box mt-2">
            <button type="button" class="btn btn-danger btn-sm" onclick="deleteImage('{{ $id }}')">
                <i class="fa fa-trash"></i> Hapus Gambar
            </button>
        </div>
    @endif

    <div class="modal fade" id="modal_cropper" role="dialog" aria-labelledby="modalLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Crop the image</h5>
                    <button type="button" class="btn close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0" style="max-height: 75vh; overflow: auto;">
                    <img id="imageCropping" src="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-hide" data-dismiss="modal">Close</button>
                    <button type="button" id="cropButton" class="btn btn-primary">Crop & Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push("scripts")
    <script>
        $(document).ready(function() {
            $('.dropify').dropify();

            let cropper = null;
            let imageCropping = $('#imageCropping')[0];
            let croppedImageName = '';
            let croppedImageFile = null;

            $('#{{ $id }}').on('change', function(e) {
                let files = e.target.files;
                if (files && files.length > 0) {
                    let file = files[0];
                    croppedImageName = file.name;
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        imageCropping.src = event.target.result;
                        $('#modal_cropper').modal('show');
                    };
                    reader.readAsDataURL(file);
                }
            });

            $('#modal_cropper').on('shown.bs.modal', function() {
                cropper = new Cropper(imageCropping, {
                    viewMode: 1,
                    aspectRatio: '{{ $lebar }}' / '{{ $panjang }}',
                });
            }).on('hidden.bs.modal', function() {
                cropper.destroy();
                cropper = null;
            });

            $("#cropButton").click(function() {
                if (cropper) {
                    cropper.getCroppedCanvas({
                        width: 800,
                        height: 450,
                    }).toBlob(function(blob) {
                        let croppedImageFile = new File([blob], croppedImageName, {
                            type: 'image/png'
                        });

                        let reader = new FileReader();
                        reader.readAsDataURL(blob);
                        reader.onloadend = function() {
                            let base64data = reader.result;

                            // Reset Dropify preview
                            let dropifyElement = $('#{{ $id }}').data('dropify');
                            dropifyElement.resetPreview();
                            dropifyElement.clearElement();
                            dropifyElement.settings.defaultFile = base64data;

                            dropifyElement.destroy();
                            $('#{{ $id }}').dropify({
                                defaultFile: base64data
                            }).data('dropify').init();

                            let inputFile = document.getElementById('{{ $id }}');
                            let dataTransfer = new DataTransfer();
                            dataTransfer.items.add(croppedImageFile);
                            inputFile.files = dataTransfer.files;

                            resetPreview('{{ $name }}', base64data, 'Image.png');

                            $('#modal_cropper').modal('hide');
                        };
                    }, 'image/png');
                }
            });

            function resetPreview(name, src, fname = '') {
                let input = $('input[name="' + name + '"]');
                let wrapper = input.closest('.dropify-wrapper');
                let preview = wrapper.find('.dropify-preview');
                let filename = wrapper.find('.dropify-filename-inner');
                let render = wrapper.find('.dropify-render').html('');

                //input.val('').attr('title', fname);
                input.attr('title', fname);
                wrapper.removeClass('has-error').addClass('has-preview');
                filename.html(fname);
                render.append($('<img />').attr('src', src).css('max-height', input.data('height') || ''));
                preview.fadeIn();
            }

            function deleteImage(id) {
                $('#' + id).dropify().clearElement();
                $('input[name="{{ $name }}"]').val(
                    '');
            }

            $('.modal-hide').click(function() {
                $('#{{ $id }}').val('');
            });
        });
    </script>
@endpush
