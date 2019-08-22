@extends('admin.layouts.pages_layout') 
@section('content')
@section('title','Update Logo')
<div id="content">
  <div id="content-header">
     <div id="breadcrumb"> <a href="{{ url('/admin/dashboard') }}" title="Go to Dashboard" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="javascript:void();">Update Logo</a></div>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-pencil"></i> </span>
            
          </div>
          <div class="widget-content nopadding">
		  @if(Session::has('flash_message_error'))
					<div class="alert alert-error">{{Session::get('flash_message_error')}}</div>
				@endif
				 @if(Session::has('flash_message_success'))
					<div class="alert alert-success">{{Session::get('flash_message_success')}}</div>
				@endif
			<div id = "error"></div>		
			<div id = "success"></div>		
		  {{ Form::open(['id'=>'updateLogo_form','class'=>'form-horizontal','method'=>'post','files'=>'true','role' => 'form','route' => 'updatelogo']) }}
              <div id="form-wizard-1" class="step">
				<div class="control-group">
				  <label class="control-label">Logo upload</label>
				  <div class="controls">
					{{ Form::file('logo_image', ['id'=>'logo_image']) }}
					@if ($errors->has('logo_image')) <p class="help-block">{{ $errors->first('logo_image') }}</p> @endif
				  </div>
				</div>
				<div class="control-group">
				  <label class="control-label"></label>
				  <div class="controls">
					<div id = "image-holder">
					<img src="{{ URL::asset('public/uploads/'.$logo_data->image) }}" alt="logo-image" height = "120px"/>
					</div>
				  </div>
				</div>
				<div class="control-group">
				  <label class="control-label">Description</label>
				  <div class="controls">
					{{ Form::textarea('description', $logo_data->description, ['placeholder' => 'Enter the description about logo','rows'=>'5']) }}
					@if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
				  </div>
				</div>
              </div>
              <div class="form-actions">
			    <a href = "{{ URL::to('admin/dashboard')}}" class = "btn btn-danger">Back</a>
				{{Form::submit('Update Logo',array('class' => 'btn btn-primary'))}}
                <div id="status"></div>
              </div>
              <div id="submitted"></div>
             {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop