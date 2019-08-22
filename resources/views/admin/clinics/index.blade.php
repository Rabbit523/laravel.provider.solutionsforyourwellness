@extends('admin.layouts.default_layout')
@section('content')
@section('title','Clinics list')
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
                  <li class="active">{{ trans('Clinics') }}</li>
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

          <!--DataTables example-->
          {{ Form::open(['id'=>'clinics_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <br>
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('add-clinic')}}" name="action">Add clinic
            <i class="mdi-content-add-circle left"></i>
          </a>
          <a href="javascript:void(0);" id="dlt_clinics" class="btn waves-effect waves-light red dark "><i class="mdi-action-delete left" aria-hidden="true"></i> Delete All</a>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-action-dashboard left"></i>
          </a>
          <a class="btn waves-effect waves-light blue dark" type="submit" href="{{ URL::route('clinic-calender')}}" name="action">View on calender
            <i class="mdi-notification-event-available left"></i>
          </a>
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                <div class="col s12 m12 l5 offset-l4">
                {!! Form::open(['id'=>'provider_forms','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('get_filter_provider')]) !!}
                <div class="row">
                <div class="col s10" style="margin-top:4%">
                  <select class="browser-default valid" multiple name="SelectedProvider[]" id="SelectedProvider" data-error=".errorTxt6" style="width:300px;">
                    <option value="all">All</option>
                    @foreach ($providers as $provider)
                      <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="input-field col s2">
                  <input type="button" class="btn waves-effect waves-light grey dark" value="Search" id="btnSearch" />
                </div>
              </div>
                {!! Form::close() !!}
              </div>
                <table id="clinic_table" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                        <th width="5%"><input type="checkbox" class="form-control selectall" value="0"  id="selectall">
                        <label for="selectall">All</label></th>
                        <th width="10%">{{ trans('Name') }}</th>
                        <th width="8%">{{ trans('Phone') }}</th>
                        <th width="25%">{{ trans('Address') }}</th>
                        <th width="10%">{{ trans('Time') }}</th>
                        <th width="10%">{{ trans('Date') }}</th>
                        <th width="10%">{{ trans('Asign rule') }}</th>
                        <th width="10%">{{ trans('Personnel') }}</th>
						<th width="10%">{{ trans('Accepted') }}</th>
                        <th width="12%">{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                      <th width="5%"><input type="checkbox" class="form-control selectall" value="0"  id="selectall">
                      <label for="selectall">All</label></th>
                      <th width="10%">{{ trans('Name') }}</th>
                      <th width="8%">{{ trans('Phone') }}</th>
                      <th width="25%">{{ trans('Address') }}</th>
                      <th width="10%">{{ trans('Time') }}</th>
                      <th width="10%">{{ trans('Date') }}</th>
					  <th width="10%">{{ trans('Asign rule') }}</th>
                      <th width="10%">{{ trans('Personnel') }}</th>
					  <th width="10%">{{ trans('Accepted') }}</th>
                      <th width="12%">{{ trans('Action') }}</th>
                    </tr>
                  </tfoot>

                  <tbody>
                 
                  </tbody>
                </table>

              </div>
            </div>
          </div>
          {{Form::close()}}
          <br>
        </div>
      </div>
      <!--end container-->

    </section>
    <!-- END CONTENT -->
@stop
