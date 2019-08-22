@extends('admin.layouts.default_layout')
@section('content')
@section('title','certificate list')
<?php
$provider_id	=	Request::segment(3);
if($provider_id == null){
	$add_certificate_url = URL::route('add-certificate');
}else{
	$add_certificate_url = URL::route('add_certificate_by_id',$provider_id);
}
?>
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Certificates') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Certificates') }}</li>
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
          <br>
          <a class="btn waves-effect waves-light green" href="{{$add_certificate_url}}" name="action">Add certificate
            <i class="mdi-content-add-circle left"></i>
          </a>
          <button type="button" class="btn waves-effect waves-light red" id="Deleteall" name="action">Delete all
            <i class="mdi-action-delete left"></i>
          </button>
          <button type="button" class="btn waves-effect waves-light blue" id="DownloadZip">Download
            <i class="mdi-file-file-download left"></i>
          </button>
		  <button type="button" class="btn waves-effect waves-light deep-purple" data-provider="{{$provider_id}}" id="download_all_certificates">Download all
            <i class="mdi-file-file-download left"></i>
          </button>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-action-dashboard left"></i>
          </a>

          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                <div class="row">
                  <div id="card-alert" class="card green success" style="display:none">
                      <div class="card-content white-text">
                        <p></p>
                      </div>
                      <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                      </button>
                  </div>
                  <div id="card-alert" class="card red error" style="display:none">
                      <div class="card-content white-text">
                        <p></p>
                      </div>
                      <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                      </button>
                  </div>
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
                    <!-- <select id="SelectedProvider" multiple class="" name="SelectedProvider[]" >
                        <option value="" selected="selected" disabled="disabled">Select Provider</option>
                        <option value="all">All</option>
                      @foreach ($providers as $provider)
                        <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
                      @endforeach
                  </select> -->
                  </div>
                  <div class="input-field col s2">
                    <input type="button" class="btn waves-effect waves-light grey dark" value="Search" id="btnSearch" />
                  </div>
                </div>
                  {!! Form::close() !!}
                </div>
              </div>
                <table id="certificate_table" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                          <th width="5%"><input type="checkbox" class="form-control selectall" value="0" name="visible_providers" id="selectall">
                          <label for="selectall">All</label></th>
                          <th width="15%">{{ trans('Name') }}</th>
                          <th width="12%">{{ trans('Subject') }}</th>
                          <th width="10%">{{ trans('Files') }}</th>
                          <th width="32%">{{ trans('Description') }}</th>
                          <th width="14%">{{ trans('Uploaded at') }}</th>
                          <th width="12%">{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                        <th width="5%"><input type="checkbox" class="form-control selectall" value="0" name="visible_providers" id="selectall">
                          <label for="selectall">All</label></th>
													<th width="15%">{{ trans('Name') }}</th>
                          <th width="12%">{{ trans('Subject') }}</th>
                          <th width="10%">{{ trans('Files') }}</th>
                          <th width="32%">{{ trans('Description') }}</th>
                          <th width="14%">{{ trans('Uploaded at') }}</th>
                          <th width="12%">{{ trans('Action') }}</th>
                    </tr>
                  </tfoot>

                  <tbody>
                  <?php $x = 1?>
                    @foreach($certifications as $certification)
                      <tr>
                          <td>{{$x}}</td>
                          <td>{{$certification->first_name}}</td>
                          <td>{{$certification->subject}}</td>
                          <td><a href="{{ WEBSITE_UPLOADS_URL }}certificates/{{$certification->file}}" target="_blank">Download file</a></td>
                          <td>
                            <a href="{{ URL::route('edit-certificates',$certification->certificate_id) }}" class="btn-floating waves-effect waves-light orange"><i class="mdi-editor-border-color"></i></a>
                            <a  href="{{ URL::route('delete-certificates',$certification->certificate_id) }}"class="btn-floating waves-effect waves-dark red" onclick="return confirm('are you sure you want to delete this certificate')"><i class="mdi-action-delete"></i></a>
                          </td>
                      </tr>
                  <?php $x++?>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <br>
        </div>
      </div>
      <!--end container-->

    </section>
    <!-- END CONTENT -->
@stop
