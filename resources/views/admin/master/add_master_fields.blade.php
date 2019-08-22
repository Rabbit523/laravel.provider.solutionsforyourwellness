@extends('admin.layouts.default')
@section('content')
@section('title', 'Master')

	<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="{{URL::to('admin/dashboard')}}">{{ trans("Dashboard") }}</a>
							</li>
							<li class="active">{{ trans("Master") }}</li>
						</ul><!-- /.breadcrumb -->

						<div class="nav-search" id="nav-search">
							<form class="form-search">
								<span class="input-icon">
									<input type="text" placeholder="Search ..." class="nav-search-input" id="nav-search-input" autocomplete="off" />
									<i class="ace-icon fa fa-search nav-search-icon"></i>
								</span>
							</form>
						</div><!-- /.nav-search -->
					</div>

					<div class="page-content">
						
						<div class="page-header">
							<h1>
								{{ trans("Master") }}
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									{{ trans("Add Fields") }}
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">
								<!-- PAGE CONTENT BEGINS -->
								{{ Form::open(['role' => 'form','url' => URL::route('addmaster'),'class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) }}
								
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ trans("Key Type: *")}} </label>

										<div class="col-sm-9">
											{{ Form::text('key_type', null, ['placeholder' => 'Enter Field Type', 'class' => 'col-xs-10 col-sm-5']) }}
											<p class="text-danger"><?php echo $errors->first('key_type'); ?></p>
										</div>
									</div>

									<div class="space-4"></div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ trans("Field Types: *") }} </label>

										<div class="col-sm-9">
											<select name="field_type" id="slct_field" class="col-xs-10 col-sm-5">
												<option value="">{{ trans("Select") }}</option>
												@foreach($fieldsType as $fields)
													  <option value="{{ $fields->id }}">{{ $fields->name }}</option>
												@endforeach 
											</select>											
											<p class="text-danger"><?php echo $errors->first('field_type'); ?></p>
										</div>
									</div>
									
									<div class="space-4"></div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1">  {{ trans("Field Label: *") }} </label>

										<div class="col-sm-9">
											{{ Form::text('field_lable', null, ['placeholder' => 'Enter Field Label', 'class' => 'col-xs-10 col-sm-5']) }}
											<p class="text-danger"><?php echo $errors->first('field_lable'); ?></p>											
										</div>
									</div>

									<div class="space-4"></div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1">  {{ trans("Field Name: *") }} </label>

										<div class="col-sm-9">
											{{ Form::text('field_name', null, ['placeholder' => 'Enter Field Name', 'class' => 'col-xs-10 col-sm-5']) }}
											<p class="text-danger"><?php echo $errors->first('field_name'); ?></p>											
										</div>
									</div>

									<div class="space-4"></div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ trans("Field Placeholder:") }} </label>

										<div class="col-sm-9">
											{{ Form::text('field_placeholder', null, ['placeholder' => 'Enter Field Placeholder', 'class' => 'col-xs-10 col-sm-5']) }}
											<p class="text-danger"><?php echo $errors->first('field_placeholder'); ?></p>
										</div>
									</div>
									<div class="space-4"></div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ trans("Order Number:") }} </label>

										<div class="col-sm-9">
											{{ Form::text('order_by', 0, ['placeholder' => 'Enter Order Number', 'class' => 'col-xs-10 col-sm-5']) }}
											<p class="text-danger"><?php echo $errors->first('order_by'); ?></p>
										</div>
									</div>
								
									<div class="space-4"></div>
									<div style="display:none;" id="option_fields">
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{trans("Field Options:")}} </label>

										<div class="col-sm-9">
											{{ Form::text('field_options', null, ['placeholder' => 'Enter Field Options(In json formate)', 'class' => 'col-xs-10 col-sm-5']) }}
											<p class="text-danger"><?php echo $errors->first('field_options'); ?></p>
										</div>
									</div>

									<div class="space-4"></div>

									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{trans("Default Field:")}} </label>

										<div class="col-sm-9">
											{{ Form::text('field_default', null, ['placeholder' => 'Enter Default Field Value', 'class' => 'col-xs-10 col-sm-5']) }}
											<p class="text-danger"><?php echo $errors->first('field_default'); ?></p>
										</div>
									</div>
									
									<div class="space-4"></div>
									</div>
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{ trans("Is Required: *") }} </label>

										<div class="col-sm-9">
											{{ Form::select('is_required', [
											   'No' => 'No',
											   'Yes' => 'Yes'],'',['class' => 'col-xs-10 col-sm-5', 'id' => 'is_required']
											) }}										
											<p class="text-danger"><?php echo $errors->first('is_required'); ?></p>
										</div>
									</div>											
									<div class="space-4"></div>
									<div style="display:none;" id="required_rules">
									<div class="form-group">
										<label class="col-sm-3 control-label no-padding-right" for="form-field-1"> {{trans("Required Rules: *")}} </label>

										<div class="col-sm-9">
											{{ Form::text('required_rules', null, ['placeholder' => 'Enter Required Rules', 'class' => 'col-xs-10 col-sm-5', 'id' => 'text_required_rules']) }}
											<p class="text-danger"><?php echo $errors->first('required_rules'); ?></p>
										</div>
									</div>
									
									<div class="space-4"></div>
									</div>
									<div class="clearfix form-actions">
										<div class="col-md-offset-3 col-md-9">
											<button name="submit" class="btn btn-info" type="submit">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Submit
											</button>

											&nbsp; &nbsp; &nbsp;
											<button class="btn" type="reset">
												<i class="ace-icon fa fa-undo bigger-110"></i>
												Reset
											</button>
										</div>
									</div>										
								{{ Form::close() }}

								<div class="hr hr-18 dotted hr-double"></div>
								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->
@stop

