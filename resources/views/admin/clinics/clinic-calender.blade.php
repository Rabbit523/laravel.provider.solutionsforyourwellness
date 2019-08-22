
@extends('admin.layouts.default_layout')
@section('content')
@section('title','clinic calender')
<?php
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
              <h5 class="breadcrumbs-title">{{ trans('Clinic calender') }} </h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('clinics')}}">{{ trans('Clinics') }}</a></li>
                  <li class="active">{{ trans('Clinic calender') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
        <div class="section">
            <div class="divider"></div>

	          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('add-clinic')}}" name="action">Add clinic
	            <i class="mdi-content-add-circle left"></i>
	          </a>
	          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
	            <i class="mdi-action-dashboard left"></i>
	          </a>
						<!-- <div class="col s12 m12 l12">
						{!! Form::open(['id'=>'provider_forms','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('clinic-calender')]) !!}
						<div class="row">
						<div class="col s10" style="margin-top:2%">
							<select class="browser-default valid" multiple name="SelectedProvider[]" id="SelectedProvider" data-error=".errorTxt6" style="width:100%;">
								<option value="all">All</option>
								@foreach ($providers as $provider)
									<option value="{{ $provider->id }}" >{{ $provider->first_name }} {{ $provider->last_name }}</option>
								@endforeach
							</select>
						</div>
						<div class="input-field col s2">
							<input type="submit" class="btn waves-effect waves-light grey dark" value="Search" />
						</div>
					</div>
						{!! Form::close() !!}
					</div> -->
            <div id="full-calendar">
              <div class="row">
                <div class="col s12 m12 l12">
					
                  <div id='calendar'></div>
                  <script>

                      $(document).ready(function() {
                          /* initialize the calendar

                          -----------------------------------------------------------------*/
													var color = $('#box_color').val();
                          $('#calendar').fullCalendar({
                            header: {
                              left: 'prev,next today',
                              center: 'title',
                              right: 'month,basicWeek,basicDay'
                            },
                            defaultDate: '{{ date('Y-m-d') }}',
                            editable: false,
                            droppable: false, // this allows things to be dropped onto the calendar
                            eventLimit: true, // allow "more" link when too many events
                            events : [
                                <?php foreach($tasks as $task){ 
									$clinic_date_time = new DateTime($task['date'].' '.$task['time'], new DateTimeZone('GMT'));
									$clinic_date_time->setTimezone(new DateTimeZone($task['timezone']));
									$date_time 		= $clinic_date_time->format('Y-m-d H:i:s');
									$clinicdate     = $clinic_date_time->format('Y-m-d');
									$time         	= $clinic_date_time->format('H:i:s');
									?>
                                {	title : '{{ $task["location_name"] }}',
                                    start : '{{ $clinicdate }}',
                                    color :'{{ $task["color"] }}',
									status :'{{ $task["clinic_status"] }}',
									time :'{{ $date_time  }}',
                                },
                                <?php } ?>
                            ],
								eventRender: function (event, element) {
								element.attr('href', 'javascript:void(0);');
								element.click(function() {
									$("#startTime").html(moment(event.time).format('MMM Do h:mm A'));
									$("#eventInfo").html(event.title);
									$("#eventAddress").html(event.title);
									$("#eventstatus").html(event.status);
									$("#calender_detailss").openModal({ modal: true, title: event.title, width:350});
								});
							}
                          });
                        });
                  </script>
                </div>
              </div>
            </div>
            </div>
      </div>
      <!--end container-->
    </section>
		<div id="calender_detailss" class="modal bottom-sheet">
        <div class="modal-content">
          <h4 style="font-size:22px" id='eventInfo'></h4>
          <ul class="collection">
            <li class="collection-item avatar">
              <span class="title" id='eventstatus'>Title</span>
              <p id="eventAddress">Address :</p>
              <p id='startTime'><b>time : </b></p>
            </li>
          </ul>
        </div>
      </div>
		</div>
		<div id="eventContent" title="Event Details" style="display:none;">
    Start: <span id="startTime"></span><br>
    End: <span id="endTime"></span><br><br>
    <p id="eventInfo"></p>
    <p><strong><a id="eventLink" href="" target="_blank">Read More</a></strong></p>
</div>
    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
@stop
