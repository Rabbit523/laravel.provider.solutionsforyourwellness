@extends('admin.layouts.pages_layout')
@section('content')
@section('title','Email logs')
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
			<hr>
         <div class="box">
            <div class="box-header">
              <h3 class="box-title">Email logs</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table id="example1" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>{{ trans('S. No.') }}</th>
                  <th>{{ trans('Email to') }}</th>
                  <th>{{ trans('Subject') }}</th>
                  <th>{{ trans('Message') }}</th>
                  <th>{{ trans('Status') }}</th>
                  <th>{{ trans('Created at') }}</th>
                  <th>{{ trans('Action') }}</th>
                </tr>
                </thead>
                 <tbody>
              @php($x=1)
				    @foreach($logs as $log)
					<tr>
						<td>{{ $x }}</td>
						<td>{{ $log->email_to }}</td>
						<td>{{ $log->subject }}</td>
						<td>{{ $log->message }}</td>
						<td>{{ $log->status }}</td>
						<td>{{ $log->created_at }}</td>
						 <td>
							<a class="btn btn-info" href="{{ URL::route('edit_email_template',$log->id) }}">
								<i class="fa fa-edit "></i>
							</a>

						</td>
					</tr>
					@php($x++)
					@endforeach
					</tbody>
                <tfoot>
                <tr>
                  <th>{{ trans('S. No.') }}</th>
                  <th>{{ trans('Email to') }}</th>
                  <th>{{ trans('Subject') }}</th>
                  <th>{{ trans('Message') }}</th>
                  <th>{{ trans('Status') }}</th>
                  <th>{{ trans('Created at') }}</th>
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
  <!-- /.content-wrapper -->
@stop
