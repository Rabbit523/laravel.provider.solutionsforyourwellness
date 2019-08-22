@extends('admin.layouts.pages_layout')
@section('content')
@section('title','User Creation')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        General Form Elements
        <small>Preview</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-10">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">User creation</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
			{{ Form::open(['id'=>'user_create_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('admincreateuser')]) }}
            <form class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="first_name" class="col-sm-2 control-label">First Name</label>
                  <div class="col-sm-10">
					{{ Form::text('first_name',null, ['class'=>'form-control','id'=>'first_name','placeholder' => 'First Name. ','autocomplete'=>false]) }}
					 @if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Last Name</label>
                  <div class="col-sm-10">
                    {{ Form::text('last_name',null, ['class'=>'form-control','id'=>'last_name','placeholder' => 'Last Name. ','autocomplete'=>false]) }}
					@if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Username</label>
                  <div class="col-sm-10">
                    {{ Form::text('username',null, ['class'=>'form-control','id'=>'username','placeholder' => 'Username','autocomplete'=>false]) }}
					@if ($errors->has('username')) <p class="help-block">{{ $errors->first('username') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Email</label>
                  <div class="col-sm-10">
                    {{ Form::text('email',null, ['class'=>'form-control','id'=>'email','placeholder' => 'Email','autocomplete'=>false]) }}
					@if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Password</label>
                  <div class="col-sm-10">
                     {{ Form::password('password',['class'=>'form-control','id'=>'password','placeholder' => 'Password','autocomplete'=>false]) }}
					 @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Confirm Password</label>
                  <div class="col-sm-10">
				  {{ Form::password('confirm_password',['class'=>'form-control','id'=>'confirm_password','placeholder' => 'Confirm Password','autocomplete'=>false]) }}
				  @if ($errors->has('confirm_password')) <p class="help-block">{{ $errors->first('confirm_password') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Phone</label>
                  <div class="col-sm-10">
                     {{ Form::text('phone',null, ['class'=>'form-control','id'=>'phone','placeholder' => 'Contact Number','autocomplete'=>false]) }}
					 @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Image</label>
                  <div class="col-sm-10">
                     <input name="image" id="image" type="file"/>
					 @if ($errors->has('image')) <p class="help-block">{{ $errors->first('image') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label"></label>
                  <div class="col-sm-10">
                     <div id="image-holder"></div>
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
				{{Form::submit('Create User',['class'=>'btn btn-success','id'=>'createuser']) }}
				<a href="{{URL::route('userslist')}}" class="btn btn-danger">Back</a>
              </div>
              <!-- /.box-footer -->
            {{ Form::close() }}
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@stop
