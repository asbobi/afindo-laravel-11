@push('styles')
    <style>
        .table-input .form-control {
            padding: 4px 8px;
            height: 34px;
        }

        .table-input {
            border-collapse: collapse;
        }

        .table-input td {
            vertical-align: middle;
        }

        .add-item-btn,
        .table-input .btn {
            padding: 7.25px 10px;
        }

        .table-input .select2-selection__rendered {
            line-height: 22px !important;
        }

        .table-input .select2-container .select2-selection--single {
            height: 34px !important;
        }

        .table-input .select2-selection__arrow {
            height: 34px !important;
        }

        .table-input .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 1px !important;
        }

        /*Select2 ReadOnly Start*/
        .table-input select[readonly].select2-hidden-accessible+.select2-container {
            pointer-events: none;
            touch-action: none;
        }

        .table-input select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
            background: #eee;
            box-shadow: none;
        }

        .table-input select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
        .table-input select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
            display: none;
        }

        /*Select2 ReadOnly End*/
    </style>
@endpush

{!! $cover[0] !!}
<div class="form-group">
    {!! $label !!}
    <div class="table-wrapper">
        <table class="table table-input table-bordered w-100 {{ $class }}">
            <thead>
                <tr>
                    <th>No</th>
                    @foreach ($columns as $column)
                        <th>{{ $column['header'] }}</th>
                    @endforeach
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $edit = $items !== null;
                    if (empty($items) || !is_array($items)) {
                        $items = [[]];
                    }
                @endphp
                @foreach (@$items as $item)
                    <tr>
                        <td>
                            <span class="no">1</span>
                        </td>
                        @foreach ($columns as $column)
                            <td>

                                @if ($column['type'] == 'select')
                                    <select class="form-control select2-item" name="{{ $column['data'] }}[]" {{ !$column['editable'] && $edit ? 'readonly' : '' }}>
                                        @foreach ($column['options'] as $label => $value)
                                            <option value="{{ $value }}"
                                                {{ @$item->{$column['data']} == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                @elseif($column['type'] == 'textarea')
                                    <textarea class="form-control" name="{{ $column['data'] }}[]" {{ !$column['editable'] && $edit ? 'readonly' : '' }}>{{ @$item->{$column['data']} }}</textarea>
                                @else
                                    <input type="text" class="form-control" name="{{ $column['data'] }}[]"
                                        value="{{ @$item->{$column['data']} }}"
                                        {{ !$column['editable'] && $edit ? 'readonly' : '' }}>
                                @endif

                                {!! $column['append'] !!}
                            </td>
                        @endforeach
                        <td>
                            <button type="button" class="btn text-danger" onclick="removeItem(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="w-100 item-action">
        <button type="button" class="btn btn-primary w-100 add-item-btn" onclick="addItem()"><i class="fa fa-plus"></i>
            Tambah</button>
    </div>
</div>
{!! $cover[1] !!}


@push('scripts')
    <script>
        //table input action
        let rowTemplate = $('.table-input tbody tr').first().clone();
        $(rowTemplate).find('input, select, textarea').attr('value', '').change();
        $(rowTemplate).find('input, select, textarea').removeAttr('readonly');
        rowTemplate = rowTemplate[0].outerHTML;
        

        function addItem() {
            const row = $(rowTemplate);
            $('.table-input tbody').append(row);
            orderNumber();
        }

        function removeItem(button) {
            $(button).closest('tr').remove();
            if ($('.table-input tbody tr').length == 0) addItem();
            orderNumber();
        }

        function orderNumber() {
            $('.table-input tbody tr').each(function(index) {
                $(this).find('.no').text(index + 1);
            });
            initSelect2Item();
        }

        function initSelect2Item() {
            $('.select2-item').select2({
                width: '100%'
            });
        }
        $(document).ready(function() {
            orderNumber();
            initSelect2Item();
        })
    </script>
@endpush
