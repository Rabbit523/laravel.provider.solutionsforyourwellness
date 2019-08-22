@extends('admin.layouts.login_layout')
@section('title', 'Forgot Password')
@section('content')
<div class="login-box-body">
    <p class="login-box-msg">Enter the email to recover password.</p>
	@if(Session::has('flash_message_error'))
	  <div class="alert alert-danger"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;{{Session::get('flash_message_error')}} </div>
	@endif
	 @if(Session::has('flash_message_success'))
		 <div class="alert alert-success"> <span class="glyphicon glyphicon-info-sign"></span> &nbsp;  {{Session::get('flash_message_success')}} </div>
	@endif
	{{ Form::open(['id'=>'forgot_password_form','role' => 'form', 'url' =>route('admin_forgot_password')]) }}
      <div class="form-group has-feedback">
        {{ Form::text('email',null, ['class'=>'form-control','id'=>'email','placeholder' => 'Enter the Email. ','autocomplete'=>false]) }}
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
	  @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
      <div class="row">
        <div class="col-xs-6">
		  {{Form::submit('Recover Password',array('class'=>'btn btn-primary btn-block btn-flat'))}}
        </div>
        <!-- /.col -->
      </div>
   {{ Form::close() }}
  </div>
  <!-- /.login-box-body -->
@stop
