@extends('admin.layouts.default_layout')
@section('content')
@section('title','settings')
<?php
use \App\Http\Controllers\BaseController;
$provider_id	=	Request::segment(3);
?>
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Settings') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Settings') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <?php // prd($admin_settings);?>

      <!--start container-->
      <div class="container">
        <div id="jqueryvalidation" class="section">
          <div class="row">
            <div class="col s12 m12 l12">
                <div class="col s12 m12 l12">
                      <div class="card-panel">
                          <div class="row">
                              {{
                               Form::open(['id'=>'editsetting_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('settings')]) }}
								<div class="row" style="margin:0% 5px 0% 5px">
									<div class="input-field col s12">
										<h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;color:#18497a">General Settings
										</h5>
									</div>
								</div>
                                  <div class="row" style="margin:0% 5px 0% 5px">
                                    <div class="input-field col s6">
                                        <label for="copyright_text">Copyright texts*</label>
                                        {{ Form::text('copyright_text',$admin_settings->copyright_text, ['class'=>'form-control','id'=>'copyright_text','autocomplete'=>false]) }}
                                        @if ($errors->has('copyright_text')) <p class="help-block">{{ $errors->first('copyright_text') }}</p> @endif
                                    </div>
                                    <!-- <div class="input-field col s6">
                                        <label for="default_hours">Default hours for providers (hours)</label>
                                        {{ Form::text('default_hours',$admin_settings->default_hours, ['class'=>'form-control','id'=>'default_hours','autocomplete'=>false]) }}
                                        @if ($errors->has('default_hours')) <p class="help-block">{{ $errors->first('default_hours') }}</p> @endif
                                    </div> -->

                                    <div class="input-field col s6">
                                      <label for="google_map_api">Google map api key *</label>
                                      {{ Form::text('google_map_api',$admin_settings->google_map_api, ['class'=>'form-control','id'=>'google_map_api','autocomplete'=>false]) }}
                                      @if ($errors->has('google_map_api')) <p class="help-block">{{ $errors->first('google_map_api') }}</p> @endif
                                    </div>
								</div>
								<div class="row" style="margin-top:1%;margin-bottom:2%;padding-left:1%">
									<div class="input-field col s12">
										<h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;color:#18497a">Clinics settings
										</h5>
									</div>
								</div>
									   
								<div class="input-field col s6">
									<label for="default_time_stay_in_feeds">Default time unfilled before clinic opening goes to home feed (hours)</label>
									{{ Form::text('default_time_stay_in_feeds',$admin_settings->default_time_stay_in_feeds, ['class'=>'form-control','id'=>'default_time_stay_in_feeds','autocomplete'=>false]) }}
									@if ($errors->has('default_time_stay_in_feeds')) <p class="help-block">{{ $errors->first('default_time_stay_in_feeds') }}</p> @endif
								</div>

							  <div class="input-field col s3">
								<label for="default_prep_time">Default prep time (minutes)</label>
								{{ Form::text('default_prep_time',$admin_settings->default_prep_time, ['class'=>'form-control','id'=>'default_prep_time','autocomplete'=>false]) }}
								@if ($errors->has('default_prep_time')) <p class="help-block">{{ $errors->first('default_prep_time') }}</p> @endif
							  </div>
							  <div class="input-field col s3">
								<label for="max_distance">Set max distance (miles)</label>
								{{ Form::text('max_distance',$admin_settings->max_distance, ['class'=>'form-control','id'=>'max_distance','autocomplete'=>false]) }}
								@if ($errors->has('max_distance')) <p class="help-block">{{ $errors->first('max_distance') }}</p> @endif
							  </div>
							  <div class="input-field col s4">
								<label for="preferred_wait_time">Preferred wait time (hours)</label>
								{{ Form::text('preferred_wait_time',$admin_settings->preferred_wait_time, ['class'=>'form-control','id'=>'preferred_wait_time','autocomplete'=>false]) }}
								@if ($errors->has('preferred_wait_time')) <p class="help-block">{{ $errors->first('preferred_wait_time') }}</p> @endif
							  </div>
							  
							  <div class="input-field col s4">
								<label for="google_map_api">Allow clock in before prep time (In minutes ex : 5)*</label>
								{{ Form::text('allow_clockin_before_preptime',$admin_settings->allow_clockin_before_preptime, ['class'=>'form-control','id'=>'allow_clockin_before_preptime','autocomplete'=>false]) }}
								@if ($errors->has('allow_clockin_before_preptime')) <p class="help-block">{{ $errors->first('allow_clockin_before_preptime') }}</p> @endif
							  </div>

							  <div class="input-field col s4">
								<label for="clockin_margin_time">Margin time for accept clinics (In minutes ex : 5)*</label>
								{{ Form::text('accept_margin_time',$admin_settings->accept_margin_time, ['class'=>'form-control','id'=>'accept_margin_time','autocomplete'=>false]) }}
								@if ($errors->has('accept_margin_time')) <p class="help-block">{{ $errors->first('accept_margin_time') }}</p> @endif
							  </div>
							  <!--<div class="input-field col s6">
								<select name="default_auto_discard_time" id="auto_discard">
								  <option value="" selected disabled>Select option</option>
								  <option value="1" <?php if($admin_settings->default_auto_discard_time=='1'){ echo 'selected'; } ?>>Yes</option>
								  <option value="0" <?php if($admin_settings->default_auto_discard_time=='0'){ echo 'selected'; } ?>>No</option>
								</select>
								<label for="auto_discard">Default auto discard time since at location</label>
								@if ($errors->has('default_auto_discard_time')) <p class="help-block">{{ $errors->first('default_auto_discard_time') }}</p> @endif
							  </div>-->
							  
							  <div class="row" style="margin-top:1%;margin-bottom:2%;padding-left:1%">
									<div class="input-field col s12">
										<h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;color:#18497a">Announcement settings
										</h5>
									</div>
								</div>
									  
							  
							  <div class="input-field col s6">
								<label for="default_announcemnet_stay_feeds">Default time announcements stay in news feed all announcement for (days)</label>
								{{ Form::text('default_announcemnet_stay_feeds',$admin_settings->default_announcemnet_stay_feeds, ['class'=>'form-control','id'=>'default_announcemnet_stay_feeds','autocomplete'=>false]) }}
								@if ($errors->has('default_announcemnet_stay_feeds')) <p class="help-block">{{ $errors->first('default_announcemnet_stay_feeds') }}</p> @endif
							  </div>
							  
							  <div class="row" style="margin-top:1%;margin-bottom:2%;padding-left:1%">
									<div class="input-field col s12">
										<h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;color:#18497a">Notification settings
										</h5>
									</div>
								</div>

							  <div class="input-field col s4">
							  <label for="clock_in_default_time">Reminder to clock in (minutes)</label>
							  {{ Form::text('clock_in_default_time',$admin_settings->clock_in_default_time, ['class'=>'form-control','id'=>'clock_in_default_time','autocomplete'=>false]) }}
							  @if ($errors->has('clock_in_default_time')) <p class="help-block">{{ $errors->first('clock_in_default_time') }}</p> @endif
							</div>
							  <div class="input-field col s4">
								<label for="default_time_clockout">Time to notify user to clock out (minutes)</label>
								{{ Form::text('default_time_clockout',$admin_settings->default_time_clockout, ['class'=>'form-control','id'=>'default_time_clockout','autocomplete'=>false]) }}
								@if ($errors->has('default_time_clockout')) <p class="help-block">{{ $errors->first('default_time_clockout') }}</p> @endif
							  </div>
							  <div class="input-field col s4">
								<label for="default_miles_clockout">Distance to notify user to clock out (miles)</label>
								{{ Form::text('default_miles_clockout',$admin_settings->default_miles_clockout, ['class'=>'form-control','id'=>'default_miles_clockout','autocomplete'=>false]) }}
								@if ($errors->has('default_miles_clockout')) <p class="help-block">{{ $errors->first('default_miles_clockout') }}</p> @endif
							  </div>
							  <div class="input-field col s4">
								<label for="notify_clockout_time_admin">Default time to notify admin for clock out (minutes)</label>
								{{ Form::text('notify_clockout_time_admin',$admin_settings->notify_clockout_time_admin, ['class'=>'form-control','id'=>'notify_clockout_time_admin','autocomplete'=>false]) }}
								@if ($errors->has('notify_clockout_time_admin')) <p class="help-block">{{ $errors->first('notify_clockout_time_admin') }}</p> @endif
							  </div>
							  <div class="input-field col s4">
								<label for="notify_clockout_mile_admin">Default distance to notify admin for clock out (miles)</label>
								{{ Form::text('notify_clockout_mile_admin',$admin_settings->notify_clockout_mile_admin, ['class'=>'form-control','id'=>'notify_clockout_mile_admin','autocomplete'=>false]) }}
								@if ($errors->has('notify_clockout_mile_admin')) <p class="help-block">{{ $errors->first('notify_clockout_mile_admin') }}</p> @endif
							  </div>
                                      
							  

							  <div class="input-field col s4">
								<label for="unfilled_before_time">Unfilled before notification time (In hours ex : 2)*</label>
								{{ Form::text('unfilled_before_time',$admin_settings->unfilled_before_time, ['class'=>'form-control','id'=>'unfilled_before_time','autocomplete'=>false]) }}
								@if ($errors->has('unfilled_before_time')) <p class="help-block">{{ $errors->first('unfilled_before_time') }}</p> @endif
							  </div> 

							   <div class="input-field col s4">
								<label for="default_max_scheduled_hours">Default max scheduled per month (In hours Ex : 40)</label>
								{{ Form::text('default_max_scheduled_hours',$admin_settings->default_max_scheduled_hours, ['class'=>'form-control','id'=>'default_max_scheduled_hours','autocomplete'=>false]) }}
								@if ($errors->has('default_max_scheduled_hours')) <p class="help-block">{{ $errors->first('default_max_scheduled_hours') }}</p> @endif
							  </div>

							  <div class="input-field col s4">
								<label for="default_max_scheduled_per_day">Default max scheduled per day (In hours ex : 5)*</label>
								{{ Form::text('default_max_scheduled_per_day',$admin_settings->default_max_scheduled_per_day, ['class'=>'form-control','id'=>'default_max_scheduled_per_day','autocomplete'=>false]) }}
								@if ($errors->has('default_max_scheduled_per_day')) <p class="help-block">{{ $errors->first('default_max_scheduled_per_day') }}</p> @endif
							  </div>

							  <div class="input-field col s4">
								<label for="default_max_scheduled_per_clinic">Default max scheduled per clinic (In minuts ex : 30)*</label>
								{{ Form::text('default_max_scheduled_per_clinic',$admin_settings->default_max_scheduled_per_clinic, ['class'=>'form-control','id'=>'default_max_scheduled_per_clinic','autocomplete'=>false]) }}
								@if ($errors->has('default_max_scheduled_per_clinic')) <p class="help-block">{{ $errors->first('default_max_scheduled_per_clinic') }}</p> @endif
							  </div>

							  <div class="input-field col s4">
								<label for="default_max_mileage_per_month">Default max mileage per month (Ex : 20)*</label>
								{{ Form::text('default_max_mileage_per_month',$admin_settings->default_max_mileage_per_month, ['class'=>'form-control','id'=>'default_max_mileage_per_month','autocomplete'=>false]) }}
								@if ($errors->has('default_max_mileage_per_month')) <p class="help-block">{{ $errors->first('default_max_mileage_per_month') }}</p> @endif
							  </div>

							  <div class="input-field col s4">
								<label for="default_max_mileage_per_day">Default max mileage per day (Ex : 10)*</label>
								{{ Form::text('default_max_mileage_per_day',$admin_settings->default_max_mileage_per_day, ['class'=>'form-control','id'=>'default_max_mileage_per_day','autocomplete'=>false]) }}
								@if ($errors->has('default_max_mileage_per_day')) <p class="help-block">{{ $errors->first('default_max_mileage_per_day') }}</p> @endif
							  </div>

							  <div class="input-field col s4">
								<label for="default_max_mileage_per_clinic">Default max mileage per clinic (Ex : 5)*</label>
								{{ Form::text('default_max_mileage_per_clinic',$admin_settings->default_max_mileage_per_clinic, ['class'=>'form-control','id'=>'default_max_mileage_per_clinic','autocomplete'=>false]) }}
								@if ($errors->has('default_max_mileage_per_clinic')) <p class="help-block">{{ $errors->first('default_max_mileage_per_clinic') }}</p> @endif
							  </div>
							  
							  <div class="row" style="margin-top:1%;margin-bottom:2%;padding-left:1%">
									<div class="input-field col s12">
										<h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;color:#18497a">Pay period settings
										</h5>
									</div>
								</div>
									  <div class="clearfix"></div>
									  <label for="pay_period" style="padding-left:2%">Pay period</label>
									  <div class="clearfix"></div>
									  <div class="input-field col s4">
									  <select class="browser-default has_other" name="pay_period" data-other="#textbox_id" data-other-text="Custom">
											<option selected disabled>Pay period</option>
											<option value="1-15" @if($admin_settings->pay_period == '1-15') {{ trans('selected') }} @endif>1-15</option>
											<option value="custom" @if($admin_settings->pay_period == 'custom') {{ trans('selected') }} @endif >Custom</option>
										</select>
										
									  </div>
									  <div id="textbox_id" @if($admin_settings->pay_period == 'custom') {{ trans('style="display:block"') }} @endif>
										  <div class="input-field col s4" >
											<select class="browser-default" name="payperiod_start" data-other="#textbox_id" data-other-text="Custom">
											<?php for($i=1;$i<=30;$i++){?>
											<option value="<?php echo $i?>" <?php if(isset($admin_settings->pay_period_start)){
												if($admin_settings->pay_period_start == $i){ echo 'selected';}
											} ?>><?php echo $i?></option>
											<?php } ?>
											</select>
										  </div>
										  <div class="input-field col s4">
											<input type="text" id="textbox_id2" value="<?php if(isset($admin_settings->pay_period_days)) { echo $admin_settings->pay_period_days; } else { echo '14';}?>" name="payperiod_days" placeholder="No of days" />
										  </div>
									  </div>
									  								<div class="row" style="margin:0% 5px 0% 5px">
									<div class="input-field col s12">
										<h5  style="border-bottom:1px dotted #666;padding-bottom:1%;font-size: 1.20rem;color:#18497a">Distance maintain settings for providers
										</h5>
									</div>
								</div>
                                  <div class="row" style="margin:0% 5px 0% 5px">
                                    <div class="input-field col s6">
                                        <label for="green_miles_start">Green area starting miles from*</label>
                                        {{ Form::text('green_miles_start',$admin_settings->green_miles_start, ['class'=>'form-control','id'=>'green_miles_start','autocomplete'=>false,'required'=>'required']) }}
                                        @if ($errors->has('green_miles_start')) <p class="help-block">{{ $errors->first('green_miles_start') }}</p> @endif
                                    </div>

                                    <div class="input-field col s6">
                                      <label for="green_miles_end">Green area starting miles end *</label>
                                      {{ Form::text('green_miles_end',$admin_settings->green_miles_end, ['class'=>'form-control','id'=>'green_miles_end','autocomplete'=>false,'required'=>'required']) }}
                                      @if ($errors->has('green_miles_end')) <p class="help-block">{{ $errors->first('green_miles_end') }}</p> @endif
                                    </div>
                                    <div class="input-field col s6">
                                        <label for="yellow_miles_start">Yellow area starting miles from*</label>
                                        {{ Form::text('yellow_miles_start',$admin_settings->yellow_miles_start, ['class'=>'form-control','id'=>'yellow_miles_start','autocomplete'=>false,'required'=>'required']) }}
                                        @if ($errors->has('yellow_miles_start')) <p class="help-block">{{ $errors->first('yellow_miles_start') }}</p> @endif
                                    </div>

                                    <div class="input-field col s6">
                                      <label for="yellow_miles_end">Yellow area starting miles end *</label>
                                      {{ Form::text('yellow_miles_end',$admin_settings->yellow_miles_end, ['class'=>'form-control','id'=>'yellow_miles_end','autocomplete'=>false,'required'=>'required']) }}
                                      @if ($errors->has('yellow_miles_end')) <p class="help-block">{{ $errors->first('yellow_miles_end') }}</p> @endif
                                    </div>
                                    <div class="input-field col s6">
                                      <label for="red_miles_start">Red area starting miles ( greater than ) *</label>
                                      {{ Form::text('red_miles_start',$admin_settings->red_miles_start, ['class'=>'form-control','id'=>'red_miles_start','autocomplete'=>false,'required'=>'required']) }}
                                      @if ($errors->has('red_miles_start')) <p class="help-block">{{ $errors->first('red_miles_start') }}</p> @endif
                                    </div>
								</div>
                                      <div class="input-field col s12">
                                        {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createprovider']) }}
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
      </div>
      <!--end container-->

    </section>
    <!-- END CONTENT -->
	<script>
		 // handle cases where dropdown's have an "other" option
		  $.each( $("select.has_other").change(function() {
			updateOther(this, true);
		  }), function() { 
			updateOther(this);
		  });

		  function updateOther(select, focus) {
			var $select = $(select);
			var $other = $($select.data("other"));
			var other_text = $select.data("other-text");
			var text = $select.find("option:selected").text();
			if (text == other_text) {
			  $other.show();
			  if (focus) $other.focus();
			} else {
			  $other.val("").hide();
			}
		  }
	</script>
@stop
