<!DOCTYPE html>
<html lang="en">
<head>
@include('admin.includes.inner.head')
</head>
<body>
  @include('vendor.toast.messages-jquery')
    <!-- Start Page Loading -->
    @include('admin.includes.inner.loader')
    <!-- End Page Loading -->
    <!-- START HEADER -->
    @include('admin.includes.inner.header')
    <!-- END HEADER -->
    <!-- START MAIN -->
    <div id="main">
        <!-- START WRAPPER -->
        <div class="wrapper">
            <!-- START LEFT SIDEBAR NAV-->
            @include('admin.includes.inner.sidebar-left')
            <!-- END LEFT SIDEBAR NAV-->
            <!-- START CONTENT -->
            @yield('content')
              <!-- END CONTENT -->
            <!-- START RIGHT SIDEBAR NAV-->
            @include('admin.includes.inner.sidebar-right')
            <!-- LEFT RIGHT SIDEBAR NAV-->
        </div>
        <!-- END WRAPPER -->
    </div>
    <!-- END MAIN -->
    <!-- START FOOTER -->
    @include('admin.includes.inner.footer')
    <!-- END FOOTER -->
    @include('admin.includes.inner.script')
</body>
</html>
