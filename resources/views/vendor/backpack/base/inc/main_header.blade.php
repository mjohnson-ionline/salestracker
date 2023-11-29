<header class="{{ config('backpack.base.header_class') }}">
    <a class="navbar-brand" href="{{ url(config('backpack.base.home_link')) }}" title="{{ config('backpack.base.project_name') }}" style="font-size:13px;text-align: center;">
        <img src="{{ asset('img/Sales-Tracker-White-Logo.png') }}" style="width: 150px;margin-left:45px;margin-top:10px;margin-bottom:10px;">
    </a>
    @include(backpack_view('inc.menu'))
</header>
