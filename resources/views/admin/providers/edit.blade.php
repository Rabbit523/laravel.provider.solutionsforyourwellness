@extends('admin.layouts.default_layout')
@section('content')
@section('title','provider edit')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Providers') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('providers')}}">{{ trans('Providers') }}</a></li>
                  <li class="active">{{ trans('Edit provider') }}</li>
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
                            <h4 class="header2">Edit provider</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'edit_provider_form','class'=>'form-horizontal','autocomplete'=>'off','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('edit-provider',$provider->id)]) }}
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <label for="uname">First name*</label>
                                            {{ Form::text('first_name',$provider->first_name, ['class'=>'form-control','id'=>'first_name','autocomplete'=>false]) }}
					                                  @if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="uname">Last name*</label>
                                            {{ Form::text('last_name',$provider->last_name, ['class'=>'form-control','id'=>'last_name','autocomplete'=>false]) }}
  					                                @if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                          <label for="cemail">E-mail *</label>
                                          {{ Form::text('email',$provider->email, ['class'=>'form-control','id'=>'email','autocomplete'=>false]) }}
  					                              @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                          <label for="cemail">Phone number *</label>
                                          {{ Form::text('phone',$provider->phone, ['class'=>'form-control','id'=>'phone','autocomplete'=>false]) }}
					                                @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                                        </div>

                                        <div class="input-field col s4">
                                          <label for="password">Password </label>
                                          {{ Form::text('password',null,['class'=>'form-control','id'=>'password','autocomplete'=>false]) }}
                                          @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                        <button type="button" id="generate_password" class="btn waves-effect waves-light right green submit waves-input-wrapper" onclick="GetrandomString(this)">Generate Password</button>
                                        </div>

                                        <div class="input-field col s4">
                                          <label for="cemail">Last four ss# </label>
										  <input type="password" name="social_security_number" class="form-control ss-input" maxlength="4" >
					                     @if ($errors->has('social_security_number')) <p class="help-block">{{ $errors->first('social_security_number') }}</p> @endif
                                        </div>
                                        <div class="col s12">
                                          <label for="crole">Provider type *</label>
                                          <select class="browser-default" name="provider_type" data-error=".errorTxt6">
                                    				<option value="" disabled selected>Choose type</option>
                                    				<option value="1099 contractor" @if ($provider->provider_type == '1099 contractor'){{ trans('selected') }}@endif>1099 contractor</option>
                                            <option value="W2 employee" @if ($provider->provider_type == 'W2 employee'){{ trans('selected') }}@endif>W2 employee</option>
                                    			</select>
                                			    @if ($errors->has('provider_type')) <p class="help-block">{{ $errors->first('provider_type') }}</p> @endif
                                        </div>
                                        <div class="input-field col s3">
                                          <label for="cemail">Hourly rate *</label>
                                          {{ Form::text('hourly_rate',$provider->hourly_rate, ['class'=>'form-control','id'=>'hourly_rate','autocomplete'=>false]) }}
					                                @if ($errors->has('hourly_rate')) <p class="help-block">{{ $errors->first('hourly_rate') }}</p> @endif
                                        </div>
                                        <div class="input-field col s3">
                                          <label for="cemail">Max hours </label>
                                          {{ Form::text('max_hours',$provider->max_hours, ['class'=>'form-control','id'=>'max_hours','autocomplete'=>false]) }}
					                                @if ($errors->has('max_hours')) <p class="help-block">{{ $errors->first('max_hours') }}</p> @endif
                                        </div>
										<div class="input-field col s6" style="margin-top:4%;margin-bottom:2%">
                                          <?php $explode_string = $provider->timezone;
											  ?>
                                          <select class="browser-default valid" multiple name="timezone" id="providertimezone" style="width:100%" >
                                            @foreach ($timezones as $timezone)
											  <option value="{{ $timezone->timezone_name }}" <?php if($explode_string ==$timezone->timezone_name){ echo 'selected';}?>>{{ $timezone->timezone_name.' '.$timezone->timezone_value }}</option>
											@endforeach
                                          </select>
                                          <label for="timezone" style="margin-top:-8%">Timezone</label>

                                           @if ($errors->has('provider_type')) <p class="help-block">{{ $errors->first('provider_type') }}</p> @endif
                                      </div>
										
										<div class="input-field col s12">
                                          <label for="image">Image ( jpg, png )</label><br><br>
                                          {{ Form::file('image', array('class'=>'form-control','id'=>'image')) }}
                                          @if ($errors->has('image')) <p class="help-block">{{ $errors->first('image') }}</p> @endif <br>
                                          @if($provider->image)
                                          <img src="{{  WEBSITE_UPLOADS_URL.'users/'.$provider->image }}" height="50" width="60">
                                          @endif
                                      </div>

                                        <div class="input-field col s12" style="margin-top:5%">
                                        <label for="crole" style="margin-top:-2%">Address * ( enter location or you can also drag pointer)</label>
                                        {!! Form::text('location', $provider->address, ['class' => 'form-control', 'id' => 'searchInput','required' => 'required']) !!}

                                        {{ Form::hidden('lat', $provider->latitude, array('id' => 'lat')) }}
                                        {{ Form::hidden('lng', $provider->longitude, array('id' => 'lng')) }}
                                          <div class="map" id="map" style="width: 100%; height: 300px;"></div>
                                          @if ($errors->has('location')) <p class="help-block">{{ $errors->first('location') }}</p> @endif
                                        </div>

                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createprovider']) }}
                                          <a href="{{URL::route('providers')}}" class="btn waves-effect waves-light right red">Back</a>
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
  <?php
  $latitude = $provider->latitude; //------------ IP latitude
  $longitude = $provider->longitude; //------------ IP latitude ip2location_longitude()
  ?>
  <script>
  function initialize() {
     var latlng = new google.maps.LatLng(<?php echo $latitude; ?>,<?php echo $longitude; ?>);
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
  <script>
	$(document).ready(function(){
	$('.ss-input').unbind('keyup change input paste').bind('keyup change input paste',function(e){
		var $this = $(this);
		var val = $this.val();
		var valLength = val.length;
		var maxCount = $this.attr('maxlength');
		if(valLength>maxCount){
			$this.val($this.val().substring(0,maxCount));
		}
	}); 
	});
</script>
@stop
