@extends('admin.layouts.default_layout')
@section('content')
@section('title','clinic edit')
<!-- Content Wrapper. Contains page content -->
<section id="content">
      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Clinics') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('clinics')}}">{{ trans('Clinics') }}</a></li>
                  <li class="active">{{ trans('Edit clinic') }}</li>
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
                <div class="col s12 m12 l12">
                        <div class="card-panel">
                            <h4 class="header2">Edit clinic</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'edit_clinic_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('edit-clinic',$clinic->id)]) }}
                                    <div class="row">
                                      <?php
                                      $user_id = Auth::user()->id;
                                      $user_time_zone 			= DB::table('users')->select('timezone')->where('id',$user_id)->get();
                                      $user_time_zone_value = $user_time_zone[0]->timezone;

                                      $clinic_date_time = new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
                                      $clinic_date_time->setTimezone(new DateTimeZone($clinic->timezone));
                                      $date_time 		= $clinic_date_time->format('Y-m-d H:i');
                                      $date         = $clinic_date_time->format('Y-m-d');
                                      $time         = $clinic_date_time->format('H:i');

                                      ?>
                                        <div class="input-field col s6">
                                            <label for="name">Name</label>
                                            {{ Form::text('name',$clinic->name, ['class'=>'form-control','id'=>'name','autocomplete'=>false]) }}
                                            @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="phone">Contact number</label>
                                            {{ Form::text('phone',$clinic->phone, ['class'=>'form-control','id'=>'phone','autocomplete'=>false]) }}
					                                  @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="date">Date</label>

                                            {{ Form::text('date',$date, ['class'=>'datepicker','id'=>'date','autocomplete'=>false]) }}
                                            @if ($errors->has('date')) <p class="help-block">{{ $errors->first('date') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="input_starttime">Start time</label>
                                            {{ Form::text('time',$time, ['class'=>'timepicker','id'=>'input_starttime','autocomplete'=>false]) }}
  					                                @if ($errors->has('time')) <p class="help-block">{{ $errors->first('time') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="preptime">Prep time (minutes ex: 30)</label>
                                            {{ Form::text('preptime',(strtotime($clinic->time)-strtotime($clinic->prep_time))/(60), ['class'=>'form-control','id'=>'preptime','autocomplete'=>false]) }}
                                            @if ($errors->has('preptime')) <p class="help-block">{{ $errors->first('preptime') }}</p> @endif
                                        </div>
										@if($clinic->manual_provider == null)
                                        <div class="col s12" id="s2id_providers_lable" style="margin-top:2%;margin-bottom:2%;">
                                          <label for="providers" >Select preferred provider </label><br>
                                            <?php $explode 		= explode(',', $clinic->provider_id);
												$explode_manual = explode(',', $clinic->manual_provider);
												$explode_string = array_diff($explode,$explode_manual);
											?>
                                          <select class="browser-default" name="providers[]" multiple="multiple" style="width:100%" id="providers">
                                            @foreach ($providers as $provider)
                                              <option value="{{ $provider->id }}" <?php if(in_array($provider->id,$explode_string)){ echo 'selected class="active selected"';}?>>{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                            @endforeach
                                          </select>
                                          @if ($errors->has('providers')) <p class="help-block">{{ $errors->first('providers') }}</p> @endif
                                        </div>
										@endif
										
										
										<div class="col s12" id="s2id_manual_providers_lable" style="margin-top:2%;margin-bottom:2%;">
                                          <label for="manual_providers">Manual provider </label><br>
                                            <?php $explode_string = explode(',', $clinic->manual_provider); ?>
                                          <select class="browser-default" name="manual_providers[]" multiple="multiple" style="width:100%" id="manual_providers" >
                                            @foreach ($providers as $provider)
                                              <option value="{{ $provider->id }}" <?php if(in_array($provider->id,$explode_string)){ echo 'selected class="active selected"';}?>>{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                            @endforeach
                                          </select>
                                          @if ($errors->has('manual_providers')) <p class="help-block">{{ $errors->first('manual_providers') }}</p> @endif
                                        </div>
										<div class="success" style="padding-left:1%"><p></p></div>
										
										<div style="color:#f00;" class="input-field col s12" id="providers_error"></div>

                                        <div class="input-field col s4">
                                            <label for="estimated_duration">Estimated duration (Minutes: ex- 50)</label>
                                            {{ Form::text('estimated_duration',$clinic->estimated_duration, ['class'=>'form-control','id'=>'estimated_duration','autocomplete'=>false]) }}
					                                  @if ($errors->has('estimated_duration')) <p class="help-block">{{ $errors->first('estimated_duration') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="personnel">Personnel</label>
                                            {{ Form::text('personnel',$clinic->personnel, ['class'=>'form-control','id'=>'personnel','autocomplete'=>false]) }}
                                            @if ($errors->has('personnel')) <p class="help-block">{{ $errors->first('personnel') }}</p> @endif
                                        </div>
                                        <div class="input-field col s4">
                                            <label for="uname">Unfilled time (hours example : 10)</label>
                                            {{ Form::text('unfilled_time',$clinic->default_unfilled_time, ['class'=>'form-control','id'=>'unfilled_time','autocomplete'=>false]) }}
                                            @if ($errors->has('unfilled_time')) <p class="help-block">{{ $errors->first('unfilled_time') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12">
                                            <label for="service_provider">Services Provided</label>
                                            {{ Form::text('service_provider',$clinic->service_provider, ['class'=>'form-control','id'=>'service_provider','autocomplete'=>false]) }}
                                            @if ($errors->has('service_provider')) <p class="help-block">{{ $errors->first('service_provider') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12" style="margin-top:4%;margin-bottom:2%">
                                          <?php $explode_string = $clinic->timezone;
											  ?>
                                          <select class="browser-default valid" multiple name="timezone" id="timezone" style="width:100%" >
                                            @foreach ($timezones as $timezone)
											  <option value="{{ $timezone->timezone_name }}" <?php if($explode_string ==$timezone->timezone_name){ echo 'selected';}?>>{{ $timezone->timezone_name.' '.$timezone->timezone_value }}</option>
											@endforeach
                                          </select>
                                          <label for="timezone" style="margin-top:-5%">Clinic timezone</label>

                                           @if ($errors->has('provider_type')) <p class="help-block">{{ $errors->first('provider_type') }}</p> @endif
                                      </div>
                                        <div class="input-field col s12" style="margin-top:3%">
                                        <label for="searchInput" style="margin-top:-2%">Address * ( enter location or you can also drag pointer)</label>
                                        {!! Form::text('location', $clinic->location_name, ['class' => 'form-control', 'id' => 'searchInput','required' => 'required']) !!}

                                        {{ Form::hidden('lat', $clinic->latitude, array('id' => 'lat')) }}
                                        {{ Form::hidden('lng', $clinic->longitude, array('id' => 'lng')) }}
                                          <div class="map" id="map" style="width: 100%; height: 300px;"></div>
                                          @if ($errors->has('location')) <p class="help-block">{{ $errors->first('location') }}</p> @endif
                                        </div>

                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createclinic']) }}
                                          <a href="{{URL::route('clinics')}}" class="btn waves-effect waves-light right red">Back</a>
                                        </div>
                                    </div>
                                {{ Form::close() }}
								<?php if(isset($CheckClinicStatus) && $CheckClinicStatus  == 0){ ?>
								<div class="col s12 m12 l12">
									<table class="striped" style="margin-top:2%">
										<thead>
										  <tr>
											<th data-field="no" width="5%">S.No.</th>
											<th data-field="name" width="5%">Name</th>
											<th data-field="item" width="10%">Date</th>
											<th data-field="item" width="25%">Location</th>
											<th data-field="uprice" width="10%">Hourly rate</th>
											<th data-field="uprice" width="10%">Clock in</th>
											<th data-field="price" width="10%">Clock out</th>
											<th data-field="price" width="10%">Total time</th>
											<th data-field="price" width="10%">Mileage</th>
											<th data-field="price" width="15%">Drive time</th>
											<th data-field="price" width="15%">Price</th>
										  </tr>
										</thead>
										<tbody>
										 @php($x=1)
											@if(!empty($provider_data))
										  @foreach($provider_data as $record)
											<?php 
												$clinic_date_time = new DateTime($record->date.' '.$record->time, new DateTimeZone('GMT'));
												$clinic_date_time->setTimezone(new DateTimeZone($record->timezone));
												$date         	= $clinic_date_time->format('Y-m-d');
												
												$clinic_clockin = new DateTime($record->clock_in, new DateTimeZone('GMT'));
												$clinic_clockin->setTimezone(new DateTimeZone($record->timezone));
												$clockin         	= $clinic_clockin->format('Y-m-d H:i:s');
												
												$clinic_clockout = new DateTime($record->clock_out, new DateTimeZone('GMT'));
												$clinic_clockout->setTimezone(new DateTimeZone($record->timezone));
												$clockout         	= $clinic_clockout->format('Y-m-d H:i:s');
											?>
										  <tr>
											<td>{{ $x }}</td>
											<td>{{ $record->first_name.' '.$record->last_name }}</td>
											<td>{{ $date }}</td>
											
											<td>{{ $record->clinic_location }} <br><a href="{{ URL::route('clinic_on_map',[$record->clinic_id,$record->provider_id]) }}" class="btn waves-effect waves-light indigo" >View on map</a></td>
											<td>{{ $record->hourly_rate }}</td>
											<td>{{ $clockin }}</td>
											<td>{{ $clockout }}</td>
											<td>
											<?php 
												$clinic_spend_time	= $record->clinic_spend_time/60;
											?>
												{{ number_format($record->clinic_spend_time/60,2) }} hours
											  <?php $final_array[] = isset($record->clinic_spend_time)?number_format($record->clinic_spend_time/60,2):[]; ?>
											</td>
											<td>{{ $final_mileage= $record->mileage }} miles
												<?php $total_mileage[] = isset($final_mileage)?$final_mileage:[]; ?>
											</td>
											<td>
											{{ $drive_time= number_format($record->drive_time/60,2) }} hours
											<?php $total_drive_time[] = isset($drive_time)?$drive_time:[]; ?>
											</td>
											<td>
												<?php 
													$price			=	$record->income;
													$price_array[]	=	$record->income;
												?>
												${{ number_format($price,2) }}
											</td>
										  </tr>
										   @php($x++)
										@endforeach
										<tr>
										<td colspan="5"></td>
										<td  class="white-text"></td>
										<td  class="cyan white-text">Sub Total:</td>
										<?php if(isset($final_array)){?>
										<td  class="cyan white-text">{{$grand_total = array_sum($final_array)}} hours</td>
										<?php } else{ ?>
										  <td class="cyan white-text">0 hours</td>
									  <?php  } ?>
													<?php if(isset($total_mileage)){?>
													<td class="cyan white-text">{{$total_mileage = array_sum($total_mileage)}} miles</td>
													<?php } else{ ?>
														<td class="cyan white-text">0 miles</td>
												<?php  } ?>
												<?php if(isset($total_drive_time)){?>
												<td class="cyan white-text">{{$total_drive_time = array_sum($total_drive_time)}} hours</td>
												<?php } else{ ?>
													<td class="cyan white-text">0 hours</td>
											<?php  } ?>
											<td class="cyan white-text">${{ isset($price_array)?number_format(array_sum($price_array),2):0 }}</td>
									  </tr>
									@else
										<h5>No records found.</h5>
									@endif
										</tbody>
									  </table>
								</div>	
							<?php } ?>	
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
  <script type="text/javascript">
	<?php if(isset($CheckClinicStatus) && $CheckClinicStatus  == 0){ ?>
		$("#edit_clinic_form :input").prop("disabled", true);
	<?php } ?>
		 var provider_id = $("#manual_providers").val();
		 var clinic_id 	= {{ $clinic->id }};
		 $.ajax({
		   type: "POST",
		   url:"{{URL::route('check_asign_status')}}",
		   data: {
			"_token": "{{ csrf_token() }}",
			"provider_id": provider_id,
			"clinic_id": clinic_id,
		  },
		   success: function(res){
			 if(res==1){
			   var message	   =	'You can asign to another provider.';
			   //$('.success p').html(message);
			   //$('.success p').css('color','green');
			  
			 }else if(res==0){
			   var errormessage				=	'Clinic in progress or over. you cant asign this clinic to other provider.';
			   $('.success p').html(errormessage);
			   $('.success p').css('color','red');
			  $('#s2id_manual_providers').css('display','none');
			  $('#s2id_providers').css('display','none');
			  $('#s2id_providers_lable').css('display','none');
			  $('#s2id_manual_providers_lable').css('display','none');
			  //$('#providers').css('display','none');
			  //$('#manual_providers').attr('disabled', 'disabled');
			  //$("#manual_providers").prop('disabled', 'disabled');
			 }
		   }
		 });
  </script>
@stop
