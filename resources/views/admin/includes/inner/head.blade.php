  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="msapplication-tap-highlight" content="no">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <title>{{PROJECT_NAME}} - @yield('title')</title>
  <!-- Favicons-->
  <link rel="icon" href="{{ URL::asset('public/assets/admin/images/favicon.png') }}" sizes="32x32">
  <!-- Favicons-->

  <link rel="apple-touch-icon-precomposed" href="{{ URL::asset('public/assets/admin/images/favicon/apple-touch-icon-152x152.png') }}">
  <!-- For iPhone -->
  <meta name="msapplication-TileColor" content="#00bcd4">
  <meta name="msapplication-TileImage" content="{{ URL::asset('public/assets/admin/images/favicon/mstile-144x144.png') }}">
  <!-- For Windows Phone -->
  <!-- CORE CSS-->
  {{ Html::style('public/assets/admin/css/materialize.css') }}
  {{ Html::style('public/assets/admin/css/style.css') }}
  <!-- Custome CSS-->
  {{ Html::style('public/assets/admin/css/custom/custom.css') }}
  <!-- INCLUDED PLUGIN CSS ON THIS PAGE -->
  {{ Html::style('public/assets/admin/js/plugins/perfect-scrollbar/perfect-scrollbar.css') }}
  {{ Html::style('public/assets/admin/js/plugins/jvectormap/jquery-jvectormap.css') }}
  {{ Html::style('public/assets/admin/js/plugins/chartist-js/chartist.min.css') }}
  {{ Html::style('public/assets/admin/js/plugins/data-tables/css/jquery.dataTables.min.css') }}

  {{ Html::style('public/assets/admin/js/plugins/perfect-scrollbar/perfect-scrollbar.css') }}
  {{ Html::style('public/assets/admin/css/jquery.fancybox.css') }}
  {{ Html::style('public/assets/admin/css/select2.min.css') }}

  {{ Html::style('public/assets/admin/css/clockpicker.css') }}

  {{ Html::style('public/assets/admin/js/plugins/fullcalendar/css/fullcalendar.min.css') }}
  {{ Html::script('public/assets/admin/js/plugins/jquery-1.11.2.min.js') }}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/qtip2/3.0.3/jquery.qtip.css" rel="stylesheet"/>
  {{ Html::script('public/assets/admin/js/plugins/fullcalendar/lib/jquery-ui.custom.min.js') }}
  {{ Html::script('public/assets/admin/js/plugins/fullcalendar/lib/moment.min.js') }}
  {{ Html::script('public/assets/admin/js/plugins/fullcalendar/js/fullcalendar.min.js') }}
  {{ Html::script('public/assets/admin/js/plugins/fullcalendar/fullcalendar-script.js') }}

  <!-- {{ Html::script('public/assets/admin/fc/moment.min.js') }}
  {{ Html::script('public/assets/admin/fc/jquery.min.js') }}
  {{ Html::script('public/assets/admin/fc/fullcalendar.min.js') }} -->

  <style>
  table.dataTable tbody th, table.dataTable tbody td {
      padding: 8px 17px;
  }
  #searchInput{z-index: 0;
    position: absolute;
    top: 0px;
    left: 0px;

  }
  </style>
