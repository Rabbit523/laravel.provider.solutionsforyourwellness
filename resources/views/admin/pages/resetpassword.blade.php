@extends('admin.layouts.login_layout')
@section('title', 'Reset Password')
@section('content')
<div id="loginbox">
			{{ Form::open(['id' => 'resetpasswordform','class' => 'form-vertical','role' => 'form','url' => 'admin/reset_password/'.$reset_password_token]) }}
			{{ Form::hidden('reset_password_token',$reset_password_token, []) }}

                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lg"><i class="icon-lock"> </i></span>
							{{ Form::password('newpassword', ['placeholder' => 'newpassword']) }}
							@if ($errors->has('newpassword')) <p class="help-block">{{ $errors->first('newpassword') }}</p> @endif
                        </div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_ly"><i class="icon-lock"></i></span>
							{{ Form::password('confirmpassword', ['placeholder' => 'Confirm Password','autocomplete'=>false]) }}
							@if ($errors->has('confirmpassword')) <p class="help-block">{{ $errors->first('confirmpassword') }}</p> @endif
                        </div>
                    </div>
                </div>
                <div class="form-actions">

                    <span class="pull-left">
					{{Form::submit('Change Password',array('class' => 'btn btn-success'))}}
					</span>
					<span class="pull-right">
	<a href="{{ url('/admin/login') }}" class="flip-link btn btn-info" id="to-recover">Login</a>
</span>
                </div>
            {{ Form::close() }}
        </div>
@stop
