@extends('admin.layouts.default_layout')
@section('content')
@section('title','announcement list')
<!-- Content Wrapper. Contains page content -->
<section id="content">
      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Announcements') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Announcements') }}</li>
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
          <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('add-announcement')}}" name="action">Add announcement
            <i class="mdi-content-add-circle left"></i>
          </a>
            <a href="javascript:void(0);" id="dlt_announcement" class="btn waves-effect waves-light red dark "><i class="mdi-action-delete left" aria-hidden="true"></i> Delete All</a>
          <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('admindashboard')}}" name="action">Go to dashboard
            <i class="mdi-action-dashboard left"></i>
          </a>
          <div id="table-datatables"><br>
            <div class="divider"></div>
            <div class="row">
              <div class="col s12 m12 l12">
                <table id="announcement_table" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                        <th width="3%"><input type="checkbox" class="form-control selectall" value="0" id="selectall">
                        <label for="selectall">All</label></th>
                          <th >{{ trans('Title') }}</th>
                          <th >{{ trans('Image') }}</th>
                          <th>{{ trans('Description') }}</th>
						  <th >{{ trans('Uploaded at') }}</th>
                          <th >{{ trans('Status') }}</th>
                          <th >{{ trans('Action') }}</th>
                      </tr>
                  </thead>
                  <tfoot>
                    <tr>
                      <th width="3%"><input type="checkbox" class="form-control selectall" value="0" id="selectall">
                      <label for="selectall">All</label></th>
                      <th >{{ trans('Title') }}</th>
                      <th >{{ trans('Image') }}</th>
                      <th >{{ trans('Description') }}</th>
					  <th >{{ trans('Uploaded at') }}</th>
                      <th >{{ trans('Status') }}</th>
                      <th >{{ trans('Action') }}</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  <?php $x = 1?>
                    @foreach($announcement as $item)
                      <tr>
                          <td><input type="checkbox" id="{{ $item->id }}" value="{{$item->id}}" name="chk_ids[]" class="checked_announcement" /><label for="{{ $item->id }}"></label></td>
                          <td>{{$item->title}}</td>
                          <td><img src="{{ WEBSITE_UPLOADS_URL }}announcement/{{ $item->image }}" height="60" width="60"></td>
                          <td>@if ($item->status == 0){{ trans('inactive') }}@endif
                              @if ($item->status == 1){{ trans('active') }}@endif
                          </td>
                          <td>
                            <a href="{{ URL::route('edit-announcement',$item->id)}}"><i class="mdi-editor-border-color"></i></a>
                            <a  href="{{ URL::route('delete-announcement',$item->id)}}"><i class="mdi-action-delete"></i></a>
                            @if ($item->status == 1)
                            <a  href="{{ URL::route('announcement-status',$item->id)}}" onclick="return confirm('are you sure to deactivate this announcement')"><i class="mdi-action-lock-open"></i></a>
                            @endif
                            @if ($item->status == 0)
                            <a  href="{{ URL::route('announcement-status',$item->id)}}" onclick="return confirm('are you sure to activate this announcement')"><i class="mdi-action-lock"></i></a>
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
