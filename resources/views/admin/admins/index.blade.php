@extends('admin.layouts.default_layout')
@section('content')
@section('title','admins list')
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
              <h5 class="breadcrumbs-title">{{ trans('Admins') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Admins') }}</li>
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
          {{ Form::open(['id'=>'admin_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <br>
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('add-admin')}}" name="action">Add admin
            <i class="mdi-content-add-circle left"></i>
          </a>
          <a href="javascript:void(0);" id="delete_all_admin" class="btn waves-effect waves-light red dark "><i class="mdi-action-delete left" aria-hidden="true"></i> Delete All</a>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-action-dashboard left"></i>
          </a>
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                <table id="admin_table" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                        <th><input type="checkbox" class="form-control selectall" value="0" id="selectall">
                        <label for="selectall">All</label></th>
                          <th>{{ trans('Full name') }}</th>
                          <th>{{ trans('Email') }}</th>
                          <th>{{ trans('Phone') }}</th>
                          <th>{{ trans('Status') }}</th>
                          <th>{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                      <th><input type="checkbox" class="form-control selectall" value="0" id="selectall">
                      <label for="selectall">All</label></th>
                        <th>{{ trans('Full name') }}</th>
                        <th>{{ trans('Email') }}</th>
                        <th>{{ trans('Phone') }}</th>
                        <th>{{ trans('Status') }}</th>
                        <th>{{ trans('Action') }}</th>
                    </tr>
                  </tfoot>

                  <tbody>
                  <?php $x = 1?>
                    @foreach($admins as $admin)
                      <tr>
                          <td>{{$x}}</td>
                          <td>{{$admin->first_name}} {{$admin->last_name}}</td>
                          <td>{{$admin->email}}</td>
                          <td>{{$admin->phone}}</td>
                          <td>@if ($admin->status == 0){{ trans('inactive') }}@endif
                              @if ($admin->status == 1){{ trans('active') }}@endif
                          </td>
                          <td>
                            <a href="{{ URL::route('edit-admin',$admin->id)}}"><i class="mdi-editor-border-color"></i></a>
                            <a  href="{{ URL::route('delete-admin',$admin->id)}}"><i class="mdi-action-delete"></i></a>
                            @if ($admin->status == 1)
                            <a  href="{{ URL::route('admin-status',$admin->id)}}" onclick="return confirm('are you sure to deactivate this admin')"><i class="mdi-action-lock-open"></i></a>
                            @endif
                            @if ($admin->status == 0)
                            <a  href="{{ URL::route('admin-status',$admin->id)}}" onclick="return confirm('are you sure to activate this admin')"><i class="mdi-action-lock"></i></a>
                            @endif
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
