<!-- header area start -->
<div class="header-area">
    <div class="row align-items-center">
        <!-- nav and search button -->
        <div class="col-md-6 col-sm-8 clearfix">
            <div class="nav-btn pull-left">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <!-- profile info & task notification -->
        <div class="col-md-6 col-sm-4 clearfix">
            <ul class="notification-area pull-right">
                {{-- <li id="full-view"><i class="ti-fullscreen"></i></li>
                <li id="full-view-exit"><i class="ti-zoom-out"></i></li> --}}
                <li class="dropdown user-dropdown" style="position: relative; list-style: none;">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="font-weight: bold; color: inherit; cursor: pointer;">
                        @if(Session::has('nama_user')) {{ Session::get('nama_user') }} @endif
                        <i class="fa fa-angle-down ml-1"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="min-width: 150px;">
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="dropdown-item" style="background: none; border: none; padding: 0; cursor: pointer;">
                                <i class="fa fa-sign-out-alt mr-2"></i> Log Out
                            </button>
                        </form>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- header area end -->
<!-- page title area start -->
{{-- <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Dashboard</h4>
                <ul class="breadcrumbs pull-left">
                    <li><a href="index.html">Home</a></li>
                    <li><span>Dashboard</span></li>
                </ul>
            </div>
        </div>

    </div>
</div> --}}
<!-- page title area end -->
