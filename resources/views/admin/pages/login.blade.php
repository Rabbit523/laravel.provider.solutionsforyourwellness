@extends('admin.layouts.login_layout')
@section('title', 'Login')
@section('content')
<div class="login-box-body">
    <p class="login-box-msg">Sign in to start your session</p>
	@if(Session::has('flash_message_error'))
	  <div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;{{Session::get('flash_message_error')}} </div>
	@endif
	 @if(Session::has('flash_message_success'))
		 <div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;  {{Session::get('flash_message_success')}} </div>
	@endif
	{{ Form::open(['id'=>'login_form','role' => 'form', 'url' =>route('adminlogin')]) }}
      <div class="form-group has-feedback">
        {{ Form::text('username_or_email',null, ['class'=>'form-control','id'=>'username_or_email','placeholder' => 'Enter the username or email ','autocomplete'=>false]) }}
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
	  @if ($errors->has('username_or_email')) <p class="help-block">{{ $errors->first('username_or_email') }}</p> @endif
      <div class="form-group has-feedback">
        {{ Form::password('password', ['class'=>'form-control','id'=>'password','placeholder' => 'Enter the Password','autocomplete'=>false]) }}
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
	  @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"> Remember Me
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
		  {{Form::submit('Sign In',array('class'=>'btn btn-primary btn-block btn-flat','id' => 'login_btn'))}}
        </div>
        <!-- /.col -->
      </div>
   {{ Form::close() }}

    <div class="social-auth-links text-center">
      <p>- OR -</p>
      <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
        Facebook</a>
      <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
        Google+</a>
    </div>
    <!-- /.social-auth-links -->
	<a href="{{ URL::route('admin_forgot_password') }}">Forgot Password ?</a><br>
    <a href="register.html" class="text-center">Register a new membership</a>

  </div>
  <!-- /.login-box-body -->
@stop
