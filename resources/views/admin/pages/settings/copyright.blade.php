@extends('admin.layouts.pages_layout')
@section('content')
@section('title','Settings')
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        General Form Elements
        <small>Preview</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="#">Forms</a></li>
        <li class="active">General Elements</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <!-- right column -->
        <div class="col-md-10">
          <!-- Horizontal Form -->
          <div class="box box-info">
            <div class="box-header with-border">
              <h3 class="box-title">Copyright update</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
			{{ Form::open(['id'=>'edit_copyright_form','files'=>'true','method'=>'post','class' => 'form-horizontal', 'url' =>URL::route('admincopyrightedit')]) }}
            <form class="form-horizontal">
              <div class="box-body">
                <div class="form-group">
                  <label for="first_name" class="col-sm-2 control-label">Copyright value:</label>
                  <div class="col-sm-10">
          					 {{ Form::text('copyright_value',$copyright->field_value, ['class'=>'form-control','id'=>'copyright_value','placeholder' => 'Enter copyright text. ','autocomplete'=>false]) }}
          					 @if ($errors->has('copyright_value')) <p class="help-block">{{ $errors->first('copyright_value') }}</p> @endif
                  </div>
                </div>
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                {{Form::submit('Update',['class'=>'btn btn-success']) }}
                <a href="{{URL::route('dashboard')}}" class="btn btn-danger">Back</a>
              </div>
              <!-- /.box-footer -->
            {{ Form::close() }}
          </div>
          <!-- /.box -->
        </div>
        <!--/.col (right) -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@stop
