@extends('admin.layouts.default')
@section('content')
@section('title', 'List')
<?php $segment3	=	Request::segment(2);  ?>
	<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="#">{{ trans("Dashboard") }}</a>
							</li>
							<li class="active">{{ ucwords(trans(str_replace('_',' ',$segment3))) }}</li>
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
								{{ ucwords(trans(str_replace('_',' ',$segment3))) }}
								<small>
									<i class="ace-icon fa fa-angle-double-right"></i>
									{{ ucwords(trans('List')) }}
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
								<div class="row">	{{ Form::open(['id'=>'table_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
									<div class="col-xs-12">											
											<a href="javascript:void(0);" id="dlt_dialog" class="btn btn-danger btn-sm "><i class="fa fa-trash-o" aria-hidden="true"></i> Delete All</a>
											<a href="{{URL::route('addfields',$key)}}" class="btn btn-primary btn-sm "><i class="fa fa-plus" aria-hidden="true"></i> Add New</a>
											<a href="{{URL::route('downloadexcel', ['key' => $key, 'type' => 'xls'])}}" class="btn btn-success btn-sm"><i class="fa fa-download" aria-hidden="true"></i> Download Excel xls</a>
											<a href="{{URL::route('downloadexcel', ['key' => $key, 'type' => 'xlsx'])}}" class="btn btn-warning btn-sm"><i class="fa fa-download" aria-hidden="true"></i> Download Excel xlsx</a>
											<a href="{{URL::route('downloadexcel', ['key' => $key, 'type' => 'csv'])}}" class="btn btn-pink btn-sm"><i class="fa fa-download" aria-hidden="true"></i> Download CSV</a>
											
											<a href="javascript:void(0);" class="btn btn-info btn-sm" id="import_excel"><i class="fa fa-upload" aria-hidden="true"></i> Import CSV or Excel File</a>
											
										<div class="clearfix">
											<div class="pull-right tableTools-container"></div>
										</div>
										<div class="table-header">
										{{trans("List")}}
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
														@foreach($fieldsColumn as $cloumn)
															<th>{{$cloumn->field_lable}}</th>	
														@endforeach
														<th>{{trans("Status")}}</th>
														<th>
															<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
															{{trans("Created")}}
														</th>
														<th>
															<i class="ace-icon fa fa-clock-o bigger-110 hidden-480"></i>
															{{trans("Updated")}}
														</th>
														<th>{{trans("Action")}}</th>
													</tr>
												</thead>
												<tbody>
													@foreach($fieldsData as $fields)
													<tr>
														 @foreach ($fields as $column_name=>$column_value)
															@if ($column_name=='id')
															<td class="center">
																<label class="pos-rel">
																	<input type="checkbox" name="chk_ids[]" value = "{{$column_value}}"  class="ace allcheck" />
																	<span class="lbl"></span>
																</label>
															</td>
															@else
																<td class="center">
																@if($column_name == 'status' && $column_value == 'Active')
																		<span class="label label-sm label-success">{{$column_value}} </span>
																@elseif($column_name == 'status' && $column_value == 'Inactive')
																		<span class="label label-sm label-warning">{{$column_value}} </span>
																@else
																	<?php  
																		$clm_value = explode('.', $column_value);
																		$last_val	=	end($clm_value);
																		if($last_val == 'jpg' || $last_val == 'jpeg' || $last_val == 'png' || $last_val == 'gif' || $last_val == 'psd' || $last_val == 'bmp' || $last_val == 'tiff'){
																			echo "<img style='height:60px;' src='".URL::asset('public/uploads/team/'.$column_value)."' >"; }else{ echo $column_value; }
																	?>
																@endif
																</td>
															@endif
														@endforeach
														<td>
															<div class="hidden-sm hidden-xs action-buttons">
															
																@if($fields['status'] == 'Active') 
																	<a  title="Inactive" data-toggle="tooltip" class="blue" href="{{URL::route('activeinactive', ['key' => $key, 'id' => $fields['id']])}}">
																		<i class="ace-icon glyphicon glyphicon-ok"></i>
																	</a>
																@else
																	<a title="Active" data-toggle="tooltip" class="blue" href="{{URL::route('activeinactive', ['key' => $key, 'id' => $fields['id']])}}">
																		<i class="ace-icon glyphicon glyphicon-remove"></i>
																	</a>
																@endif
																<a title="edit" data-toggle="tooltip" class="green" href="{{URL::route('editfields', ['key' => $key, 'id' => $fields['id']])}}">
																	<i class="ace-icon fa fa-pencil bigger-130"></i>
																</a>

																<a title="delete" data-toggle="tooltip" class="red delete_item_btn" href="javascript:void(0);" data-url = "{{URL::route('deletefields', ['key' => $key, 'id' => $fields['id']])}}">
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
									<input type="hidden" value="{{$key}}" name="key_val" id="key_val">
									{{ Form::close() }}
								</div>
								<!-- PAGE CONTENT ENDS -->
							</div><!-- /.col -->
						</div><!-- /.row -->
					</div><!-- /.page-content -->
				</div>
			</div><!-- /.main-content -->			
@stop