@extends('admin.layouts.default_layout')
@section('content')
@section('title','edit-city')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('City') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('cities')}}">{{ trans('Cities') }}</a></li>
                  <li class="active">{{ trans('Edit-city') }}</li>
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
                            <h4 class="header2">Edit city</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'edit_city_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('edit_city',$city->id)]) }}
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <label for="name">City name*</label>
                                            {{ Form::text('city_name',$city->city_name, ['class'=>'form-control','id'=>'city_name','autocomplete'=>false]) }}
					                        @if ($errors->has('city_name')) <p class="help-block">{{ $errors->first('city_name') }}</p> @endif
                                        </div>
                                    </div>
									<div class="row">
										 <div class="input-field col s6">
                                            <label for="uname">Description</label>
                                            {{ Form::text('description',$city->description, ['class'=>'form-control','id'=>'description','autocomplete'=>false]) }}
                                        </div>
									</div>
                                      <div class="input-field col s12">
                                        </div>
                                        <div class="input-field col s12">
                                          {{Form::submit('Update',['class'=>'btn waves-effect waves-light right green submit','id'=>'createclinic']) }}
                                          <a href="{{URL::route('cities')}}" class="btn waves-effect waves-light right red">Back</a>
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
