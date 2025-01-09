@extends("layouts.app")

@section("title")
    {{ $title }}
@endsection

@section("content")
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <x-data-table :config="$config" />
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
