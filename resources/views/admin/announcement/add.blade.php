@extends('admin.layouts.default_layout')
@section('content')
@section('title','add announcement')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Announcements') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('announcement')}}">{{ trans('Announcements') }}</a></li>
                  <li class="active">{{ trans('Add announcement') }}</li>
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
                            <h4 class="header2">Add announcement</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'announcement_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('add-announcement')]) }}
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <label for="title">Title*</label>
                                            {{ Form::text('title',null, ['class'=>'form-control','id'=>'title','autocomplete'=>false]) }}
					                                  @if ($errors->has('title')) <p class="help-block">{{ $errors->first('title') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12" style="margin-top:5%">
                                          <label for="description" style="margin-top:-5%">Description *</label>
                                          {{ Form::textarea('description', null, ['class' => 'validate','id' => 'description']) }}
                    											@if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12">
                                            <label for="image">Image ( jpg, png )</label><br><br>
                                            {{ Form::file('image', array('class'=>'form-control','id'=>'image')) }}
  					                                @if ($errors->has('image')) <p class="help-block">{{ $errors->first('image') }}</p> @endif
                                        </div>
                                          <div class="input-field col s12" style="margin-top:5%">
                                              <label for="providers" style="margin-top:-3%">Select to limit announcement to providers</label><br>
                                                <select class="browser-default" name="visible_providers[]" multiple="multiple" style="width:100%" id="providers">
                                                  @foreach ($providers as $provider)
                                                    <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                                  @endforeach
                                                </select>
                                          </div>
										  <div class="input-field col s12" style="margin-top:3%">
                                              <label for="cities" style="margin-top:-3%">Select to limit announcement to cities</label><br>
                                                <select class="browser-default" name="visible_cities[]" multiple="multiple" style="width:100%" id="cities">
                                                  @foreach ($cities as $city)
                                                    <option value="{{ $city->city_name }}">{{ $city->city_name }}</option>
                                                  @endforeach
                                                </select>
                                          </div>
                                          <div class="input-field col s12" style="margin-top:5%">
                                            <select class="browser-default" name="notification_alert" multiple="multiple" style="width:100%" id="notification_alert">
                                              <option value="1">Instant</option>
                                              <option value="2">Provider’s app settings</option>
                                            </select>
                                            <label for="notification_alert" style="margin-top:-5%">Push notification</label><br>
                                        </div>
                                        <div class="input-field col s12"  style="margin-top:5%">
                                          <select  class="browser-default" name="email_alert" multiple="multiple" style="width:100%" id="email_alert">
                                            <option value="1">Everyone</option>
                                            <option value="2">Provider’s app settings</option>
                                          </select>
                                          <label for="email_alert" style="margin-top:-5%">Email notifications</label><br>
                                      </div>

                                      <div class="input-field col s12">
                                          <label for="stable_time">Time to stay in feeds (Days example: 2)</label>
                                          {{ Form::text('stable_time',null, ['class'=>'form-control','id'=>'stable_time','autocomplete'=>false]) }}
                                          @if ($errors->has('stable_time')) <p class="help-block">{{ $errors->first('stable_time') }}</p> @endif
                                      </div>
                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createannouncement']) }}
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
    <script src="https://cdn.ckeditor.com/4.7.3/standard/ckeditor.js"></script>
    <script>
			CKEDITOR.replace( 'description' );
		</script>
@stop
