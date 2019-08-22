<!DOCTYPE html>
<html lang="en">
<head>
@include('admin.includes.inner.head')
</head>
<body class="hold-transition skin-blue sidebar-mini">
@include('vendor.toast.messages-jquery')
<div class="wrapper">
@include('admin.includes.inner.header')
@include('admin.includes.inner.sidebar-left')
@yield('content')
@include('admin.includes.inner.footer')
@include('admin.includes.inner.sidebar-right')
</div>
@include('admin.includes.inner.script')
@ckeditor('body')
</body>
</html>
