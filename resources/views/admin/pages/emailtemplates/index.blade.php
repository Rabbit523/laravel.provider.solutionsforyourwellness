@extends('admin.layouts.default_layout')
@section('content')
@section('title','Email Templates List')

<!-- Content Wrapper. Contains page content -->
<section id="content">
      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Email Templates') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Email Templates') }}</li>
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
          {{ Form::open(['id'=>'announcement_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <br>
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('create_email_template')}}" name="action">Add Email Template
            <i class="mdi-content-add-circle left"></i>
          </a>
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                <table id="data-table-simple" class="responsive-table display"  cellspacing="0">
                <thead>
                <tr>
                  <th>{{ trans('S No') }}</th>
                  <th>{{ trans('Name') }}</th>
                  <th>{{ trans('Subject') }}</th>
                  <th>{{ trans('Body') }}</th>
                  <th>{{ trans('Action') }}</th>
                        </tr>
                        </thead>
                         <tbody>
                <?php $x = 1;?>
                @foreach($templates as $emailtemplate)
                  <tr>
                    <td>{{ $x }}</td>
                    <td>{{ $emailtemplate->name }}</td>
                    <td>{{ $emailtemplate->subject }}</td>
                    <td>{{ $emailtemplate->body }}</td>
                     <td>
                        <a href="{{ URL::route('edit_email_template',$emailtemplate->id)}}"><i class="mdi-editor-border-color"></i></a>
                    </td>
                  </tr>
                  <?php $x++;?>
                  @endforeach
                  </tbody>
                        <tfoot>
                        <tr>
                          <th>{{ trans('S No') }}</th>
                          <th>{{ trans('Name') }}</th>
                          <th>{{ trans('Subject') }}</th>
                          <th>{{ trans('Body') }}</th>
                          <th>{{ trans('Action') }}</th>
                        </tr>
                        </tfoot>
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
