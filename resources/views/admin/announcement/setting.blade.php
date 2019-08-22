@extends('admin.layouts.default_layout')
@section('content')
@section('title','announcement setting')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Announcement Settings') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('announcement')}}">{{ trans('Announcements') }}</a></li>
                  <li class="active">{{ trans('Announcement settings') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
          <!--jqueryvalidation-->
          <div id="jqueryvalidation" class="section">
            <div class="row">
              <div class="col s12 m12 l12">
                  <div class="col s12 m12 l10">
                        <div class="card-panel">
                            <div class="row">
                                {{ Form::open(['id'=>'announcement_setting','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('announcement-setting',$announcement->id)]) }}
                                    <div class="row">
									<?php
                                      $user_id = Auth::user()->id;
                                      $user_time_zone 			= DB::table('users')->select('timezone')->where('id',Auth::user()->id)->get();
                                      $user_time_zone_value = $user_time_zone[0]->timezone;

                                      $clinic_date_time = new DateTime($announcement->created_at, new DateTimeZone('GMT'));
                                      $clinic_date_time->setTimezone(new DateTimeZone($user_time_zone_value));
                                      $date_time 		= $clinic_date_time->format('Y-m-d H:i');
									  $time         = $clinic_date_time->format('H:i');
                                      $date         = $clinic_date_time->format('Y-m-d');
									  $last_date    = date('d F, Y ',strtotime($date));
                                      ?>
									  <div class="input-field col s12">
										<label for="date">Start from</label>
									   {{ Form::text('date',$last_date, ['class'=>'datepicker','id'=>'date','autocomplete'=>false]) }}
										@if ($errors->has('date')) <p class="help-block">{{ $errors->first('date') }}</p> @endif
                                        </div>
                                      <div class="input-field col s12">
                                          <label for="providers" style="margin-top:-3%">Select to limit announcement to providers</label><br>
                                          <?php $result_array = explode(',', $announcement->visible_providers);?>
                                            <select class="browser-default" name="visible_providers[]" multiple="multiple" style="width:100%" id="providers">
                                              @foreach ($providers as $provider)
                                                <option value="{{ $provider->id }}" <?php if(in_array($provider->id,$result_array)){ echo 'selected class="active selected"';}?>>{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                              @endforeach
                                            </select>
                                      </div>
									  <div class="input-field col s12" style="margin-top:3%">
                                              <label for="cities" style="margin-top:-3%">Select to limit announcement to cities</label><br>
											  <?php $visible_cities = explode(',', $announcement->visible_cities);
											  ?>
                                                <select class="browser-default" name="visible_cities[]" multiple="multiple" style="width:100%" id="cities">
                                                  @foreach ($cities as $city)
                                                    <option value="{{ $city->city_name }}" <?php if (in_array($city->city_name, $visible_cities)) {  echo "selected";} ?>>{{ $city->city_name }}</option>
                                                  @endforeach
                                                </select>
                                          </div>
									  </div>
									  <div class="row">
                                        <div class="input-field col s12" style="margin-top:5%">
                                          <select  class="browser-default" name="notification_alert" multiple="multiple" style="width:100%" id="notification_alert">
                                            <option value="1" @if($announcement->notification_alert=='1') {{ 'selected' }} @endif>Instant</option>
                                            <option value="2" @if($announcement->notification_alert=='2') {{ 'selected' }} @endif>Provider’s app settings</option>
                                          </select>
                                          <label for="notification_alert" style="margin-top:-5%">Push notification</label><br>
                                      </div>
                                    <div class="input-field col s12" style="margin-top:5%">
                                        <select   class="browser-default" name="email_alert" multiple="multiple" style="width:100%" id="email_alert">
                                          <option value="1" @if($announcement->email_alert=='1') {{ 'selected' }} @endif>Everyone</option>
                                          <option value="2" @if($announcement->email_alert=='2') {{ 'selected' }} @endif>Provider’s app settings</option>
                                        </select>
                                          <label for="email_alert" style="margin-top:-5%">Email notifications</label><br>
                                    </div>

                                    <div class="input-field col s12">
                                        <label for="stable_time">Time to stay in feeds (Days example: 2)</label>
                                        {{ Form::text('stable_time',$announcement->stable_time/24, ['class'=>'form-control','id'=>'stable_time','autocomplete'=>false]) }}
                                        @if ($errors->has('stable_time')) <p class="help-block">{{ $errors->first('stable_time') }}</p> @endif
                                    </div>

                                        <div class="input-field col s12">
                                          {{Form::submit('Update settings',['class'=>'btn waves-effect waves-light right green submit']) }}
                                          <a href="{{URL::route('announcement')}}" class="btn waves-effect waves-light right red">Back</a>
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
  <!-- /.content-wrapper -->
@stop
