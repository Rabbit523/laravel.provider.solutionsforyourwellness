@extends('admin.layouts.default_layout')
@section('content')
@section('title','add clinic')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Clinic') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('clinics')}}">{{ trans('Clinics') }}</a></li>
                  <li class="active">{{ trans('Add clinic') }}</li>
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
                            <h4 class="header2">Add clinic</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'clinic_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('add-clinic')]) }}
								
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <label for="name">Name</label>
                                            {{ Form::text('name',null, ['class'=>'form-control','id'=>'name','autocomplete'=>false]) }}
                                            @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="phone">Contact number</label>
                                            {{ Form::text('phone',null, ['class'=>'form-control','id'=>'phone','autocomplete'=>false]) }}
                                            @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="date" style="margin-top: -3%;">Date</label>
                                            {{ Form::date('date',null, ['class'=>'datepicker','id'=>'date','autocomplete'=>false]) }}
                                            @if ($errors->has('date')) <p class="help-block">{{ $errors->first('date') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="input_starttime">Start time</label>
                                            {{ Form::text('time',null, ['class'=>'timepicker','id'=>'input_starttime','autocomplete'=>false]) }}
                                            @if ($errors->has('time')) <p class="help-block">{{ $errors->first('time') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="preptime">Prep time (minutes ex: 30)</label>
                                            {{ Form::text('preptime',null, ['class'=>'form-control','id'=>'preptime','autocomplete'=>false]) }}
                                            @if ($errors->has('preptime')) <p class="help-block">{{ $errors->first('preptime') }}</p> @endif
                                        </div>

                                          <div class="input-field col s12" style="margin-top:4%;margin-bottom:2%">
                                            <select class="browser-default valid" multiple name="providers[]" id="providers" style="width:100%" >
                                              @foreach ($providers as $provider)
                                                <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                              @endforeach
                                            </select>
                                            <label style="margin-top:-5%" for="providers">Select preferred providers</label>
                                             @if ($errors->has('provider_type')) <p class="help-block">{{ $errors->first('provider_type') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12" style="margin-top:4%;">
                                          <label style="margin-top:-5%" for="manualprovider">Manually assign to a provider</label>
                                          <select class="browser-default valid" name="manualprovider[]" multiple id="manualprovider" style="width:100%">
                                            @foreach ($providers as $provider)
                                              <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                            @endforeach
                                          </select>
                                           @if ($errors->has('provider_type')) <p class="help-block">{{ $errors->first('provider_type') }}</p> @endif
                                      </div>
										<div style="color:#f00;" class="input-field col s12" id="providers_error"></div>

                                        <div class="input-field col s4">
                                            <label for="estimated_duration">Estimated duration (Minutes: ex- 50)</label>
                                            {{ Form::text('estimated_duration',null, ['class'=>'form-control','id'=>'estimated_duration','autocomplete'=>false]) }}
                                            @if ($errors->has('estimated_duration')) <p class="help-block">{{ $errors->first('estimated_duration') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="personnel">Personnel</label>
                                            {{ Form::text('personnel',null, ['class'=>'form-control','id'=>'personnel','autocomplete'=>false]) }}
                                            @if ($errors->has('personnel')) <p class="help-block">{{ $errors->first('personnel') }}</p> @endif
                                        </div>

                                        <div class="input-field col s4">
                                            <label for="unfilled_time">Unfilled time (hours: ex - 5)</label>
                                            {{ Form::text('unfilled_time',null, ['class'=>'form-control','id'=>'unfilled_time','autocomplete'=>false]) }}
                                            @if ($errors->has('unfilled_time')) <p class="help-block">{{ $errors->first('unfilled_time') }}</p> @endif
                                        </div>

                                        <div class="input-field col s12">
                                            <label for="service_provider">Services Provided</label>
                                            {{ Form::text('service_provider',null, ['class'=>'form-control','id'=>'service_provider','autocomplete'=>false]) }}
                                            @if ($errors->has('service_provider')) <p class="help-block">{{ $errors->first('service_provider') }}</p> @endif
                                        </div>

                                        <div class="input-field col s12" style="margin-top:5%">
                                            <select class="browser-default valid" multiple name="timezone" id="timezone" style="width:100%" >
                                              @foreach ($timezones as $timezone)
                                                <option value="{{ $timezone->timezone_name }}" <?php if(Auth::user()->timezone ==$timezone->timezone_name){ echo 'selected';}?>>{{ $timezone->timezone_name }} {{ $timezone->timezone_value }}</option>
                                              @endforeach
                                            </select>
                                            <label for="timezone" style="margin-top:-5%">Clinic timezone</label>
                                            @if ($errors->has('timezone')) <p class="help-block">{{ $errors->first('timezone') }}</p> @endif
                                        </div>
                                      <div class="input-field col s12">
                                      <label for="crole">Address * ( enter location or you can also drag pointer)</label>

                                       <!-- <input id="searchInput" name="location_control" class="input-controls" type="text" placeholder="Enter a location"> -->
                                      {!! Form::text('location', '', ['class' => 'form-control', 'id' => 'searchInput','required' => 'required']) !!}

                                      {{ Form::hidden('lat', '', array('id' => 'lat')) }}
                                      {{ Form::hidden('lng', '', array('id' => 'lng')) }}
                                      <div class="map" id="map" style="width: 100%; height: 300px;"></div>
                                        </div>
                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createclinic']) }}
                                          <a href="{{URL::route('clinics')}}" class="btn waves-effect waves-light right red">Back</a>
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
    /* script */
    function initialize() {
       var latlng = new google.maps.LatLng(27.6648,-81.5158);
        var map = new google.maps.Map(document.getElementById('map'), {
          center: latlng,
          zoom: 13
        });
        var marker = new google.maps.Marker({
          map: map,
          position: latlng,
          draggable: true,
          anchorPoint: new google.maps.Point(0, -29)
       });
        var input = document.getElementById('searchInput');
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
        var geocoder = new google.maps.Geocoder();
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);
        var infowindow = new google.maps.InfoWindow();
        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("Autocomplete's returned place contains no geometry");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            bindDataToForm(place.formatted_address,place.geometry.location.lat(),place.geometry.location.lng());
            infowindow.setContent(place.formatted_address);
            infowindow.open(map, marker);

        });
        // this function will work on marker move event into map
        google.maps.event.addListener(marker, 'dragend', function() {
            geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              if (results[0]) {
                  bindDataToForm(results[0].formatted_address,marker.getPosition().lat(),marker.getPosition().lng());
                  infowindow.setContent(results[0].formatted_address);
                  infowindow.open(map, marker);
              }
            }
            });
        });
    }
    function bindDataToForm(address,lat,lng){
       document.getElementById('searchInput').value = address;
       document.getElementById('lat').value = lat;
       document.getElementById('lng').value = lng;
    }

    </script>
@stop
