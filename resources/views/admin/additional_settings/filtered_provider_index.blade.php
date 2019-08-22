@extends('admin.layouts.default_layout')
@section('content')
@section('title','certificate list')
<?php 
$provider_id	=	Request::segment(3);
?>
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
          <!-- Search for small screen -->
          <div class="header-search-wrapper grey hide-on-large-only">
              <i class="mdi-action-search active"></i>
              <input type="text" name="Search" class="header-search-input z-depth-2" placeholder="Explore Materialize">
          </div>
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
          <!--DataTables example-->
          {{ Form::open(['id'=>'provider_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <br>
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('add-certificate')}}" name="action">Add certificate
            <i class="mdi-content-add-circle left"></i>
          </a>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-content-add-circle left"></i>
          </a>
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 l3 offset-l4">
            {!! Form::open(['id'=>'provider_forms','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('get_filter_provider')]) !!}
            <div class="row">
            <div class="input-field col s12">
              <select id="userId" name="userId"  onChange="this.form.submit();">
                  <option value="" selected="selected" disabled="disabled">Select Provider</option>
                @foreach ($providers as $provider)
                  <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
                @endforeach
            </select>
            </div>
          </div>
            {!! Form::close() !!}
          </div>
        </div>
            <div class="row">
              <div class="col s12 m12 l12">
                <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                          <th>{{ trans('S.no') }}</th>
                          <th>{{ trans('Name') }}</th>
                          <th>{{ trans('Subject') }}</th>
                          <th>{{ trans('Files') }}</th>
                          <th>{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                        <th>{{ trans('S.no') }}</th>
                        <th>{{ trans('Name') }}</th>
                        <th>{{ trans('Subject') }}</th>
                        <th>{{ trans('Files') }}</th>
                        <th>{{ trans('Action') }}</th>
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
                            <a href="{{ URL::route('edit-certificates',$certification->certificate_id) }}" ><i class="mdi-editor-border-color"></i></a>
                            <a  href="{{ URL::route('delete-certificates',$certification->certificate_id) }}" onclick="return confirm('are you sure you want to delete this certificate')"><i class="mdi-action-delete"></i></a>

                          </td>
                      </tr>
                  <?php $x++?>
                    @endforeach
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
