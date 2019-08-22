@extends('admin.layouts.login_layout')
@section('title', 'Login')
@section('content')
	@if(Session::has('flash_error'))
	  <div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;{{Session::get('flash_error')}} </div>
	@endif
	 @if(Session::has('flash_success'))
		 <div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;  {{Session::get('flash_success')}} </div>
	@endif
  <div id="login-page" class="row">
    <div class="col s12 z-depth-4 card-panel">
      {{ Form::open(['class'=>'login-form','id'=>'login_form','role' => 'form', 'url' =>route('adminlogin')]) }}
        <div class="row">
          <div class="input-field col s12 center">
            <img src="{{ URL::asset('public/assets/admin/images/logo_image.png') }}" alt="" class="circle responsive-img valign profile-image-login">
            <p class="center login-form-text">Wellness admin panel</p>
          </div>
        </div>
        <div class="row margin">
          <div class="input-field col s12">
            <i class="mdi-social-person-outline prefix"></i>
						{{ Form::text('email',null, ['id'=>'email']) }}
            <label for="email" class="center-align">Email</label>
          </div>
		  @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
        </div>
        <div class="row margin">
          <div class="input-field col s12">
            <i class="mdi-action-lock-outline prefix"></i>
            {{ Form::password('password', ['id'=>'password']) }}
            <label for="password">Password</label>
						@if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12 m12 l12  login-text">
              <input type="checkbox" id="remember-me" />
              <label for="remember-me">Remember me</label>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12">
			{{Form::submit('Login',array('class'=>'btn waves-effect waves-light col s12','id' => 'login_btn'))}}
          </div>
        </div>
        <div class="row">
          <div class="input-field col s6 m6 l6">
            <!--<p class="margin medium-small"><a href="page-register.html">Register Now!</a></p>-->
          </div>
          <div class="input-field col s6 m6 l6">
              <p class="margin right-align medium-small"><a href="{{ URL::route('admin_forgot_password') }}">Forgot password ?</a></p>
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>
@stop
