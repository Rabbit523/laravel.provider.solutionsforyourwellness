<!DOCTYPE html>
<html lang="en">
<head>
@include('admin.includes.outer.head')
</head>
<body class="cyan">
@include('vendor.toast.messages-jquery')
@include('admin.includes.outer.loader')
<!-- /.Main content starts -->
@yield('content')
<!-- /.Main content ends -->
@include('admin.includes.outer.script')
</body>
</html>
