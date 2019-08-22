@extends('admin.layouts.pages_layout') 
@section('content')
@section('title','Category List')
<div id="content">
  <div id="content-header">
    <div id="breadcrumb"> <a href="{{ url('/admin/dashboard') }}" title="Go to Dashboard" class="tip-bottom"><i class="icon-home"></i> Dashboard</a> <a href="javascript:void();">Category List</a></div>
  </div>
  <div class="container-fluid"><hr>
  {{Form::open(['id'=>'category_details_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
  <a href = "{{ URL::to('admin/create-category')}}" class = "btn btn-primary">Create Category</a>
  <a href = "{{ URL::to('admin/dashboard')}}" class = "btn btn-danger">Back</a>
  {{Form::button('Delete All',array('class' => 'btn btn-warning','id'=>'delete_all_category'))}}<hr>
    <div class="row-fluid">
      <div class="span12">
				@if(Session::has('flash_message_error'))
					<div class="alert alert-error">{{Session::get('flash_message_error')}}</div>
				@endif
				 @if(Session::has('flash_message_success'))
					<div class="alert alert-success">{{Session::get('flash_message_success')}}</div>
				@endif
		  <div id = "success"></div>		
		  <div id = "error"></div>		
          <div class="widget-box">
          <div class="widget-title"> <span class="icon"><i class="icon-th"></i></span>
            <h5>{{trans("Items List")}}</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered" id = "admin_category_data_table">
              <thead>
                <tr>
				  <th>{{Form::checkbox('selectall', trans("Select All"))}}</th>
				  <th>{{trans("Category Name")}}</th>		
                  <th>{{trans("Description")}}</th>
                  <th>{{trans("Status")}}</th>
                  <th>{{trans("Action")}}</th>
                </tr>
              </thead>
            <tbody>
				@foreach ($categorydetails as $category)
				  <tr id = "cat_row_{{ $category->id }}">
					<td>{{Form::checkbox('checkbox[]',$category->id)}}</td>
					<td>{{$category->category_name}}</td>
					<td>{{$category->description}}</td>
					<td>{{$category->status}}</td>
					<td>
					<div class="fr">
						<a href="edit-category/{{$category->id}}" class="btn btn-primary btn-mini">Edit</a>
						@if($category->status == 'Active') 
						<a href="active-category/{{$category->id}}" class="btn btn-warning btn-mini">Deactive</a>	
						@else
							<a href="active-category/{{$category->id}}" class="btn btn-success btn-mini">Active</a>
						@endif
						<a href="javascript:void();" data-id = "{{$category->id}}" class="btn btn-danger btn-mini category_delete_btn">Delete</a>
					</div>
					</td>
				  </tr>
					@endforeach
				</tbody>
            </table>
          </div>
        </div>                                                                                                       
      </div>
    </div>
	{{ Form::close() }}
  </div>
</div>
@stop