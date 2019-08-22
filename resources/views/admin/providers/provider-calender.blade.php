
@extends('admin.layouts.default_layout')
@section('content')
@section('title','provider calender')
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
              <h5 class="breadcrumbs-title">{{ trans('Provider calender') }} (  {{ CustomHelper::GetUserData($segment3)->first_name.' '.CustomHelper::GetUserData($segment3)->last_name }} )</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('providers')}}">{{ trans('Providers') }}</a></li>
                  <li class="active">{{ trans('Provider calender') }}</li>
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
            <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('provider-details',$segment3)}}" name="action">Provider Details
              <i class="mdi-action-assignment left"></i>
            </a>
            <div id="full-calendar">
              <div class="row">
                <div class="col s12 m12 l12">
                  <div id='calendar'></div>
                  <script>
                      $(document).ready(function() {
                          /* initialize the calendar
                          -----------------------------------------------------------------*/
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
                                @foreach($tasks as $task)
                                {
                                    title : '{{ $task->location_name }}',
                                    start : '{{ $task->clock_in }}',
                                    end   : '{{ $task->clock_out }}',
                                    //color : '#ff3333',
                                },
                                @endforeach
                            ],
                            eventRender: function(event, element) {
                              element.qtip({
                                content: 	'<b>' + event.start.format('hh:mma') + ' - ' + event.end.format('hh:mma') + '</b>' +
                                  	'<br>' +
                                  	'<u>' + event.title + '</u>',
                                    position: {
                                      target: 'mouse'
                                    },
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

    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
@stop
