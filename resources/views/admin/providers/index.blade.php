@extends('admin.layouts.default_layout')
@section('content')
@section('title','Providers list')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Providers') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Providers') }}</li>
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
          {{ Form::open(['id'=>'provider_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <br>
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('add-provider')}}" name="action">Add provider
            <i class="mdi-content-add-circle left"></i>
          </a>
          <a href="javascript:void(0);" id="dlt_providers" class="btn waves-effect waves-light red dark "><i class="mdi-action-delete left" aria-hidden="true"></i> Delete All</a>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-action-dashboard left"></i>
          </a>
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                <table id="provider_table" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                        <th><input type="checkbox" class="form-control selectall" value="0" name="visible_providers" id="selectall">
                        <label for="selectall">All</label></th>
                          <th>{{ trans('Full name') }}</th>
                          <th>{{ trans('Email') }}</th>
                          <th>{{ trans('Phone') }}</th>
                          <th>{{ trans('Provider type') }}</th>
						  <th>{{ trans('Address') }}</th>
                          <th>{{ trans('Status') }}</th>
                          <th>{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                      <th><input type="checkbox" class="form-control selectall" value="0" name="visible_providers" id="selectall">
                        <label for="selectall">All</label></th>
                        <th>{{ trans('Full name') }}</th>
                          <th>{{ trans('Email') }}</th>
                          <th>{{ trans('Phone') }}</th>
                          <th>{{ trans('Provider type') }}</th>
						  <th>{{ trans('Address') }}</th>
                          <th>{{ trans('Status') }}</th>
                          <th>{{ trans('Action') }}</th>
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
