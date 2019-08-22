@extends('admin.layouts.default_layout')
@section('content')
@section('title','clinic edit')
<!-- Content Wrapper. Contains page content -->
<section id="content">
      <!--start container-->
      <div class="container">
          <!--jqueryvalidation-->
          <div id="jqueryvalidation" class="section">
            <div class="row">
              <div class="col s12 m12 l12">
                  <div class="col s12 m12 l10">
                        <div class="card-panel">
                            <h4 class="header2">Edit clinic status</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'edit_clinic_status_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('edit_clinic_status',[$clinic->provider_id,$clinic->clinic_id])]) }}
                                    <div class="row">
                                      <?php
                                      $user_id = Auth::user()->id;
                                      $user_time_zone 			= DB::table('users')->select('timezone')->where('id',$user_id)->get();
                                      $user_time_zone_value = $user_time_zone[0]->timezone;

                                      $clinic_date_time = new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
                                      $clinic_date_time->setTimezone(new DateTimeZone($user_time_zone_value));
                                      $date_time 		= $clinic_date_time->format('Y-m-d H:i');
                                      $date         = $clinic_date_time->format('Y-m-d');
                                      $time         = $clinic_date_time->format('H:i');

                                      ?>
                                        <div class="input-field col s6">
                                            <label for="clock_in">Clock In</label>
											{{ Form::hidden('clinic_status_id',$clinic->id, ['class'=>'form-control','id'=>'clinic_status_id']) }}
                                            {{ Form::text('clock_in',$clinic->clock_in, ['class'=>'form-control','id'=>'clock_in']) }}
                                            @if ($errors->has('clock_in')) <p class="help-block">{{ $errors->first('clock_in') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="clock_out">Clock Out</label>
                                            {{ Form::text('clock_out',$clinic->clock_out, ['class'=>'form-control','id'=>'clock_out']) }}
					                                  @if ($errors->has('clock_out')) <p class="help-block">{{ $errors->first('clock_out') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12">
                                          {{Form::submit('Update',['class'=>'btn waves-effect waves-light right green submit']) }}
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
  <!-- /.content-wrapper -->
  <?php
  $latitude = $clinic->latitude; //------------ IP latitude
  $longitude = $clinic->longitude; //------------ IP latitude ip2location_longitude()
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
@stop
