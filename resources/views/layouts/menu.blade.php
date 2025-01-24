@php
    $data_fitur = cache("akses_user")->toArray();
    $arr = [];
    $jumlahmenu = count($data_fitur) + 100;
@endphp

@foreach ($data_fitur as $row)
    @php
        $arr[$row->KelompokFitur][] = $row;
    @endphp
@endforeach
<div class="main-menu menu-fixed menu-light menu-accordion" data-scroll-to-active="true">
    <div class="main-menu-content">
        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            @if ($jumlahmenu > 20)
                <li class="{{ request()->is("home") || request()->is("/") ? "active" : "" }}">
                    <a href="{{ route("home") }}"><i class="feather icon-home"></i> <span
                            class="menu-title">Dashboard</span></a>
                </li>
                @foreach ($arr as $key => $val)
                    @php
                        $jumlahsub = count($val);
                    @endphp
                    @if ($jumlahsub > 1)
                        @php
                            $li_active = 'class="nav-item has-sub"';
                        @endphp
                        @foreach ($val as $k => $v)
                            @if (strtolower(@$menu) == strtolower($v->NamaFitur))
                                @php
                                    $li_active = 'class="nav-item has-sub open"';
                                    break;
                                @endphp
                            @endif
                        @endforeach
                        <li class=" nav-item"><a href="#"><i class="feather icon-monitor"></i><span
                                    class="menu-title" data-i18n="Templates">Templates</span></a>
                        <li {!! $li_active !!}>
                            <a href="#"><i class="{{ @$val[0]->Icon }}"></i>
                                <span class="menu-title">{{ ucwords(strtolower($key)) }}</span>
                            </a>
                            <ul class="menu-content">
                                @foreach ($val as $k => $v)
                                    @php
                                        $active =
                                            strtolower(@$menu) == strtolower($v->NamaFitur) ? 'class="active"' : "";
                                        $url = @$v->Slug ?? "admin/home";
                                    @endphp

                                    <li {!! $active !!}>
                                        <a href="{{ url($url) }}"><span
                                                class="menu-item">{{ $v->NamaFitur }}</span></a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        @foreach ($val as $k => $v)
                            @php
                                $active = strtolower(@$menu) == strtolower($v->NamaFitur) ? 'class="active"' : "";
                                $url = @$v->Slug ?? "admin/home";
                            @endphp

                            <li {!! $active !!}>
                                <a href="{{ url($url) }}"><i class="{{ $v->Icon }}"></i>
                                    <span class="menu-title">{{ $v->NamaFitur }}</span></a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
            @else
                <li class="{{ request()->is("home") || request()->is("/") ? "active" : "" }}">
                    <a href="{{ route("home") }}"><i class="feather icon-home"></i> <span
                            class="menu-title">Dashboard</span></a>
                </li>
                @foreach ($arr as $key => $val)
                    @php
                        $jumlahsub = count($val);
                    @endphp
                    @if ($jumlahsub > 1)
                        <li class="navigation-header"><span>{{ strtoupper($key) }}</span><i class=" feather icon-minus"
                                data-toggle="tooltip" data-placement="right"
                                data-original-title="{{ strtoupper($key) }}"></i>
                        </li>
                    @endif
                    @foreach ($val as $k => $v)
                        @php
                            $active = strtolower(@$menu) == strtolower($v->NamaFitur) ? 'class="active"' : "";
                            $url = @$v->Slug ?? "admin/home";
                        @endphp

                        <li {!! $active !!}>
                            <a href="{{ url($url) }}"><i class="{{ $v->Icon }}"></i>
                                <span class="menu-title">{{ $v->NamaFitur }}</span></a>
                        </li>
                    @endforeach
                @endforeach
            @endif
        </ul>
    </div>
</div>
