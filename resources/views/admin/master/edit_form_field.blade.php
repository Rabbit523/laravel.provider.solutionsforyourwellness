@extends('admin.layouts.default')
@section('content')
@section('title', 'Edit')
<?php 
	$segment3	=	Request::segment(2); 
	$segment4	=	Request::segment(3); 
	use App\Http\Controllers\admin\AdminMasterController;
?>
	<div class="main-content">
				<div class="main-content-inner">
					<div class="breadcrumbs ace-save-state" id="breadcrumbs">
						<ul class="breadcrumb">
							<li>
								<i class="ace-icon fa fa-home home-icon"></i>
								<a href="{{URL::route('admindashboard')}}">{{ trans("Dashboard") }}</a>
							</li>
							<li>								
								<a href="{{URL::route('viewfields',$key)}}">{{ ucwords(trans(str_replace('_',' ',$segment3))) }}</a>
							</li>
							<li class="active">{{ ucwords(trans(str_replace('_',' ',$segment4))) }}</li>
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
									{{ ucwords(trans(str_replace('_',' ',$segment4))) }}
								</small>
							</h1>
						</div><!-- /.page-header -->

						<div class="row">
							<div class="col-xs-12">								
								<!-- PAGE CONTENT BEGINS -->
								{{ Form::open(['role' => 'form','class' => 'form-horizontal', 'enctype' => 'multipart/form-data']) }}									
									@include('admin.fields.editfields',$data)									
									{{ Form::hidden("field_id",$field_id,['class' => 'col-xs-10 col-sm-5']) }}										
									<div class="clearfix form-actions">
										<div class="col-md-offset-3 col-md-9">
											<button name="submit" class="btn btn-info" type="submit">
												<i class="ace-icon fa fa-check bigger-110"></i>
												Update
											</button>

											&nbsp; &nbsp; &nbsp;
											<button class="btn" type="reset">
												<i class="ace-icon fa fa-undo bigger-110"></i>
												Reset
											</button>
											&nbsp; &nbsp; &nbsp;
											<a href="{{URL::route('viewfields',$key)}}" class="btn btn-primary" type="reset">
												<i class="ace-icon fa fa-reply bigger-110"></i>
												Back
											</a>
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

