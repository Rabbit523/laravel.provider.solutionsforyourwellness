@extends('admin.layouts.default_layout')
@section('content')
@section('title','Edit profile')
<!-- Content Wrapper. Contains page content -->
<section id="content">
      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Profile') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Edit profile') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <div id="preselecting-tab" class="section">
            <div class="row">
              <div class="col-md-12">
                <div class="row">
                  <div class="col s12">
                    <ul class="tabs tab-demo-active z-depth-1 cyan">

                      <li class="tab col s3"><a class="white-text waves-effect waves-light active" href="#edit_profile">Edit profile</a>
                      </li>
                      <li class="tab col s3"><a class="white-text waves-effect waves-light " href="#profile">Change security pin</a>
                      </li>
                      <li class="tab col s3"><a class="white-text waves-effect waves-light" href="#change_password">Change password</a>
                      </li>
					  <li class="tab col s3"><a class="white-text waves-effect waves-light" href="#notification_settings">Notification settings</a>
                      </li>
                    </ul>
                  </div>
                  <div class="col s12">

          <div id="edit_profile" class="col s12  cyan lighten-4">
            <!--start container-->
					  <section id="content">
							  <div class="container">
								  <!--jqueryvalidation-->
								  <div id="jqueryvalidation" class="section">
									<div class="row">
									  <div class="col s12 m12 l12">
										  <div class="col s12 m12 l12">
												<div class="card-panel">
													<h4 class="header2">Edit profile</h4>
													<div class="row">
														{{ Form::open(['id'=>'edit_user_profile','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
															<div class="row">
																<div class="input-field col s6">
																	<label for="first">first name*</label>
																	{{ Form::text('first_name',Auth::user()->first_name, ['class'=>'form-control','id'=>'first_name','autocomplete'=>false]) }}
																			  @if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
																</div>
																<div class="input-field col s6">
																	<label for="last">last name*</label>
																	{{ Form::text('last_name',Auth::user()->last_name, ['class'=>'form-control','id'=>'last_name','autocomplete'=>false]) }}
																			@if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
																</div>

																<div class="input-field col s6">
																  <label for="mail">e-mail *</label>
																  {{ Form::text('email',Auth::user()->email, ['class'=>'form-control','id'=>'email','autocomplete'=>false]) }}
																		  @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
																</div>
																<div class="input-field col s6">
																  <label for="phone">phone no *</label>
																  {{ Form::text('phone',Auth::user()->phone, ['class'=>'form-control','id'=>'phone','autocomplete'=>false]) }}
																			@if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
																</div>

																<div class="input-field col s12">
																  <label for="Image" style="margin-top:-4%">Timezone </label>
																  <select class="browser-default valid" multiple name="timezone" id="Timezoneuser" data-error=".errorTxt6" style="width:500px;" style="margin-top:5%">
																	@foreach ($timezones as $timezone)
																	  <option value="{{ $timezone->timezone_name }}" <?php if($timezone->timezone_name==Auth::user()->timezone){ echo 'selected';}?>>{{ $timezone->timezone_name.' '.$timezone->timezone_value }}</option>
																	@endforeach
																  </select>
																</div>

																<div class="input-field col s6" style="margin-top:5%">
																<label for="Image" style="margin-top:-35px">Image </label>
																  <input name="image" id="image" type="file"/>
																</div>
																<div class="Image-field col s6">

																  <div id="image-holder">
																		@if(Auth::user()->image)
																		<a target="_blank" class="fancybox" href="<?php echo WEBSITE_UPLOADS_URL.'users/'.Auth::user()->image ?>">
																		<img class="img-thumbnail" src="<?php echo WEBSITE_URL.'image.php?height=80px&width=120px&image='.WEBSITE_UPLOADS_URL.'users/'.Auth::user()->image ?>">
																		</a>
																		@else
																		<a class="fancybox" href="<?php echo WEBSITE_IMG_URL ?>no_user_img.png">
																		<img class="img-thumbnail" src="<?php echo WEBSITE_IMG_URL ?>no_user_img.png" height="50px" with="50px">
																		</a>
																		@endif
																	  </div>
																</div>

																<div class="input-field col s12">
																  {{Form::submit('Update profile',['class'=>'btn waves-effect waves-light right green submit','id'=>'update_profile']) }}
																  <a href="{{URL::route('admindashboard')}}" class="btn waves-effect waves-light right red">Back</a>
																</div>
															</div>
														{{ Form::close() }}
													</div>
												</div>
											</div>
									</div>
								  </div>
								  </div>
								  <div class="divider"></div>
								</div>
						</section>
					</div>
          <div id="profile" class="col s12  cyan lighten-4">
            <section id="content">
							  <div class="container">
								  <!--jqueryvalidation-->
								  <div id="jqueryvalidation" class="section">
									<div class="row">
									  <div class="col s12 m12 l12">
										  <div class="col s12 m12 l12">
												<div class="card-panel">
                          <div id="card-alert" class="card green success" style="display:none">
                              <div class="card-content white-text">
                                <p></p>
                              </div>
                              <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                              </button>
                          </div>
                          <div id="card-alert" class="card red error" style="display:none">
                              <div class="card-content white-text">
                                <p></p>
                              </div>
                              <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                              </button>
                          </div>

													<h4 class="header2">Change security pin</h4>
													<div class="row">
														{{ Form::open(['id'=>'change_security_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('admin_change_password')]) }}
															<div class="row">
																<div class="input-field col s6">
																	<label for="uname">Current social security pin*</label>
																	{{ Form::password('old_pin', ['class'=>'form-control','id'=>'old_pin','autocomplete'=>false]) }}
																	@if ($errors->has('old_pin')) <p class="help-block">{{ $errors->first('old_pin') }}</p> @endif
																</div>
																<div class="input-field col s6">
																	<label for="uname">New social security pin*</label>
																	{{ Form::password('new_pin', ['class'=>'form-control','id'=>'new_pin','autocomplete'=>false]) }}
																	@if ($errors->has('new_pin')) <p class="help-block">{{ $errors->first('new_pin') }}</p> @endif
																</div>
																<div class="input-field col s6">
																  <label for="cemail">Confirm social security pin*</label>
																  {{ Form::password('confirm_pin', ['class'=>'form-control','id'=>'confirm_pin','autocomplete'=>false]) }}
																  @if ($errors->has('confirm_pin')) <p class="help-block">{{ $errors->first('confirm_pin') }}</p> @endif
																</div>
																<div class="input-field col s12">
																  {{Form::submit('Change pin',['class'=>'btn waves-effect waves-light right green submit','id'=>'change_pin_button']) }}
																</div>
															</div>
														{{ Form::close() }}
													</div>
												</div>
											</div>
									</div>
								  </div>
								  </div>
								  <div class="divider"></div>
								</div>
						</section>
            </div>
          <div id="change_password" class="col s12  cyan lighten-4">
                      <!--start container-->
					  <section id="content">
							  <div class="container">
								  <!--jqueryvalidation-->
								  <div id="jqueryvalidation" class="section">
									<div class="row">
									  <div class="col s12 m12 l12">
										  <div class="col s12 m12 l12">
												<div class="card-panel">
                          <div id="card-alert" class="card green success" style="display:none">
                              <div class="card-content white-text">
                                <p></p>
                              </div>
                              <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                              </button>
                          </div>
                          <div id="card-alert" class="card red error" style="display:none">
                              <div class="card-content white-text">
                                <p></p>
                              </div>
                              <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span>
                              </button>
                          </div>

													<h4 class="header2">Change password</h4>
													<div class="row">
														{{ Form::open(['id'=>'change_password_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('admin_change_password')]) }}
															<div class="row">
																<div class="input-field col s6">
																	<label for="uname">Current password*</label>
																	{{ Form::password('old_password', ['class'=>'form-control','id'=>'old_password','autocomplete'=>false]) }}
																	@if ($errors->has('old_password')) <p class="help-block">{{ $errors->first('old_password') }}</p> @endif
																</div>
																<div class="input-field col s6">
																	<label for="uname">New password*</label>
																	{{ Form::password('new_password', ['class'=>'form-control','id'=>'new_password','autocomplete'=>false]) }}
																	@if ($errors->has('new_password')) <p class="help-block">{{ $errors->first('new_password') }}</p> @endif
																</div>
																<div class="input-field col s6">
																  <label for="cemail">Confirm password*</label>
																  {{ Form::password('confirm_password', ['class'=>'form-control','id'=>'confirm_password','autocomplete'=>false]) }}
																  @if ($errors->has('confirm_password')) <p class="help-block">{{ $errors->first('confirm_password') }}</p> @endif
																</div>
																<div class="input-field col s12">
																  {{Form::submit('Change password',['class'=>'btn waves-effect waves-light right green submit','id'=>'change_password_button']) }}
																</div>
															</div>
														{{ Form::close() }}
													</div>
												</div>
											</div>
									</div>
								  </div>
								  </div>
								  <div class="divider"></div>
								</div>
						</section>
					</div>
					<div id="notification_settings" class="col s12  cyan lighten-4">
						<section id="content">
										  <div class="container">
											  <!--jqueryvalidation-->
											  <div id="jqueryvalidation" class="section">
												<div class="row">
												  <div class="col s12 m12 l12">
													  <div class="col s12 m12 l12">
															<div class="card-panel">
									  <div id="card-alert" class="card green success" style="display:none">
										  <div class="card-content white-text">
											<p></p>
										  </div>
										  <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">×</span>
										  </button>
									  </div>
									  <div id="card-alert" class="card red error" style="display:none">
										  <div class="card-content white-text">
											<p></p>
										  </div>
										  <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
											<span aria-hidden="true">×</span>
										  </button>
									  </div>

									<h4 class="header2" align="center">Notification settings</h4>
									<div class="row">
										{{ Form::open(['id'=>'notification_settings','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('admin_notification_setting')]) }}
											<div class="row">
												<div class="input-field col s12"><h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;">Notify Settings</h5>
												</div>
											</div>
											<div class="row">
												<div class="input-field col s6">
													<p>
														<input type="radio" id="email_only" class="filled-in" name="notify_only" value="1" @if(Auth::user()->notify_only == 1) {{ "checked" }} @endif>
														<label for="email_only">Email only</label>
													</p>
												</div>
												<div class="input-field col s6">
													<p>
														<input type="radio" id="push_only" class="filled-in" name="notify_only" value="2" @if(Auth::user()->notify_only == 2) {{ "checked" }} @endif >
														<label for="push_only">Push Notifications Only</label>
													</p>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="row">
												<div class="input-field col s6">
													<p>
														<input type="radio" id="email_and_push" class="filled-in" name="notify_only" value="3" @if(Auth::user()->notify_only == 3) {{ "checked" }} @endif >
														<label for="email_and_push">Email & Push Notifications Both</label>
													</p>
												</div>
												<div class="input-field col s6">
													<p>
														<input type="radio" id="no_notification" class="filled-in" name="notify_only" value="4" @if(Auth::user()->notify_only == 4) {{ "checked" }} @endif >
														<label for="no_notification">No Notifications</label>
													</p>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="row" style="margin-top:15px">
												<div class="input-field col s12"><h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;">Clinic notifications</h5>
												</div>
											</div>
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="unfilled_notify" name="field1" @if(Auth::user()->unfilled_notify != 'off') {{ "checked" }} @endif >
														<label for="unfilled_notify">Clinic has gone x unfilled ( hours )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="unfilled_notify_div" style="@if(Auth::user()->unfilled_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													  <input name="unfilled_notify" type="text" value="@if(Auth::user()->unfilled_notify != 'off'){{ Auth::user()->unfilled_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="x_time_unfilled_notify" name="field2" @if(Auth::user()->x_time_unfilled_notify != 'off') {{ "checked" }} @endif >
														<label for="x_time_unfilled_notify">Clinic is in X time and unfilled ( hours )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="x_time_unfilled_notify_div" style="@if(Auth::user()->x_time_unfilled_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													 <input name="x_time_unfilled_notify" type="text" value="@if(Auth::user()->x_time_unfilled_notify != 'off'){{ Auth::user()->x_time_unfilled_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row">
												<div class="input-field col s4">
													<p>
														<input type="checkbox" class="filled-in" id="clinic_filled_notify" name="field3" @if(Auth::user()->clinic_filled_notify != 'off') {{ "checked" }} @endif >
														<label for="clinic_filled_notify">Clinic has been filled*</label>
													</p>
												</div>
												<div class="input-field col s4">
													<p>
														<input type="checkbox" class="filled-in" id="mileage_info_notify" name="field4" @if(Auth::user()->mileage_info_notify != 'off') {{ "checked" }} @endif >
														<label for="mileage_info_notify">Clinic status is pending mileage info*</label>
													</p>
												</div>
												<div class="input-field col s4">
													<p>
														<input type="checkbox" class="filled-in" id="clinic_status_notify" name="field5" @if(Auth::user()->clinic_status_notify != 'off') {{ "checked" }} @endif >
														<label for="clinic_status_notify">Clinic status is complete*</label>
													</p>
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row" style="margin-top:3%">
												<div class="input-field col s12"><h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;">Admins notifications</h5>
												</div>
											</div>
											<div class="row">
												<div class="input-field col s4">
													<p>
														<input type="checkbox" class="filled-in" id="user_added_notify" name="field6" @if(Auth::user()->user_added_notify != 'off') {{ "checked" }} @endif >
														<label for="user_added_notify">Addition of new user*</label>
													</p>
												</div>
											</div>
											
											<div class="row" style="margin-top:3%">
												<div class="input-field col s12"><h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;">Admin notifications</h5>
												</div>
											</div>
											<div class="row">
												<div class="input-field col s4">
													<p>
														<input type="checkbox" class="filled-in" id="admin_added_notify" name="field7" @if(Auth::user()->admin_added_notify != 'off') {{ "checked" }} @endif >
														<label for="admin_added_notify">Addition of new admin*</label>
													</p>
												</div>
												<div class="clearfix"></div>
											</div>
											
											<div class="row" style="margin-top:3%">
												<div class="input-field col s12"><h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;">Certifications doc notifications</h5>
												</div>
											</div>
											<div class="row">
												<div class="input-field col s4">
													<p>
														<input type="checkbox" class="filled-in" id="certifications_notify" name="field8" @if(Auth::user()->certifications_notify != 'off') {{ "checked" }} @endif >
														<label for="certifications_notify">submission of new certifications doc*</label>
													</p>
												</div>
												<div class="clearfix"></div>
											</div>
											
											<div class="row" style="margin-top:3%">
												<div class="input-field col s12"><h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;">Time Card notifications</h5>
												</div>
											</div>
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="over_hour_month_notify" name="field9" @if(Auth::user()->over_hour_month_notify != 'off') {{ "checked" }} @endif >
														<label for="over_hour_month_notify">If provider over x hours in a month ( hours )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="over_hour_month_notify_div" style="@if(Auth::user()->over_hour_month_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													 <input name="over_hour_month_notify" type="text" value="@if(Auth::user()->over_hour_month_notify != 'off'){{ Auth::user()->over_hour_month_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="over_hour_day_notify" name="field10" @if(Auth::user()->over_hour_day_notify != 'off') {{ "checked" }} @endif >
														<label for="over_hour_day_notify">If provider go over x hours in a day ( hours )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="over_hour_day_notify_div" style="@if(Auth::user()->over_hour_day_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													 <input name="over_hour_day_notify" type="text" value="@if(Auth::user()->over_hour_day_notify != 'off'){{ Auth::user()->over_hour_day_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="over_hour_clinic_notify" name="field11" @if(Auth::user()->over_hour_clinic_notify != 'off') {{ "checked" }} @endif >
														<label for="over_hour_clinic_notify">If provider go over x hours in a Clinic ( hours )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="over_hour_clinic_notify_div" style="@if(Auth::user()->over_hour_clinic_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													 <input name="over_hour_clinic_notify" type="text" value="@if(Auth::user()->over_hour_clinic_notify != 'off'){{ Auth::user()->over_hour_clinic_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="over_mileage_month_notify" name="field12" @if(Auth::user()->over_mileage_month_notify != 'off') {{ "checked" }} @endif >
														<label for="over_mileage_month_notify">If provider goes over x mileage in a month ( miles )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="over_mileage_month_notify_div" style="@if(Auth::user()->over_mileage_month_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													 <input name="over_mileage_month_notify" type="text" value="@if(Auth::user()->over_mileage_month_notify != 'off'){{ Auth::user()->over_mileage_month_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="over_mileage_day_notify" name="field13" @if(Auth::user()->over_mileage_day_notify != 'off') {{ "checked" }} @endif >
														<label for="over_mileage_day_notify">If provider goes over x mileage in a day ( miles )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="over_mileage_day_notify_div" style="@if(Auth::user()->over_mileage_day_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													 <input name="over_mileage_day_notify" type="text" value="@if(Auth::user()->over_mileage_day_notify != 'off'){{ Auth::user()->over_mileage_day_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
											<div class="row">
												<div class="input-field col s5">
													<p>
														<input type="checkbox" class="filled-in" id="over_mileage_clinic_notify" name="field14" @if(Auth::user()->over_mileage_clinic_notify != 'off') {{ "checked" }} @endif >
														<label for="over_mileage_clinic_notify">If provider goes over x mileage in a clinic ( miles )*</label>
													</p>
												</div>
												<div class="input-field col s7" id="over_mileage_clinic_notify_div" style="@if(Auth::user()->over_mileage_clinic_notify != 'off'){{ 'display:block' }} @else {{ 'display:none' }}@endif">
													 <input name="over_mileage_clinic_notify" type="text" value="@if(Auth::user()->over_mileage_clinic_notify != 'off'){{ Auth::user()->over_mileage_clinic_notify }}@endif">
												</div>
											</div>
											<div class="clearfix"></div>
											
												
											<div class="input-field col s12">
											  {{Form::submit('Update settings',['class'=>'btn waves-effect waves-light right green submit','id'=>'update_settings']) }}
											</div>
											
										{{ Form::close() }}
													</div>
												</div>
											</div>
										</div>
									  </div>
									  </div>
									  <div class="divider"></div>
									</div>
							</section>
						</div>
					</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
    </section>
	<script type="text/javascript">
        $(function () {
            $("#unfilled_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#unfilled_notify_div").show();
                } else {
                    $("#unfilled_notify_div").hide();
                }
            });
			$("#x_time_unfilled_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#x_time_unfilled_notify_div").show();
                } else {
                    $("#x_time_unfilled_notify_div").hide();
                }
            });
			$("#over_hour_month_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#over_hour_month_notify_div").show();
                } else {
                    $("#over_hour_month_notify_div").hide();
                }
            });
			$("#over_hour_day_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#over_hour_day_notify_div").show();
                } else {
                    $("#over_hour_day_notify_div").hide();
                }
            });
			$("#over_hour_clinic_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#over_hour_clinic_notify_div").show();
                } else {
                    $("#over_hour_clinic_notify_div").hide();
                }
            });
			$("#over_mileage_month_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#over_mileage_month_notify_div").show();
                } else {
                    $("#over_mileage_month_notify_div").hide();
                }
            });
			$("#over_mileage_day_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#over_mileage_day_notify_div").show();
                } else {
                    $("#over_mileage_day_notify_div").hide();
                }
            });
			$("#over_mileage_clinic_notify").click(function () {
                if ($(this).is(":checked")) {
                    $("#over_mileage_clinic_notify_div").show();
                } else {
                    $("#over_mileage_clinic_notify_div").hide();
                }
            });
        });
    </script>
@stop
