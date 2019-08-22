@extends('admin.layouts.items_layout') 
@section('content')
@section('title','Item Creation')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/admin/dashboard') }}" title="Go to Dashboard" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="{{ url('/admin/category-list') }}">Category List</a> <a href="javascript:void(0);" class="current">Create Category</a> </div>
  </div>
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title"> <span class="icon"> <i class="icon-pencil"></i> </span>
            <h5>Enter the details to create item</h5>
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
		  {{ Form::open(['id'=>'createcategory_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' => 'admin/create-category']) }}
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
              <div id="form-wizard-1" class="step">
                <div class="control-group">
                  <label class="control-label">Category Name</label>
                  <div class="controls">
                    {{ Form::text('category_name',null, ['id'=>'category_name','placeholder' => 'Enter the Category Name. ','autocomplete'=>false]) }}
					@if ($errors->has('category_name')) <p class="help-block">{{ $errors->first('category_name') }}</p> @endif
                  </div>
                </div>
				<div class="control-group">
				 <label class="control-label">Description</label>
					<div class="controls">
					{{ Form::textarea('description',null, ['class'=>'span11','id'=>'description','autocomplete'=>false,'placeholder'=>'Enter description']) }}
					@if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
					</div>
				</div>
              <div class="form-actions">
			    <a href = "{{ URL::to('admin/category-list')}}" class = "btn btn-danger">Back</a>
				{{Form::submit('Create Category',array('class' => 'btn btn-primary'))}}
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
		<!--Multiple Image Upload Start Modal -->
			  <div class="modal fade" id="ItemPicture_modal" role="dialog">
				<div class="modal-dialog modal-lg">
				  <div class="modal-content">
					<div class="modal-header">
					  <button type="button" class="close" data-dismiss="modal">&times;</button>
					  <h4 class="modal-title">Item Pictures</h4>
					</div>
					<div class="modal-body" style="padding: 7px;">
						<div class="dv_upld">
							<!-- The file upload form used as target for the file upload widget -->
							<form id="fileupload" name = "fileupload" action="" method="POST" enctype="multipart/form-data">
								<!-- Redirect browsers with JavaScript disabled to the origin page -->
								<noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
								<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
								<div class="row fileupload-buttonbar" style="margin-left:0px;">
									<div class="col-lg-7">
										<!-- The fileinput-button span is used to style the file input field as button -->
										<span class="btn btn-success fileinput-button">
											<i class="glyphicon glyphicon-plus"></i>
											<span>Add files...</span>
											 <input type="file" name="files[]" multiple>
											 <input name="item_id" id = "item_id" type="hidden" value="">
											 <input type="hidden" name="_token" value="{{ csrf_token() }}">
										</span>
										<button type="submit" class="btn btn-primary start">
											<i class="glyphicon glyphicon-upload"></i>
											<span>Start upload</span>
										</button>
										<button type="reset" class="btn btn-warning cancel">
											<i class="glyphicon glyphicon-ban-circle"></i>
											<span>Cancel upload</span>
										</button>
								   
									</div>
									<!-- The global progress state -->
									<div class="col-lg-5 fileupload-progress fade">
										<!-- The global progress bar -->
										<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
											<div class="progress-bar progress-bar-success" style="width:0%;"></div>
										</div>
										<!-- The extended global progress state -->
										<div class="progress-extended">&nbsp;</div>
									</div>
								</div>
								<!-- The table listing the files available for upload/download -->
								<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
							</form>
							<!-- The template to display files available for upload -->
							<script id="template-upload" type="text/x-tmpl">
							{% for (var i=0, file; file=o.files[i]; i++) { %}
								<tr class="template-upload fade">
									<td align="center">
										<span class="preview"></span>
									
									</td>
									<td>
										<p class="size">Processing...</p>
										<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
									</td>
									<td>
									<input name="user[]" type="hidden" value="456789">
									<input name="item[]" type="hidden" value="13465">

										{% if (!i && !o.options.autoUpload) { %}
											<button class="btn btn-primary start single" disabled>
												<i class="glyphicon glyphicon-upload"></i>
												<span>Start</span>
											</button>
										{% } %}
										{% if (!i) { %}
											<button class="btn btn-warning cancel">
												<i class="glyphicon glyphicon-ban-circle"></i>
												<span>Cancel</span>
											</button>
										{% } %}
									</td>
								</tr>
							{% } %}
							</script>
							<!-- The template to display files available for download -->
							<script id="template-download" type="text/x-tmpl">
							{% for (var i=0, file; file=o.files[i]; i++) { %}
								<tr class="template-download fade">
									<td>
										<span class="preview">
											{% if (file.thumbnailUrl) { %}
												<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
											{% } %}
										</span>
									</td>
									<td>
										<p class="name">
											{% if (file.url) { %}
												<a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" >{%=file.name%}</a>
											{% } else { %}
												<span>{%=file.name%}</span>
											{% } %}
										</p>
										{% if (file.error) { %}
											<div><span class="label label-danger">Error</span> {%=file.error%}</div>
										{% } %}
									</td>
									<td>
										<span class="size">{%=o.formatFileSize(file.size)%}</span>
									</td>
									<td>
										{% if (file.deleteUrl) { %}
											<button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
												<i class="glyphicon glyphicon-trash"></i>
												<span>Delete</span>
											</button>
											<input type="checkbox" name="delete" value="1" class="toggle">
										{% } else { %}
											<button class="btn btn-warning cancel">
												<i class="glyphicon glyphicon-ban-circle"></i>
												<span>Cancel</span>
											</button>
										{% } %}
									</td>
								</tr>
							{% } %}
							</script>
							<!---->
						</div>
						<div id="image_div"></div>
					</div>
					<div class="modal-footer">
					  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				  </div>
				</div>
			</div>
			<!--End Modal -->
@stop