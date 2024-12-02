@extends("layouts.app")

@section("title")
    {{ $title }}
@endsection

@section("content")
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Zero configuration oo</h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <x-data-table :columns="$columns" ajaxUrl="{{ $ajaxUrl }}" title="{{ $title }}"
                                    addButton="{{ $addButton }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section("scripts")
    {{-- @include("home.scripts") --}}
@endsection
