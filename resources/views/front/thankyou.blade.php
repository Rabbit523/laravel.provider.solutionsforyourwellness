@extends('admin.layouts.login_layout')
@section('title', 'Reset password')
@section('content')

  <div id="login-page" class="row">
    <div class="col s12 z-depth-4 card-panel">
			<div class="row">
          <div class="input-field col s12 center">
            <img src="{{ URL::asset('public/assets/admin/images/logo_image.png') }}" alt="" class="circle responsive-img valign profile-image-login">
            <p class="center login-form-text">Reset password</p>
          </div>
        </div>
			<p style="color:green">Your password changed successfully</p>
    </div>
  </div>
@stop
