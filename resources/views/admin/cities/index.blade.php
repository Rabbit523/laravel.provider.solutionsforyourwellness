@extends('admin.layouts.default_layout')
@section('content')
@section('title','Cities list')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Cities') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Cities') }}</li>
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
          {{ Form::open(['id'=>'cities_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <br>
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('add_city')}}" name="action">Add city
            <i class="mdi-content-add-circle left"></i>
          </a>
          <a href="javascript:void(0);" id="delete_all_cities" class="btn waves-effect waves-light red dark "><i class="mdi-action-delete left" aria-hidden="true"></i> Delete All</a>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-action-dashboard left"></i>
          </a>
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                <table id="cities_table" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                        <th><input type="checkbox" class="form-control selectall" value="0"  id="selectall">
                        <label for="selectall">All</label></th>
                        <th>{{ trans('City Name') }}</th>
                        <th>{{ trans('Description') }}</th>
                        <th>{{ trans('Date') }}</th>
                        <th>{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                      <th><input type="checkbox" class="form-control selectall" value="0"  id="selectall">
                        <label for="selectall">All</label></th>
                        <th>{{ trans('City Name') }}</th>
                        <th>{{ trans('Description') }}</th>
                        <th>{{ trans('Date') }}</th>
                        <th>{{ trans('Action') }}</th>
                    </tr>
                  </tfoot>

                  <tbody>
                  <?php $x = 1?>
                    @foreach($cities as $city)
                      <tr>
                          <td><input type="checkbox" id="{{ $city->id }}" value="{{$city->id}}" name="chk_ids[]" class="checked_cities" /><label for="{{ $city->id }}"></label></td>
                          <td>{{$city->city_name}}</td>
                          <td>{{$city->description}}</td>
                          <td>{{$city->created_at}}</td>
                          <td>
                            <a href="{{URL::route('edit_city',$city->id)}}"><i class="mdi-editor-border-color"></i></a>
                            <a  href="{{URL::route('delete_city',$city->id)}}"><i class="mdi-action-delete"></i></a>
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
