@extends('admin.layouts.pages_layout')
@section('content')
@section('title','Profile')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User Profile
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Examples</a></li>
        <li class="active">User profile</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
			@if(Auth::user()->image)
				<a class="fancybox" href="{{ WEBSITE_UPLOADS_URL }}users/{{ Auth::user()->image }}">
					<img class="profile-user-img img-responsive img-circle" src="{{ WEBSITE_UPLOADS_URL }}users/500x500/{{ Auth::user()->image }}" alt="profile-image">
				</a>
			@else
			<a class="fancybox" href="{{ URL::asset('public/uploads/users/NoPreview.png') }}">
				<img class="profile-user-img img-responsive img-circle" src="{{ URL::asset('public/uploads/users/NoPreview.png') }}" class = "img-thumbnail" id = "user_profile_picture" alt="profile-image">
			</a>
			@endif


              <h3 class="profile-username text-center">{{ Auth::user()->first_name.' '.Auth::user()->last_name }}</h3>
              <p class="text-muted text-center">{{ $admin->designation }}</p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">About Me</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <strong><i class="fa fa-book margin-r-5"></i> Education</strong>

              <p class="text-muted">
			             {{ $admin->education }}
              </p>
              <hr>
              <strong><i class="fa fa-map-marker margin-r-5"></i> Location</strong>
              <p class="text-muted">{{ Auth::user()->address }}</p>
              <hr>
              <strong><i class="fa fa-user margin-r-5"></i> Username</strong>

              <p class="text-muted">
			             {{ Auth::user()->username }}
              </p>
              <hr>
              <strong><i class="fa fa-phone margin-r-5"></i> Contact info.</strong>
              <p>
                Phone: {{ Auth::user()->phone }}<br>
                Email: {{ Auth::user()->email }}
              </p>
              <hr>
              <strong><i class="fa fa-file-text-o margin-r-5"></i> Experience</strong>
              <p>{{ $admin->experience }}</p>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#activity" data-toggle="tab">Edit Profile</a></li>
              <li><a href="#timeline" data-toggle="tab">Change Password</a></li>
            </ul>
            <div class="tab-content">
              <div class="active tab-pane" id="activity">
                {{ Form::open(['class'=>'form-horizontal','id'=>'user_create_form','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('adminprofile')]) }}
                           <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">First Name<span class="danger">*</span></label>
                            <div class="col-sm-10">
        					{{ Form::text('first_name',Auth::user()->first_name, ['class'=>'form-control','id'=>'first_name','placeholder' => 'First Name. ','autocomplete'=>false]) }}
        					@if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
                            </div>
                          </div>
        				  <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Last Name<span class="danger">*</span></label>
                            <div class="col-sm-10">
        					{{ Form::text('last_name',Auth::user()->last_name, ['class'=>'form-control','id'=>'last_name','placeholder' => 'Last Name. ','autocomplete'=>false]) }}
        					@if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
                            </div>
                          </div>
        				  <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Username<span class="danger">*</span></label>
                            <div class="col-sm-10">
        					{{ Form::text('username',Auth::user()->username, ['class'=>'form-control','id'=>'username','placeholder' => 'Username','autocomplete'=>false]) }}
        					@if ($errors->has('username')) <p class="help-block">{{ $errors->first('username') }}</p> @endif
                            </div>
                          </div>
        				  <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Phone<span class="danger">*</span></label>
                            <div class="col-sm-10">
        					{{ Form::text('phone',Auth::user()->phone, ['class'=>'form-control','id'=>'phone','placeholder' => 'Contact Number','autocomplete'=>false]) }}
        					@if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="inputEmail" class="col-sm-2 control-label">Email<span class="danger">*</span></label>
                            <div class="col-sm-10">
                              {{ Form::text('email',Auth::user()->email, ['class'=>'form-control','id'=>'email','placeholder' => 'Email','autocomplete'=>false]) }}
        						          @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Image <span class="danger">*</span></label>

                            <div class="col-sm-10">
                              <input name="image" id="image" type="file"/>
                            </div>
                          </div>
                          <div class="form-group">
                              <label for="inputName" class="col-sm-2 control-label"><span> </span></label>
                                <div class="col-sm-10">
                                  <div id="image-holder">
                                    @if(Auth::user()->image)
                                    <a class="fancybox" href="<?php echo WEBSITE_UPLOADS_URL.'/users/'.Auth::user()->image ?>">
                                    <img class="img-thumbnail" src="<?php echo WEBSITE_URL.'image.php?height=80px&width=120px&image='.WEBSITE_UPLOADS_URL.'users/'.Auth::user()->image ?>">
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
                            <label for="inputSkills" class="col-sm-2 control-label">Location<span class="danger">*</span></label>
                            <div class="col-sm-10">
                              {{ Form::textarea('address',Auth::user()->address, ['rows'=>'4','class'=>'form-control','id'=>'address','placeholder' => 'Enter the full address','autocomplete'=>false]) }}
        					            @if ($errors->has('address')) <p class="help-block">{{ $errors->first('address') }}</p> @endif
                            </div>
                          </div>
        				  <div class="form-group">
                            <label for="inputExperience" class="col-sm-2 control-label">Designation<span class="danger">*</span></label>
                            <div class="col-sm-10">
                              {{ Form::text('designation',$admin->designation,['class'=>'form-control','id'=>'designation','placeholder' => 'Enter the designation','autocomplete'=>false]) }}
        					  @if ($errors->has('skills')) <p class="help-block">{{ $errors->first('skills') }}</p> @endif
                            </div>
                          </div>
                          <div class="form-group">
                            <label for="inputExperience" class="col-sm-2 control-label">Experience<span class="danger">*</span></label>
                            <div class="col-sm-10">
                              {{ Form::textarea('experience',$admin->experience, ['rows'=>'4','class'=>'form-control','id'=>'experience','placeholder' => 'Enter the experience','autocomplete'=>false]) }}
        					  @if ($errors->has('skills')) <p class="help-block">{{ $errors->first('skills') }}</p> @endif
                            </div>
                          </div>
        				  <div class="form-group">
                            <label for="inputExperience" class="col-sm-2 control-label">Education details<span class="danger">*</span></label>
                            <div class="col-sm-10">
                              {{ Form::textarea('education',$admin->education, ['rows'=>'4','class'=>'form-control','id'=>'education','placeholder' => 'Enter the education details','autocomplete'=>false]) }}
        					  @if ($errors->has('education')) <p class="help-block">{{ $errors->first('education') }}</p> @endif
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                              {{Form::submit('Update Profile',['class'=>'btn btn-success','id'=>'updateprofile']) }}
                            </div>
                          </div>
                        {{ Form::close() }}
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="timeline">
                {{ Form::open(['class'=>'form-horizontal','id'=>'change_password_form','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('adminprofile')]) }}
                           <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Current Password<span class="danger">*</span></label>
                            <div class="col-sm-10">
        					                 {{ Form::password('current_password', ['class'=>'form-control','id'=>'current_password','placeholder' => 'Current Password. ','autocomplete'=>false]) }}
        					                 @if ($errors->has('current_password')) <p class="help-block">{{ $errors->first('current_password') }}</p> @endif
                            </div>
                          </div>
        				  <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">New Password<span class="danger">*</span></label>
                            <div class="col-sm-10">
        					{{ Form::password('new_password', ['class'=>'form-control','id'=>'new_password','placeholder' => 'New password','autocomplete'=>false]) }}
        					@if ($errors->has('new_password')) <p class="help-block">{{ $errors->first('new_password') }}</p> @endif
                            </div>
                          </div>
        				  <div class="form-group">
                            <label for="inputName" class="col-sm-2 control-label">Confirm Password<span class="danger">*</span></label>
                            <div class="col-sm-10">
        					            {{ Form::password('confirm_password', ['class'=>'form-control','id'=>'confirm_password','placeholder' => 'Confirm password','autocomplete'=>false]) }}
        					            @if ($errors->has('confirm_password')) <p class="help-block">{{ $errors->first('confirm_password') }}</p> @endif
                            </div>
                    </div>
                    <div class="form-group">
                      <div class="col-sm-offset-2 col-sm-10">
                        {{Form::submit('Change Password',['class'=>'btn btn-success','id'=>'changepassword']) }}
                      </div>
                    </div>
                    {{ Form::close() }}
              </div>
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@stop
