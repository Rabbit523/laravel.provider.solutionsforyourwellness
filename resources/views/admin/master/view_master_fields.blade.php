@extends('admin.layouts.default')
@section('content')
@section('title', 'View Master')

	<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="#">{{ trans("Dashboard") }}</a>
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
								{{trans("Master")}}
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									{{trans("All Fields")}}
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							@if(Session::has('flash_success'))
							<div class="alert alert-success">
								<button type="button" class="close" data-dismiss="alert">
									<i class="ace-icon fa fa-times"></i>
								</button>
								<strong>
									<i class="ace-icon fa fa-check"></i>
									{{trans("Well done!")}}
								</strong>

								{{ Session::get('flash_success') }}
								<br>
							</div>
							@endif
							
							@if(Session::has('flash_error'))
							<div class="alert alert-danger">
								<button type="button" class="close" data-dismiss="alert">
									<i class="ace-icon fa fa-times"></i>
								</button>
								<strong>
									<i class="ace-icon fa fa-warning"></i>
									{{trans("Opps!")}}
								</strong>

								{{ Session::get('flash_error') }}
								<br>
							</div>
							@endif
							<div class="col-xs-12">
								<div class="row">
									<div class="col-xs-12">									
										<div class="clearfix">
											<div class="pull-right tableTools-container"></div>
										</div>
										<div class="table-header">
										{{trans("Master Fields")}}
										</div>

										<!-- div.table-responsive -->

										<!-- div.dataTables_borderWrap -->
										<div>
											<table id="dynamic-table" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th class="center">
															<label class="pos-rel">
																<input type="checkbox" class="ace" />
																<span class="lbl"></span>
															</label>
														</th>
														<th>{{trans("Key Type")}}</th>
														<th>{{trans("Field Type")}}</th>
														<th>{{trans("Field Name")}}</th>
														<th>{{trans("Field Placeholder")}}</th>
														<th>{{trans("Field Options")}}</th>
														<th>{{trans("Field Default")}}</th>					
														<th>
															<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
															{{trans("Created")}}
														</th>
														<th>{{trans("Status")}}</th>
														<th>{{trans("Action")}}</th>
													</tr>
												</thead>

												<tbody>
													@foreach($fieldsdata as $fields)
													<tr>
														<td class="center">
															<label class="pos-rel">
																<input type="checkbox" name="dlt[]" class="ace" />
																<span class="lbl"></span>
															</label>
														</td>
														<td><a href="{{URL::to('admin/master/'.$fields->key_type.'/add')}}">{{$fields->key_type}}</a></td>
														<td>{{$fields->field_name}}</td>
														<td>{{$fields->field_type}}</td>
														<td>{{$fields->field_placeholder}}</td>
														<td>{{$fields->field_options}}</td>
														<td>{{$fields->field_default}}</td>
														<td>{{$fields->created_at}}</td>
														<td>
															<?php if ($fields->status == 'Active'){ $status_class =  'label-success'; }else{ $status_class = 'label-warning'; } ?>
															<span class="label label-sm {{ $status_class }}">{{$fields->status}}</span>
														</td>
														<td>
															<div class="hidden-sm hidden-xs action-buttons">
																<a class="blue" href="#">
																	<i class="ace-icon fa fa-search-plus bigger-130"></i>
																</a>

																<a class="green" href="#">
																	<i class="ace-icon fa fa-pencil bigger-130"></i>
																</a>

																<a class="red" href="#">
																	<i class="ace-icon fa fa-trash-o bigger-130"></i>
																</a>
															</div>
														</td>
													</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								</div>
								
								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->
@stop