@extends('admin.layouts.pages_layout') 
@section('content')
@section('title','Edit Profile')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/admin/dashboard') }}" title="Go to Dashboard" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="{{ url('/admin/my-profile') }}">My Profile</a> <a href="{{ url('/admin/editprofile') }}" class="current">Edit Profile</a> </div>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-pencil"></i> </span>
            <h5>Enter the details to change profile</h5>
          </div>
          <div class="widget-content nopadding">
		  @if(Session::has('flash_message_error'))
					<div class="alert alert-error">{{Session::get('flash_message_error')}}</div>
				@endif
				 @if(Session::has('flash_message_success'))
					<div class="alert alert-success">{{Session::get('flash_message_success')}}</div>
				@endif
		  {{ Form::open(['id'=>'editprofileform','class'=>'form-horizontal','role' => 'form','route' => 'editprofile', 'files' => true]) }}
              <div id="form-wizard-1" class="step">
                <div class="control-group">
                  <label class="control-label">First Name</label>
                  <div class="controls">
                    {{ Form::text('first_name', Auth::user()->first_name, ['placeholder' => 'First Name']) }}
					@if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
                  </div>
                </div>
				<div class="control-group">
                  <label class="control-label">Last Name</label>
                  <div class="controls">
                    {{ Form::text('last_name', Auth::user()->last_name, ['placeholder' => 'Last Name']) }}
					@if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
                  </div>
                </div>
				<div class="control-group">
                  <label class="control-label">Username</label>
                  <div class="controls">
                    {{ Form::text('username', Auth::user()->username, ['placeholder' => 'Username']) }}
					@if ($errors->has('username')) <p class="help-block">{{ $errors->first('username') }}</p> @endif
                  </div>
                </div>
				<div class="control-group">
                  <label class="control-label">Email</label>
                  <div class="controls">
                    {{ Form::text('email', Auth::user()->email, ['placeholder' => 'Email']) }}
					@if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                  </div>
                </div>
				<div class="control-group">
                  <label class="control-label">Phone</label>
                  <div class="controls">
                    {{ Form::text('phone', Auth::user()->phone, ['placeholder' => 'Phone Number']) }}
					@if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                  </div>
                </div>
				<div class="control-group">
				  <label class="control-label">Gender</label>
				  <div class="controls">
					<label>
					  {{Form::radio('gender', 'male',Auth::user()->gender == 'male')}}
					  Male
					</label>
					<label>
					  {{Form::radio('gender', 'female',Auth::user()->gender == 'female')}}
					  Female
					</label>
					@if ($errors->has('gender')) <p class="help-block">{{ $errors->first('gender') }}</p> @endif
				  </div>
				</div>
				<div class="control-group">
				  <label class="control-label">File upload input</label>
				  <div class="controls">
					{{ Form::file('profilepicture', ['id'=>'profilepicture']) }}
					@if ($errors->has('profilepicture')) <p class="help-block">{{ $errors->first('profilepicture') }}</p> @endif
				  </div>
				</div>
				<div class="control-group">
				  <label class="control-label"></label>
				  <div class="controls">
					<div id = "image-holder">
					<img src="{{ URL::asset('public/uploads/'.Auth::user()->image) }}" alt="profile-image" height ="80px" width = "80px"/>
					</div>
				  </div>
				</div>
              </div>
              <div class="form-actions">
			    <a href = "{{ URL::to('admin/users-list')}}" class = "btn btn-danger">Back</a>
				{{Form::submit('Update profile',array('class' => 'btn btn-primary'))}}
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