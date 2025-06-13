<!-- sidebar menu area start -->
<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            {{-- <a href="{{ url('/Dasboard-CRM') }}"><img src="{{ asset('assets/images/icon/logo.png') }}" alt="logo"></a> --}}
                <i ></i>
                <span style="color: aliceblue;">CRM-WEB</span>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    <!-- Dashboard -->
                    <li class="{{ Request::is('Dasboard-CRM') ? 'active' : '' }}">
                        <a href="{{ url('/Dasboard-CRM') }}">
                            <i class="ti-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Master Data -->
                    @php
                        $isMasterDataActive = Request::is('Master-*') || Request::is('master-data/*');
                    @endphp
                    <li class="{{ $isMasterDataActive ? 'active' : '' }}">
                        <a href="javascript:void(0)" aria-expanded="{{ $isMasterDataActive ? 'true' : 'false' }}">
                            <i class="fa fa-table"></i> <span>Master Data</span>
                        </a>
                        <ul class="collapse {{ $isMasterDataActive ? 'in' : '' }}">
                            @if (session('role') === 'root')
                                <li class="{{ Request::is('Master-Users') ? 'active' : '' }}">
                                    <a href="{{ url('/Master-Users') }}">M Users</a>
                                </li>
                            @endif
                            <li class="{{ Request::is('Master-Mitra') ? 'active' : '' }}">
                                <a href="{{ url('/Master-Mitra') }}">M Mitra</a>
                            </li>
                            <li class="{{ Request::is('Master-Unit') ? 'active' : '' }}">
                                <a href="{{ url('/Master-Unit') }}">M Unit</a>
                            </li>
                            <li class="{{ Request::is('Master-Bank') ? 'active' : '' }}">
                                <a href="{{ url('/Master-Bank') }}">M Bank</a>
                            </li>
                        </ul>
                    </li>


                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
