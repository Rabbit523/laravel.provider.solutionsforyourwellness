@extends('admin.layouts.pages_layout') 
@section('content')
@section('title','Change Password')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/admin/dashboard') }}" title="Go to Dashboard" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="{{ url('/admin/changepassword') }}">Change Password</a></div>
    <h1>Change Password</h1>
  </div>
  <div class="container-fluid"><hr>
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-pencil"></i> </span>
            <h5>Enter the details to change password</h5>
          </div>
          <div class="widget-content nopadding">
		  @if(Session::has('flash_message_error'))
					<div class="alert alert-error">{{Session::get('flash_message_error')}}</div>
				@endif
				 @if(Session::has('flash_message_success'))
					<div class="alert alert-success">{{Session::get('flash_message_success')}}</div>
				@endif
			<div id = "error"></div>		
			<div id = "success"></div>		
		  {{ Form::open(['id'=>'changepasswordform','class'=>'form-horizontal','method'=>'post','role' => 'form','route' => 'changepassword']) }}
              <div id="form-wizard-1" class="step">
                <div class="control-group">
                  <label class="control-label">Current Password</label>
                  <div class="controls">
                    {{ Form::password('currentpassword', ['id'=>'currentpassword','placeholder' => 'Current Password','autocomplete'=>false]) }}
					@if ($errors->has('currentpassword')) <p class="help-block">{{ $errors->first('currentpassword') }}</p> @endif
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">New Password</label>
                  <div class="controls">
                    {{ Form::password('newpassword', ['id'=>'newpassword','placeholder' => 'New Password','autocomplete'=>false]) }}
					@if ($errors->has('newpassword')) <p class="help-block">{{ $errors->first('newpassword') }}</p> @endif
                  </div>
                </div>
                <div class="control-group">
                  <label class="control-label">Confirm Password</label>
                  <div class="controls">
                    {{ Form::password('confirmpassword', ['id'=>'confirmpassword','placeholder' => 'Confirm Password','autocomplete'=>false]) }}
					@if ($errors->has('confirmpassword')) <p class="help-block">{{ $errors->first('confirmpassword') }}</p> @endif
                  </div>
                </div>
              </div>
              <div class="form-actions">
			    <a href = "{{ URL::to('admin/dashboard')}}" class = "btn btn-danger">Back</a>
				{{Form::submit('Change Password',array('class' => 'btn btn-primary'))}}
                <div id="status"></div>
              </div>
              <div id="submitted"></div>
             {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop