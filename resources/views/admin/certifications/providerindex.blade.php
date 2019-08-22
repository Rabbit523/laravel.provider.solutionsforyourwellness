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
                  <li><a href="{{ URL::route('admindashboard') }}">{{ trans('Dashboard') }}</a></li>
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
          {{ Form::open(['id'=>'certificate_forms','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
          <br>
          <a class="btn waves-effect waves-light green" type="submit" href="{{ $add_certificate_url }}" name="action">Add certificate
            <i class="mdi-content-add-circle left"></i>
          </a>
          <a href="javascript:void(0);" id="dlt_certificates" class="btn waves-effect waves-light red dark "><i class="mdi-action-delete left" aria-hidden="true"></i> Delete All</a>
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
                <table id="data-table-simple" class="responsive-table display" cellspacing="0">
                  <thead>
                      <tr>
                        <th width="5%"><input type="checkbox" class="form-control selectall" value="0" id="selectall">
                        <label for="selectall">All</label></th>
												<th width="15%">{{ trans('Name') }}</th>
												<th width="12%">{{ trans('Subject') }}</th>
												<th width="10%">{{ trans('Files') }}</th>
												<th width="34%">{{ trans('Description') }}</th>
												<th width="14%">{{ trans('Uploaded at') }}</th>
												<th width="10%">{{ trans('Action') }}</th>
                      </tr>
                  </thead>

                  <tfoot>
                    <tr>
                      <th width="5%"><input type="checkbox" class="form-control selectall" value="0" id="selectall">
                      <label for="selectall">All</label></th>
											<th width="15%">{{ trans('Name') }}</th>
											<th width="12%">{{ trans('Subject') }}</th>
											<th width="10%">{{ trans('Files') }}</th>
											<th width="34%">{{ trans('Description') }}</th>
											<th width="14%">{{ trans('Uploaded at') }}</th>
											<th width="10%">{{ trans('Action') }}</th>
                    </tr>
                  </tfoot>

                  <tbody>
                  <?php $x = 1?>
                    @foreach($certifications as $certification)
                      <tr>
                          <td><input type="checkbox" id="{{ $certification->certificate_id }}" value="{{$certification->certificate_id}}" name="chk_ids[]" class="checked_certificates" /><label for="{{ $certification->certificate_id }}"></label></td>
                          <td>{{$certification->first_name}}</td>
                          <td>{{$certification->subject}}</td>
                          <td>
                            <?php if($certification->type == 'png' || $certification->type == 'PNG' || $certification->type == 'JPG'|| $certification->type == 'JPEG' || $certification->type == 'jpg' || $certification->type == 'jpeg'){?>
                              <a class="fancybox" href="{{ WEBSITE_UPLOADS_URL.'certificates/'.$certification->file}}" ><img src="{{ WEBSITE_UPLOADS_URL.'certificates/'.$certification->file }}" target="_blank" style="height:40px" ></a>
                              <?php } else {?>
                            <a href="{{ WEBSITE_UPLOADS_URL }}certificates/{{$certification->file}}" target="_blank">View file</a>
                            <?php } ?>
                          </td>
                          <td>@if(strlen($certification->description)>50){{ substr($certification->description,0,50).'...' }} @else{{ $certification->description }} @endif</td>
                          <td>{{ $certification->created_at }}</td>
                          <td>
                            <a href="{{ URL::route('edit-certificates',$certification->certificate_id) }}" ><i class="mdi-editor-border-color"></i></a>
														<a title="delete" data-toggle="tooltip" class="delete_record_btn" href="javascript:void(0);" data-url="{{URL::route('delete-certificate',$certification->certificate_id)}}"> <i class="mdi-action-delete" title="Delete"></i></a>
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
