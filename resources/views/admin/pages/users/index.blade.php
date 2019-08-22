@extends('admin.layouts.pages_layout')
@section('content')
@section('title','Users list')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Data Tables
        <small>advanced tables</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Tables</a></li>
        <li class="active">Data tables</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-xs-12">
		<a href = "{{ URL::route('admincreateuser')}}" class = "btn btn-success">Create User</a>
			<a href = "{{ URL::route('admindashboard')}}" class = "btn btn-danger">Back</a>
			<hr>
				@if(Session::has('flash_error'))
					<div class="alert alert-error"><span class="glyphicon glyphicon-info-sign"></span> &nbsp;{{Session::get('flash_error')}}</div>
				@endif
				 @if(Session::has('flash_success'))
					<div class="alert alert-success"><span class="glyphicon glyphicon-info-sign"></span> &nbsp;{{Session::get('flash_success')}}</div>
				@endif
			<hr>
         <div class="box">
            <div class="box-header">
              <h3 class="box-title">Users list</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="user_data_table" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{ trans('S No') }}</th>
                  <th>{{ trans('Name') }}</th>
                  <th>{{ trans('Image') }}</th>
                  <th>{{ trans('Username') }}</th>
                  <th>{{ trans('Email') }}</th>
                  <th>{{ trans('Phone') }}</th>
                  <th>{{ trans('Status') }}</th>
				           <th>{{ trans('Action') }}</th>
                </tr>
                </thead>
                 <tbody>
				@php ($x = 1)
				@foreach($records as $record)
					<tr>
						<td>{{ $x }}</td>
						<td>{{ $record->first_name.' '.$record->last_name }}</td>
						<td>
							@if($record->image)
								<a class="fancybox" href="{{WEBSITE_UPLOADS_URL}}users/{{$record->image}}">
									<img src="{{WEBSITE_UPLOADS_URL}}users/50x50/{{$record->image}}">
								</a>
							@else
								<a class="fancybox" href="{{WEBSITE_UPLOADS_URL}}users/NoPreview.png">
									<img src="{{WEBSITE_UPLOADS_URL}}users/NoPreview.png">
								</a>
							@endif
						</td>
						<td>{{ $record->username }}</td>
						<td>{{ $record->email }}</td>
						<td>{{ $record->phone }}</td>
						<td>{{ $record->status }}</td>
						 <td>
							<a class="btn btn-info" href="{{ URL::route('adminedituser',$record->id) }}">
								<i class="fa fa-edit "></i>
							</a>
							<button class="btn btn-danger btn-delete-user" title="delete" data-id="{{ URL::route('admindeleteuser',$record->id) }}">
								<i class="fa fa-trash "></i>
							</button>
						</td>
					</tr>
					@php ($x++)
					@endforeach
					</tbody>
                <tfoot>
                <tr>
                  <th>{{ trans('S No') }}</th>
                  <th>{{ trans('Name') }}</th>
                  <th>{{ trans('Image') }}</th>
                  <th>{{ trans('Username') }}</th>
                  <th>{{ trans('Email') }}</th>
                  <th>{{ trans('Phone') }}</th>
                  <th>{{ trans('Status') }}</th>
				          <th>{{ trans('Action') }}</th>
                </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
@stop
