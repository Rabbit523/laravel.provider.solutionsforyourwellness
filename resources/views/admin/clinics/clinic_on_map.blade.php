@extends('admin.layouts.default_layout')
@section('content')
@section('title','Clinic on map')

<?php
	$segment2	=	Request::segment(1);
	$segment3	=	Request::segment(2);
	$segment4	=	Request::segment(3);
	$segment5	=	Request::segment(4);
	
?>
<style>
#chosen {
border-radius:10px;
width:20%;
background-image: -webkit-linear-gradient(top, #cccccc, #330000);
background-image: -o-linear-gradient(top, #cccccc, #330000);
background-image: -moz-linear-gradient(top, #cccccc, #330000);
text-align:center;
color:#ffffff;
font-weight:bold; font-size:large;
margin-left:40%;
}
#picHolder {
width:250px;
height:240px;
margin:10px;
}
input[type='range'] {
	    height: 50px;
-webkit-appearance: none;
padding-left:2px; padding-right:2px;
-webkit-border-radius: 5px;
background-image: -webkit-linear-gradient(top, #ffbfbf, #4e5777, #959ea7);
}
</style>
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
                  <li class="active">{{ trans('Clinic on map') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
	  <?php
	  if(isset($clinic->clock_in) && isset($clinic->clock_out)){
		  $clinic_clockin_time 	= 	new DateTime($clinic->clock_in, new DateTimeZone('GMT'));
		$clinic_clockin_time->setTimezone(new DateTimeZone($clinic->timezone));
		$clockindate_time 		= 	$clinic_clockin_time->format('Y-m-d H:i:s');
		$clockindate_time_stamp	=	strtotime($clinic->clock_in);
		
		$clinic_clockout_time 	= 	new DateTime($clinic->clock_out, new DateTimeZone('GMT'));
		$clinic_clockout_time->setTimezone(new DateTimeZone($clinic->timezone));
		$clockoutdate_time 		= 	$clinic_clockout_time->format('Y-m-d H:i:s');
		
		$clockoutdate_time_stamp	=	strtotime($clinic->clock_out);

		$clinic_spend_time 		= 	$clinic->clinic_spend_time;
		$middle_time 			= 	round($clinic_spend_time/2);
		
		$clinic_start_data = DB::table('geolocation')->where('clinic_id',$clinic->id)->where('user_id',$provider->id)->first();
		if(isset($clinic_start_data) && $clinic_start_data != null){
			$user_start_lat = $clinic_start_data->latitude;
			$user_start_long = $clinic_start_data->longitude;
		}else{
			$user_start_lat = '';
			$user_start_long = '';
		}

		$provider_name 	= $provider->first_name.' '.$provider->last_name;
		$clinic_name		=  $clinic->name;
		$clinic_latitude 	= $clinic->latitude; //------------ IP latitude
		$clinic_longitude = $clinic->longitude; //------------ IP latitude ip2location_longitude()
		$color	=	1;
	  ?>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
          <!--jqueryvalidation-->
          <div id="jqueryvalidation" class="section">
            <div class="row">
				<div class="col s12 m12 l12">
					<div class="col s12 m12 l10">
                        <div class="card-panel">
                            <h4 class="header2">Clinic on map</h4>
                            <div class="row">
								
								<div id="dvMap" style="height: 300px">
								</div>
							</div>
							<div class="row">
								<div id="slider" style="margin-top:5%">
									<div class="m8">
										<form id="range_form" method="GET">
											<input id="slide" type="range" name="range_value"
											 min="0" max="{{ $clinic_spend_time }}" step="1" value="0"
											 onChange="update_slider(this.value)" style="background:grey" />
											 <input type="hidden" value="{{ $segment3 }}" name="clinic_id">
											 <input type="hidden" value="{{ $segment4 }}" name="provider_id">
											 <input type="hidden" value="{{ $user_start_lat }}" id="provider_lat">
											 <input type="hidden" value="{{ $user_start_long }}" id="provider_lng">
										</form>
									</div>
								</div>
							</div>
							
							<div class="row" >
								<div class="m8">
									<div id="timecard_div">
									<?php 
									$admin_settings 	= 	DB::table('admin_settings')->where('id',20)->first();
									$green_miles_start 	= 	$admin_settings->green_miles_start;
									$green_miles_end 	= 	$admin_settings->green_miles_end;
									$yellow_miles_start = 	$admin_settings->yellow_miles_start;
									$yellow_miles_end 	= 	$admin_settings->yellow_miles_end;
									$red_miles_start 	= 	$admin_settings->red_miles_start;
									for ($i=$clockindate_time_stamp; $i<=$clockoutdate_time_stamp;$i=$i+60){
										if(isset($providergeodata[date("d-m-Y H:i", $i)])){
											
											if($providergeodata[date("d-m-Y H:i", $i)] <= $green_miles_end ){
												echo "<div class='minutewidth' style='background-color:green;float:left'>&nbsp;</div>";
											}else if($providergeodata[date("d-m-Y H:i", $i)] && $providergeodata[date("d-m-Y H:i", $i)] >=$yellow_miles_start && $providergeodata[date("d-m-Y H:i", $i)] <= $yellow_miles_end){
												echo "<div class='minutewidth' style='background-color:yellow;float:left'>&nbsp;</div>";
											}else{
												echo "<div class='minutewidth' style='background-color:red;float:left'>&nbsp;</div>";
											}
										}else{
											echo "<div class='minutewidth' style='background-color:grey;float:left'>&nbsp;</div>";
										}
									}
									?>
									</div>
									
								</div>
							</div>
							<div class="row">
								<div class="m2" style="float:left">Clock in :{{ $clockindate_time }}</div>
								<div class="m8"> </div>
								<div class="m2" style="float:right">Clock out :{{ $clockoutdate_time }}</div>
							</div>
							<div class="row">
								<div class="m12">
									<h2 style="padding:2%;text-align:center;font-size:20px">Total clinic spend time  :  {{ $clinic_spend_time }} minutes <a class="btn waves-effect waves-light blue modal-trigger" href="#time_edit_modal"><i class="mdi-editor-mode-edit"></i> </a></h2>
								</div>
							</div>
							<div class="row">
								<div class="m12">
									<div id="chosen" >after {{ 0 }} minutes</div>
								</div>
							</div>
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

<script>	
	var width	=	parseInt($('#timecard_div').width()/$(".minutewidth").length);
	$(".minutewidth").css('width',width+'px');
	window.onload = function () {
		lats = document.getElementById('provider_lat').value;
		lngs = document.getElementById('provider_lng').value;
		
		var markers = [
				{
					"title": 'Clinic Location',
					"lat": '{{ $clinic_latitude }}',
					"lng": '{{ $clinic_longitude }}',
					"description": '{{ $clinic->location_name }}'
				}
			,
				{
					"title": '{{ $provider_name }}',
					"lat": lats,
					"lng": lngs,
					"description": '{{ $provider->address }}'
				}
		];
        var mapOptions = {
            center: new google.maps.LatLng(markers[0].lat, markers[0].lng),
            minZoom: 5, maxZoom: 15,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
        var infoWindow = new google.maps.InfoWindow();
        var lat_lng = new Array();
        var latlngbounds = new google.maps.LatLngBounds();
        for (i = 0; i < markers.length; i++) {
            var data = markers[i]
            var myLatlng = new google.maps.LatLng(data.lat, data.lng);
            lat_lng.push(myLatlng);
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: data.title
            });
            latlngbounds.extend(marker.position);
            (function (marker, data) {
                google.maps.event.addListener(marker, "click", function (e) {
                    infoWindow.setContent(data.description);
                    infoWindow.open(map, marker);
                });
            })(marker, data);
        }
        map.setCenter(latlngbounds.getCenter());
        map.fitBounds(latlngbounds);
 
        //***********ROUTING****************//
 
        //Initialize the Path Array
        var path = new google.maps.MVCArray();
 
        //Initialize the Direction Service
        var service = new google.maps.DirectionsService();
 
        //Set the Path Stroke Color
        var poly = new google.maps.Polyline({ map: map, strokeColor: '#4986E7' });
 
        //Loop and Draw Path Route between the Points on MAP
        for (var i = 0; i < lat_lng.length; i++) {
            if ((i + 1) < lat_lng.length) {
                var src = lat_lng[i];
                var des = lat_lng[i + 1];
                path.push(src);
                poly.setPath(path);
                service.route({
                    origin: src,
                    destination: des,
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                }, function (result, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
                            path.push(result.routes[0].overview_path[i]);
                        }
                    }
                });
            }
        }
    }
   function update_slider(slideAmount) {
		//get the element
		var display = document.getElementById("chosen");
		//show the amount
		display.innerHTML='after ' +slideAmount+ ' minutes';

		$.ajax({
		type: "POST",
		url:"{{URL::route('get_provider_location')}}",
		data: $("#range_form").serialize(),
		success: function(res){
				if(res){
					var arr 	= res.split("~");
					var lat 	= arr[0];
					var lng 	= arr[1];
					var markers1 = [
							{
								"title": 'Clinic Location',
								"lat": '{{ $clinic_latitude }}',
								"lng": '{{ $clinic_longitude }}',
								"description": '{{ $clinic->location_name }}'
							}
						,
							{
								"title": '{{ $provider_name }}',
								"lat": lat,
								"lng": lng,
								"description": '{{ $provider->address }}'
							}
					];
						var mapOptions = {
							center: new google.maps.LatLng(markers1[0].lat, markers1[0].lng),
							minZoom: 5, maxZoom: 15,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						};
						var map = new google.maps.Map(document.getElementById("dvMap"), mapOptions);
						var infoWindow = new google.maps.InfoWindow();
						var lat_lng = new Array();
						var latlngbounds = new google.maps.LatLngBounds();
						for (i = 0; i < markers1.length; i++) {
							var data = markers1[i]
							var myLatlng = new google.maps.LatLng(data.lat, data.lng);
							lat_lng.push(myLatlng);
							var marker1 = new google.maps.Marker({
								position: myLatlng,
								map: map,
								title: data.title
							});
							latlngbounds.extend(marker1.position);
							(function (marker1, data) {
								google.maps.event.addListener(marker1, "click", function (e) {
									infoWindow.setContent(data.description);
									infoWindow.open(map, marker1);
								});
							})(marker1, data);
						}
						map.setCenter(latlngbounds.getCenter());
						map.fitBounds(latlngbounds);
				 
						//***********ROUTING****************//
				 
						//Initialize the Path Array
						var path = new google.maps.MVCArray();
				 
						//Initialize the Direction Service
						var service = new google.maps.DirectionsService();
				 
						//Set the Path Stroke Color
						var poly = new google.maps.Polyline({ map: map, strokeColor: '#4986E7' });
				 
						//Loop and Draw Path Route between the Points on MAP
						for (var i = 0; i < lat_lng.length; i++) {
							if ((i + 1) < lat_lng.length) {
								var src = lat_lng[i];
								var des = lat_lng[i + 1];
								path.push(src);
								poly.setPath(path);
								service.route({
									origin: src,
									destination: des,
									travelMode: google.maps.DirectionsTravelMode.DRIVING
								}, function (result, status) {
									if (status == google.maps.DirectionsStatus.OK) {
										for (var i = 0, len = result.routes[0].overview_path.length; i < len; i++) {
											path.push(result.routes[0].overview_path[i]);
										}
									}
								});
							}
						}
				   $('#provider_lat').val(lat);
				   $('#provider_lng').val(lng);
					return false;
				}else if(res==0){
					var errormessage				=	'Lat long not recevied.';
				}else if(res==-1){
					$(".loader").hide();
				}
			}
		});
	}   
    $(function () {
        $("#RemoveDivTime").change(function () {
            if ($(this).val() == "custom") {
                $("#ManualTime").show();
            } else {
                $("#ManualTime").hide();
            }
        });
    });
</script> 
<div id="time_edit_modal" class="modal" style="z-index: 1003; display: none; opacity: 0; transform: scaleX(0.7); top: 143.057px;">
{{ Form::open(['id'=>'remove_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('cut_time',array($clinic->id,$provider->id))]) }}
  <div class="modal-content">
	<div class="row">
			<div class="row">
				<div class="input-field col s6">
				<label for="first_name" class="" style="margin-bottom:5%">Remove time*</label><br>
				<input type="hidden" name="provider_id" value="{{ $provider->id }}">
				<input type="hidden" name="clinic_id" value="{{ $clinic->id }}">
					<select class="browser-default" name="cut_time" id="RemoveDivTime">
						<option disabled selected>Select cut time</option>
						<option value="red">Remove red time</option>
						<option value="red_yellow">Remove red+yellow time</option>
						<option value="custom">Update manually</option>
					</select>
				</div>
				<div id="ManualTime" style="display: none">
					<div class="input-field col s6" style="margin-top:5%">
						<input type="text" id="txtPassportNumber" name="cutmanualtime" placeholder="enter minute to cut" />
					</div>
				</div>
			</div>
		
	</div>
  </div>
  <div class="modal-footer">
	<a href="#" class="waves-effect waves-red btn-flat modal-action modal-close">No</a>
	<input type="submit" name="cut-time_form" class="waves-effect waves-green btn-flat modal-action modal-close"  value="Yes">
	
  </div>
  {{ Form::close() }}
</div>
<?php
 }else{
		  URL::route('admindashboard');
	  }
?>
@stop
