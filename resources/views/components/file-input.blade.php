<div class="input-group">
    <input type="hidden" name="{{ $name }}Lama" value="{{ $url != "" ? basename($url) : "" }}">
    <input type="file" class="form-control my-file-input" name="{{ $name }}" style="display:none;">
    <input type="text" class="form-control file-name" value="{{ $url != "" ? basename($url) : "" }}"
        placeholder="No file chosen" readonly>
    <div class="input-group-append">
        <label class="btn btn-primary choose-file-btn">{{ $label }}</label>
    </div>
    @if ($url != "")
        <div class="input-group-append">
            <label class="btn btn-secondary download-file-btn" data-url="{{ $url }}">Download</label>
        </div>
    @endif
</div>

@push("scripts")
    <script>
        document.addEventListener('change', function(event) {
            if (event.target.classList.contains('my-file-input')) {
                var fileInput = event.target;
                var fileName = fileInput.files[0] ? fileInput.files[0].name : "No file chosen";
                var container = fileInput.closest('.input-group');
                var fileNameInput = container.querySelector('.file-name');
                fileNameInput.value = fileName;
            }
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('choose-file-btn')) {
                var container = event.target.closest('.input-group');
                var fileInput = container.querySelector('.my-file-input');
                fileInput.click();
            }

            if (event.target.classList.contains('download-file-btn')) {
                var url = event.target.getAttribute('data-url');
                if (url) {
                    window.open(url, '_blank');
                } else {
                    console.log('URL is not defined');
                }
            }
        });
    </script>
@endpush
