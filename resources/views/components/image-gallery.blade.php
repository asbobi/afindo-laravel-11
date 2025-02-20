@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <style>
        .add-image {
            width: 100%;
            height: 100%;
            background: #eff3fa;
            border: solid 1px #ccd6e5;
            border-radius: 4px;

        }

        .add-image:hover {
            background: #eaeff7;
            border-color: #00b5b8;
            cursor: pointer;
        }

        .add-image .icon i,
        .add-image .icon div {
            transition: .25s;
        }

        .add-image:hover .icon i {
            transform: scale(1.2);
        }

        .add-image:hover .icon div {
            transform: scale(1.1);
        }

        .image-gallery{
            position: relative;
        }
        .image-gallery .image {
            object-fit: cover;
            width: 100%;
            height: 100%;
            border-radius: 4px;
        }

        :root {
            --gallery-space: {{ $itemSpace }}px;
        }

        .gallery {
            border-color: #ccd6e6 !important;
            border-radius: 2px;
        }

        .gallery-row {
            margin-left: calc(var(--gallery-space) / 2 * -1);
            margin-right: calc(var(--gallery-space) / 2 * -1);
            margin-bottom: calc(var(--gallery-space) * -1);
        }

        .gallery-row .gallery-col {
            padding-left: calc(var(--gallery-space) / 2);
            padding-right: calc(var(--gallery-space) / 2);
            margin-bottom: var(--gallery-space);
        }

        .gallery .icon i {
            font-size: 22px;
            margin-bottom: 7px;
        }

        .image-gallery-overlay {
            transition: .25s;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
            background: #383c43d6;
            opacity: 0;
        }

        .image-gallery:hover .image-gallery-overlay {
            opacity: 1;
        }

        .overlay-icon {
            position: absolute;
            top: 0px;
            right: 0px;
            background: #ff6e6e;
            color: #fff;
            padding: 6px 10px;
            border-radius: 2px;
            cursor: pointer;
        }
        /* .modal-body-cropper{
            width: 100%;
            height: 100%;
            overflow: auto;
        } */
    </style>
@endpush

<div>
    <div class="gallery p-1 border" id="{{ $id }}">
        <div class="row gallery-row list-gallery">

            <div class="col-6 col-md-{{ $col }} gallery-col add-image-col">
                <div class="add-image d-flex justify-content-center align-items-center">
                    <div class="icon text-center">
                        <i class="fa fa-plus"></i>
                        <div class="">Upload Gambar</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="d-none">
            <input type="file" id="add-image-input">
        </div>
        <input type="hidden" name="{{ $name }}Lama">
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" data-by="#{{ $id }}" id="cropper-modal" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crop Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0 modal-body-cropper">
                    <img id="cropper-image" src="" style="max-width: 100%;" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="crop-button">Crop & Upload</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script>
        const addElm{{$id}} = $('#{{ $id }}').find('.add-image-col').prop('outerHTML');

        let {{ $id }} = '{!! @$defaultValue ? json_encode(@$defaultValue) : '' !!}';
        if ({{ $id }} !== '') {
            const {{ $var }} = JSON.parse({{ $id }});
            updateGallery('#{{ $id }}', {{ $var }});
        }

        function adjustAddImageHeight(el) {
            $(el).find('.add-image-col').height($(el).find('.add-image-col').width() * {{ $lebar }} / {{ $panjang }});
        }
        adjustAddImageHeight('#{{ $id }}');

        window.addEventListener('resize', ()=>{adjustAddImageHeight('#{{ $id }}')});

        function initAddImage(el) {
            console.log('initAddImage', el);
            $(el).find('.add-image').off('click').on('click', function() {
                console.log('clicked', el);
                
                try {
                    let fileInput = $(this).closest('.gallery').find('#add-image-input');
                    if (fileInput.length) {
                        fileInput[0].click();
                    }
                } catch (error) {
                    console.error('Error triggering input click:', error);
                }
            });
        }
        initAddImage('#{{ $id }}');

        function updateGallery(galleryId, images) {
            let el = $(galleryId).find('.list-gallery');
            let html = '';


            images.forEach((image, index) => {
                let imageUrl = '';
                let imageName = '';
                let id = ''
                if (image instanceof File) {
                    imageUrl = URL.createObjectURL(image);
                    imageName = image.name;
                } else {
                    imageUrl = '{{ asset('') }}/storage/' + image.{{ $name }};
                    split = imageUrl.split('/');
                    imageName = split[split.length - 1];
                }

                html += `
        <div class="col-6 col-md-{{ $col }} gallery-col gallery-item {{ $class }}">
            <div class="image-gallery border">
                <img src="${imageUrl}" class="image" style="width: 100%;">
                <div class="image-gallery-overlay" >
                    <div class="overlay-icon" onclick="removeImage(${index})"><i class="fa fa-times"></i></div>
                    <div class="overlay-content">${imageName}</div> 
                </div>
            </div>
        </div>`;
            });


            html += addElm{{ $id }};

            el.html(html);
            adjustAddImageHeight(galleryId);
            initAddImage(galleryId);
        }

        function removeImage(index) {
            let imageFile = {{ $var }}[index];
            {{ $var }}.splice(index, 1);

            if (typeof imageFile === 'object') {
                let imageFileName = imageFile.{{ $name }};
                let oldFileName = $('[name="{{ $name }}Lama"]').val() + '||' + imageFileName;
                if (oldFileName.substring(0, 2) === '||') oldFileName = oldFileName.substring(2);
                $('[name="{{ $name }}Lama"]').val(oldFileName);
            }
            updateGallery('#{{ $id }}', {{ $var }});
        }

        $(document).ready(function() {
            let cropper;
            let selectedFile;

            $('#{{ $id }}').find('#add-image-input').off('change').on('change', function() {
                let files = this.files;
                if (files.length) {
                    selectedFile = files[0]; // Store selected file
                    let imageUrl = URL.createObjectURL(selectedFile);

                    $('#cropper-modal[data-by="#{{$id}}"] img').attr('src', imageUrl);
                    $('#cropper-modal[data-by="#{{$id}}"]').modal('show');

                    $('#cropper-modal[data-by="#{{$id}}"] img').on('load', function() {
                        if (cropper) {
                            cropper.destroy();
                        }
                        cropper = new Cropper(this, {
                            aspectRatio: {{ $panjang }} / {{ $lebar }},
                            viewMode: 1,
                            autoCropArea: 1,
                            minContainerWidth: 800,
                            minContainerHeight: 600
                        });
                    });
                }
            });

            $('[data-by="#{{ $id }}"]').find('#crop-button').off('click').on('click', function() {
                if (cropper) {
                    cropper.getCroppedCanvas({
                        width: 800,
                        height: 600,
                    }).toBlob((blob) => {
                        let file = new File([blob], selectedFile.name, {
                            type: 'image/jpeg'
                        });

                        {{ $var }}.push(file);
                        updateGallery('#{{ $id }}', {{ $var }});
                        
                        $('#cropper-modal[data-by="#{{$id}}"]').modal('hide');
                        $('#{{ $id }}').find('#add-image-input').val('');
                        cropper.destroy();
                    }, 'image/jpeg');
                }
            });
        });
    </script>
@endpush
