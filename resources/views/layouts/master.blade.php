<!DOCTYPE html>
<html lang="en">
    <!-- Head -->
    <head>
        @include('layouts.head')
    </head>
    <!-- Body -->
    <body>
        <div id="preloader">
            <div class="loader"></div>
        </div>
        <div class="page-container">
            <!-- Header -->
            @include('layouts.header')
            <!-- Sidebar -->
            @include('layouts.sidebar')
            <!-- Konten -->
            @yield('content')
            <!-- Sidebar -->
            @include('layouts.footer')
        </div>
        <!-- Script -->
        @include('layouts.scripts')
    </body>
</html>