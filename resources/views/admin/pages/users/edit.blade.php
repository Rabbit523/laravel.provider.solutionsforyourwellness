@extends('admin.layouts.pages_layout')
@section('content')
@section('title','User edit')
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
			{{ Form::open(['id'=>'user_edit_form','files'=>'true','method'=>'post','class' => 'form-horizontal', 'url' =>URL::route('adminedituser',$user->id)]) }}
            <form class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="first_name" class="col-sm-2 control-label">First Name</label>
                  <div class="col-sm-10">
          					 {{ Form::text('first_name',$user->first_name, ['class'=>'form-control','id'=>'first_name','placeholder' => 'First Name. ','autocomplete'=>false]) }}
          					 @if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
                  </div>
                </div>
                <div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Last Name</label>
                  <div class="col-sm-10">
                    {{ Form::text('last_name',$user->last_name, ['class'=>'form-control','id'=>'last_name','placeholder' => 'Last Name. ','autocomplete'=>false]) }}
					          @if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Username</label>
                  <div class="col-sm-10">
                    {{ Form::text('username',$user->username, ['class'=>'form-control','id'=>'username','placeholder' => 'Username','autocomplete'=>false]) }}
					          @if ($errors->has('username')) <p class="help-block">{{ $errors->first('username') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Email</label>
                  <div class="col-sm-10">
                    {{ Form::text('email',$user->email, ['class'=>'form-control','id'=>'email','placeholder' => 'Email','autocomplete'=>false]) }}
					          @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                  </div>
                </div>
				<div class="form-group">
                  <label for="last_name" class="col-sm-2 control-label">Phone</label>
                  <div class="col-sm-10">
                     {{ Form::text('phone',$user->phone, ['class'=>'form-control','id'=>'phone','placeholder' => 'Contact Number','autocomplete'=>false]) }}
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
                    <div id="image-holder">
                       @if($user->image)
                          <a class="fancybox" href="<?php echo USER_PROFILE_IMAGE_URL.'/'.$user->image ?>">
                          <img class="img-thumbnail" src="<?php echo WEBSITE_URL.'image.php?height=80px&width=120px&image='.USER_PROFILE_IMAGE_URL.'/'.$user->image ?>">
                          </a>
                          @else
                          <a class="fancybox" href="<?php echo WEBSITE_IMG_URL ?>no_user_img.png">
                          <img class="img-thumbnail" src="<?php echo WEBSITE_IMG_URL ?>no_user_img.png" height="50px" with="50px">
                          </a>
                          @endif
                    </div>
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
                {{Form::submit('Update User',['class'=>'btn btn-success','id'=>'updateuser']) }}
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
