@extends('admin.layouts.default_layout')
@section('content')
@section('title','Variable Creation')

<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Add Email Variables') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('email_variables_list')}}">{{ trans('Email Variables') }}</a></li>
                  <li class="active">{{ trans('Add Email Variables') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
          <!--jqueryvalidation-->
          <div id="jqueryvalidation" class="section">
            <div class="row">
              <div class="col s12 m12 l12">
                  <div class="col s12 m12 l10">
                        <div class="card-panel">
                            <h4 class="header2">Add Email Variables</h4>
                            <div class="row">
                                {{ Form::open(['class'=>'form-horizontal','id'=>'user_create_form','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('create_email_variables')]) }}
                                    <div class="row">
                                        <div class="input-field col s12">
                                            <label for="uname">Variable Name*</label>
                                            {{ Form::text('variable_name',null, ['class'=>'form-control','id'=>'templatename','placeholder' => 'Enter the template name','autocomplete'=>false]) }}
                                            @if ($errors->has('variable_name')) <p class="help-block">{{ $errors->first('variable_name') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12">
                                          <label for="crole">Description *</label>
                                          {{ Form::textarea("description",'', ['class' => 'materialize-textarea']) }}
                                          @if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createtemplate']) }}
                                          <a href="{{URL::route('email_variables_list')}}" class="btn waves-effect waves-light right red">Back</a>
                                        </div>
                                    </div>
                                {{ Form::close() }}
                            </div>
                        </div>
                    </div>
            </div>
          </div>
          </div>
        </div>
      <!--end container-->
    </section>
    <!-- END CONTENT -->
@stop
