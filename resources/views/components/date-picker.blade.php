<div class="input-group">
    <div class="input-group-prepend">
        <span class="input-group-text">
            <span class="fa fa-calendar-o"></span>
        </span>
    </div>
    <input type='text' id="{{ $id }}" name="{{ $name }}" class="form-control pickadate-format"
        placeholder="{{ $placeholder ?? "Pilih Tanggal" }}" required
        value="{{ $value ?? \Carbon\Carbon::now()->format("Y-m-d") }}" />
</div>

@push("scripts")
    <script>
        $(document).ready(function() {
            $('#{{ $id }}').pickadate({
                format: 'yyyy-mm-dd',
                formatSubmit: false,
                // Set default value to today if no value is provided
                onStart: function() {
                    var date = "{{ $value ?? \Carbon\Carbon::now()->format("Y-m-d") }}";
                    this.set('select', date);
                }
            });
        });
    </script>
@endpush
