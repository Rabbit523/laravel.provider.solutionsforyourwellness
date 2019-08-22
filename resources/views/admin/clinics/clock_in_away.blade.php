@extends('admin.layouts.default_layout')
@section('content')
@section('title','Clinics with clock in away')
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
                  <li class="active">{{ trans('Clinics with clock in away') }}</li>
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
			 {{ Form::open(['id'=>'record_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <!--DataTables example-->
          <br>
          <a href="javascript:void(0);" id="dlt_recordss" class="btn waves-effect waves-light red dark "><i class="mdi-action-delete left" aria-hidden="true"></i> Delete All</a>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-action-dashboard left"></i>
          </a>
          
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                
                <table id="clockin_away_table" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                        <th width="5%"><input type="checkbox" class="form-control selectall" value="0"  id="selectall">
                        <label for="selectall">All</label></th>
                        <th width="15%">{{ trans('Clinic name') }}</th>
                        <th width="15%">{{ trans('Provider name') }}</th>
                        <th width="15%">{{ trans('Clocked in lat') }}</th>
                        <th width="15%">{{ trans('Clocked in long') }}</th>
                        <th width="15%">{{ trans('Clinic date') }}</th>
                        <th width="20%">{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                        <th width="5%"><input type="checkbox" class="form-control selectall" value="0"  id="selectall">
                        <label for="selectall">All</label></th>
                        <th width="15%">{{ trans('Clinic name') }}</th>
                        <th width="15%">{{ trans('Provider name') }}</th>
                        <th width="15%">{{ trans('Clocked in lat') }}</th>
                        <th width="15%">{{ trans('Clocked in long') }}</th>
                        <th width="15%">{{ trans('Clinic date') }}</th>
                        <th width="20%">{{ trans('Action') }}</th>
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
