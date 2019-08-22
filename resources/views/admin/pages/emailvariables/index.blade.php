@extends('admin.layouts.default_layout')
@section('content')
@section('title','Email Variable List')

<!-- Content Wrapper. Contains page content -->
<section id="content">
      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Email Variables') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Email Variables') }}</li>
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
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('create_email_variables')}}" name="action">Add Email Variable
            <i class="mdi-content-add-circle left"></i>
          </a>
          <div id="data-table-simple"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
               <table id="example1" class="responsive-table display">
                  <thead>
                  <tr>
                    <th>{{ trans('S No') }}</th>
                    <th>{{ trans('Name') }}</th>
                    <th>{{ trans('Description') }}</th>
                    <th>{{ trans('Action') }}</th>
                  </tr>
                  </thead>
                   <tbody>
                    <?php $x = 1;?>
                    @foreach($variables as $variable)
                      <tr>
                        <td>{{ $x }}</td>
                        <td>{{ $variable->variable_name }}</td>
                        <td>{{ $variable->variable_description }}</td>
                         <td>
                           <a href="{{ URL::route('edit_email_variables',$variable->id)}}"><i class="mdi-editor-border-color"></i></a>
                        </td>
                      </tr>
                      <?php $x++;?>
                      @endforeach
                    </tbody>
                  <tfoot>
                  <tr>
                    <th>{{ trans('S No') }}</th>
                    <th>{{ trans('Name') }}</th>
                    <th>{{ trans('Description') }}</th>
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
