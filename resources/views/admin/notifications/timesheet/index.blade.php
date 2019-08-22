@extends('admin.layouts.default_layout')
@section('content')
@section('title','Timesheet')
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
              <h5 class="breadcrumbs-title">{{ trans('Timesheet') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Timesheet Report') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
        <!---<a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('provider-details',$segment4)}}" name="action">Provider details
          <i class="mdi-maps-local-atm left"></i>
        </a>
        <a class="btn waves-effect waves-light blue dark" type="submit" href="{{ URL::route('view-certificates',$segment4)}}" name="action">Certificates
          <i class="mdi-image-remove-red-eye left"></i>
        </a>
        <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('provider-calender',$segment4)}}" name="action">Provider Calender
          <i class="mdi-image-edit left"></i>
        </a>-->
        <div id="invoice">
            <div class="invoice-table">
              <div class="row">
					<!---<div class="col s12 m12 l5 offset-l4">
					  {!! Form::open(['id'=>'search_timesheet_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form','url'=>URL::route('timesheet_user_search')]) !!}
					  <div class="row">
						  <div class="col s10" style="margin-top:4%">
							<select class="browser-default valid" multiple name="provider[]" id="SelectedProvider" data-error=".errorTxt6" style="width:300px;">
							  <option value="all">All</option>
							  @foreach ($providers as $provider)
								<option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
							  @endforeach
							</select>
						  </div>
					  <div class="input-field col s2">
						<input type="button" id="btnSearch" class="btn waves-effect waves-light grey dark" value="Search"/>
					  </div>
					</div>
					  {!! Form::close() !!}
					</div>-->
                </div>
				{!! Form::open(['id'=>'timesheet_download_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('timesheet_download')]) !!}
				<input type="submit" name="submit" value="Download Excel" class="btn btn-success">
				<hr>
                <div class="col s12 m12 l12">
                  <table class="striped" id="timesheet_records_datatable">
                    <thead>
                      <tr>
                        <th data-field="no" width="5%">
							<input type="checkbox" class="form-control selectall" value="0" id="selectall">
							<label for="selectall">All</label>
						</th>
						<th data-field="name" width="5%">Name</th>
                        <th data-field="price" width="10%">Total Time</th>
						<th data-field="price" width="10%">Total Mileage</th>
						<th data-field="price" width="15%">Total Drive time</th>
						<th data-field="price" width="15%">Total Price</th>
						<th data-field="price" width="15%">Action</th>
                      </tr>
                    </thead>
                    <tbody>
						@if(!empty($records))
							@foreach($records as $record)
							@if($record['timesheet_status'] == 0)
							<tr>
								<td>
									<input type="checkbox" id="{{ $record['provider_id'] }}" value="{{ $record['provider_id'] }}" name="chk_ids[]"/>
								</td>
								<td>{{ CustomHelper::GetUserNameById($record['provider_id']) }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>
									<button type="button" class="btn btn-primary" disabled>View</a>
								</td>
							</tr>
							@else
							<tr>
								<td>
									<input type="checkbox" id="{{ $record['provider_id'] }}" value="{{ $record['provider_id'] }}" name="download_timesheet_excel[]" />
									<label for="{{ $record['provider_id'] }}"></label>
								</td>
								<td>{{ $record['first_name'].' '.$record['last_name'] }} </td>
								<td>{{ $record['total_spend_time'] }} Mins</td>
								<td>{{ $record['total_mileage'] }} Miles</td>
								<td>{{ $record['total_drive_time'] }} Mins</td>
								<td>${{ $record['income_total'] }}</td>
								<td>
									<a class="btn btn-primary" href="{{ URL::route('timesheet_single_view',$record['provider_id']) }}">View</a>
								</td>
							</tr>
							@endif
						@endforeach
						@else
						<h3>No records found.</h3>
						@endif
                    </tbody>
                  </table>
                </div>
				{!! Form::close() !!}
              </div>
            </div>

          </div>
      </div>

      <!--end container-->
    </section>
    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
@stop
