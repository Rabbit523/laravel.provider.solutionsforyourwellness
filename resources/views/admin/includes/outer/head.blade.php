  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="msapplication-tap-highlight" content="no">
  <meta name="description" content="Materialize is a Material Design Admin Template,It's modern, responsive and based on Material Design by Google. ">
  <meta name="keywords" content="materialize, admin template, dashboard template, flat admin template, responsive admin template,">
  <title>Wellness - @yield('title')</title>
  <!-- Favicons-->
  <link rel="icon" href="{{ URL::asset('public/assets/admin/images/favicon/favicon-32x32.png') }}" sizes="32x32">
  <!-- Favicons-->
  <link rel="apple-touch-icon-precomposed" href="{{ URL::asset('public/assets/admin/images/favicon/apple-touch-icon-152x152.png') }}">
  <!-- For iPhone -->
  <meta name="msapplication-TileColor" content="#00bcd4">
  <meta name="msapplication-TileImage" content="{{ URL::asset('public/assets/admin/images/favicon/mstile-144x144.png') }}">
  <!-- For Windows Phone -->
  {{ Html::style('public/assets/admin/css/materialize.css') }}
  {{ Html::style('public/assets/admin/css/style.css') }}
   {{ Html::style('public/assets/admin/css/layouts/page-center.css') }}
