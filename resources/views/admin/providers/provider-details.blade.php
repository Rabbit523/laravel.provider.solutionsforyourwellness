@extends('admin.layouts.default_layout')
@section('content')
@section('title','provider setting')
<?php
		$segment3	=	Request::segment(2);
		$segment4	=	Request::segment(3);
		$segment5	=	Request::segment(4);
	?>
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Providers') }} (  {{ CustomHelper::GetUserData($segment3)->first_name.' '.CustomHelper::GetUserData($segment3)->last_name }} )</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('providers')}}">{{ trans('Providers') }}</a></li>
                  <li class="active">{{ trans('Provider details') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
        <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('edit-provider',$segment3)}}" name="action">Edit Provider
          <i class="mdi-image-edit left"></i>
        </a>
        <a title="delete" data-toggle="tooltip" class="delete_record_btn btn waves-effect waves-light red dark" href="javascript:void(0);" data-url="{{ URL::route('delete-providers',$segment3)}}"> <i class="mdi-action-delete left" aria-hidden="true"></i> Delete Provider</a>
        <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('timesheet_single_view',$segment3)}}" name="action">Timesheet
          <i class="mdi-maps-local-atm left"></i>
        </a>
        <a class="btn waves-effect waves-light blue dark" type="submit" href="{{ URL::route('view-certificates',$segment3)}}" name="action">Certificates
          <i class="mdi-image-remove-red-eye left"></i>
        </a>
        <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('provider-calender',$segment3)}}" name="action">Provider Calender
          <i class="mdi-image-edit left"></i>
        </a>
        <div id="accordion" class="section">
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
          <div class="col s6 m6 l6">
            <ul id="profile-page-about-details" class="collection z-depth-1">
              <li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> Provider status</div>
                  <div class="col s2 grey-text text-darken-4">{{ $provider->status ==  '1' ? 'active' : 'inactive' }}</div>
				  @if($provider->status ==  '1')
					  <div class="col s5 grey-text text-darken-4">
						<a href="{{ URL::route('provider-status-change',$provider['id']) }}">Click to deactivate</a>
					  </div>
				  @else
					  <div class="col s5 grey-text text-darken-4">
						<a href="{{ URL::route('provider-status-change',$provider['id']) }}">Click to activate</a>
					  </div>
				  @endif
                </div>
              </li>
              <li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> Edit provider details</div>
                  <div class="col s7 grey-text text-darken-4 "><a href="{{ URL::route('edit-provider',$provider->id) }}">Edit provider details</a></div>
                </div>
              </li>
              <!--<li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> View or edit mileage and drive time data </div>
                  <div class="col s7 grey-text text-darken-4 ">
                    <ul class="collapsible collapsible-accordion" data-collapsible="accordion">
                  <li>
                    <div class="collapsible-header">Click to view or edit</div>
                    <div class="collapsible-body" style="">
                      <div class="row">
                            <div class="col s12 m12 l12">
                                  <div class="card-panel">

                                          {{ Form::open(['id'=>'edit_provider_calender','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
                                            {{ Form::hidden('provider_id',$provider->id) }}
                                              <div class="row">
                                                <div class="input-field col s6">
                                                    <label for="uname">Mileage info per clinic*</label>
                                                    {{ Form::text('mileage_info',$provider->mileage_info, ['class'=>'form-control','id'=>'mileage_info','autocomplete'=>false]) }}
                                                    @if ($errors->has('mileage_info')) <p class="help-block">{{ $errors->first('mileage_info') }}</p> @endif
                                                </div>
                                                <div class="input-field col s6">
                                                    <label for="uname">Drive time per clinic*</label>
                                                    {{ Form::text('drive_time',$provider->drive_time, ['class'=>'form-control','id'=>'drive_time','autocomplete'=>false]) }}
                                                    @if ($errors->has('drive_time')) <p class="help-block">{{ $errors->first('drive_time') }}</p> @endif
                                                </div>
                                                <div class="input-field col s8">
                                                    <label for="uname">Time card per clinic*</label>
                                                    {{ Form::text('time_card',$provider->time_card, ['class'=>'form-control','id'=>'time_card','autocomplete'=>false]) }}
                                                    @if ($errors->has('time_card')) <p class="help-block">{{ $errors->first('time_card') }}</p> @endif
                                                </div>
                                                <div class="input-field col s4">
                                                      {{Form::submit('Update',['class'=>'btn waves-effect waves-light right green submit']) }}
                                                </div>

                                              </div>
                                          {{ Form::close() }}

                                  </div>
                              </div>
                    </div>
                    </div>
                  </li>
                </ul>
                  </div>
                </div>
              </li>
              <li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> Edit defaults time or distance to remind to clock out </div>
                  <div class="col s7 grey-text text-darken-4 ">
                    <ul class="collapsible collapsible-accordion" data-collapsible="accordion">
                  <li>
                    <div class="collapsible-header">Click to view or edit</div>
                    <div class="collapsible-body" style="">
                      <div class="row">
                            <div class="col s12 m12 l12">
                                  <div class="card-panel">
                                          {{ Form::open(['id'=>'edit_provider_clockout','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
                                            {{ Form::hidden('provider_id',$provider->id) }}
                                              <div class="row">
                                                <div class="input-field col s12">
                                                    <label for="uname">Time to clockout - minutes( example 5 )*</label>
                                                    {{ Form::text('clockout_time',$provider->clockout_time, ['class'=>'form-control','id'=>'clockout_time','autocomplete'=>false]) }}
                                                    @if ($errors->has('clockout_time')) <p class="help-block">{{ $errors->first('clockout_time') }}</p> @endif
                                                </div>
                                                <div class="input-field col s12">
                                                    <label for="uname">Distance to alert - miles( example 1 )*</label>
                                                    {{ Form::text('clockout_distance',$provider->clockout_distance, ['class'=>'form-control','id'=>'clockout_distance','autocomplete'=>false]) }}
                                                    @if ($errors->has('clockout_distance')) <p class="help-block">{{ $errors->first('clockout_distance') }}</p> @endif
                                                </div>

                                                <div class="input-field col s12">
                                                      {{Form::submit('Update',['class'=>'btn waves-effect waves-light right green submit']) }}
                                                </div>

                                              </div>
                                          {{ Form::close() }}
                                  </div>
                              </div>
                    </div>
                    </div>
                  </li>
                  </ul>
                  </div>
                </div>
              </li>-->
              <li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> Provider certificates</div>
                  <div class="col s7 grey-text text-darken-4 "><a href="{{URL::route('view-certificates', $provider->id)}}">View certificates</a></div>
                </div>
              </li>
              <li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> Provider's full calender</div>
                  <div class="col s7 grey-text text-darken-4 "><a href="{{ URL::route('provider-calender',$provider->id) }}">View calender</a></div>
                </div>
              </li>
              <li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> Providers time sheet details / finance</div>
                  <div class="col s7 grey-text text-darken-4 ">
                    <a href="{{URL::route('timesheet_single_view', $provider->id)}}">View report </a>
                    <!-- <a href="{{URL::route('orderexcel', $provider->id)}}">Export CSV</a> -->
                  </div>
                </div>
              </li>
              <li class="collection-item">
                <div class="row">
                  <div class="col s5 grey-text darken-1"> Last four ss#</div>
                  <div class="col s7 grey-text text-darken-4"><star class="maskable">****</star> <a id="show_security_pin" class="modal-trigger" href="#security_pin_model">View </a></div>
                </div>
              </li>


            </ul>
              </div>
          </div>
        </div>
      <!--end container-->
    </section>
    <div id="security_pin_model" class="modal" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 203.057px;">
        <div class="modal-content">
          {{ Form::open(['id'=>'view_security_pin','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
            {{ Form::hidden('provider_id',$provider->id) }}
              <div class="row">
                <div class="input-field col s12">
                    <label for="uname">Enter password to view social security pin*</label>
                    {{ Form::password('password',null, ['class'=>'form-control','id'=>'password','autocomplete'=>false]) }}
                    @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                </div>
                <div class="input-field col s12">
                      {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit']) }}
                </div>

              </div>
          {{ Form::close() }}
        </div>

    </div>
    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
@stop
