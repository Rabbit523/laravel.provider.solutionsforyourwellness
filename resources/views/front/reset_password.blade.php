@extends('admin.layouts.login_layout')
@section('title', 'Reset password')
@section('content')

  <div id="login-page" class="row">
    <div class="col s12 z-depth-4 card-panel">
			{{ Form::open(['id' => 'resetpasswordform','class' => 'form-vertical','role' => 'form','url'=>URL::route( 'api_user_reset_password',$reset_password_token)]) }}
			{{ Form::hidden('reset_password_token',$reset_password_token, []) }}
        <div class="row">
          <div class="input-field col s12 center">
            <img src="{{ URL::asset('public/assets/admin/images/logo_image.png') }}" alt="" class="circle responsive-img valign profile-image-login">
            <p class="center login-form-text">Reset password</p>
          </div>
        </div>
        <div class="row margin">
          <div class="input-field col s12">
            <i class="mdi-action-lock-outline prefix"></i>
						{{ Form::password('newpassword',['id'=>'newpassword']) }}

            <label for="newpassword" class="center-align">New password</label>
          </div>
        </div>
        <div class="row margin">
          <div class="input-field col s12">
            <i class="mdi-action-lock-outline prefix"></i>
            {{ Form::password('confirmpassword', ['autocomplete'=>false,'id'=>'confirmpassword']) }}
            <label for="confirmpassword">Confirm password</label>
          </div>
        </div>
        <div class="row">
          <div class="input-field col s12">
			{{Form::submit('Submit',array('class'=>'btn waves-effect waves-light col s12'))}}
          </div>
        </div>
        <div class="row">
          <div class="input-field col s6 m6 l6">
            <!--<p class="margin medium-small"><a href="page-register.html">Register Now!</a></p>-->
          </div>

        </div>
      {{ Form::close() }}
    </div>
  </div>
@stop
