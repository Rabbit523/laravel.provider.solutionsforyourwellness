@extends('admin.layouts.default_layout')
@section('content')
@section('title','edit settings')
<section id="content">
  <div id="breadcrumbs-wrapper">
      <!-- Search for small screen -->
      <div class="header-search-wrapper grey hide-on-large-only">
          <i class="mdi-action-search active"></i>
          <input type="text" name="Search" class="header-search-input z-depth-2" placeholder="Explore Materialize">
      </div>
    <div class="container">
      <div class="row">
        <div class="col s12 m12 l12">
          <h5 class="breadcrumbs-title">{{ trans('Edit settings') }}</h5>
          <ol class="breadcrumbs">
              <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
              <li><a href="{{ URL::route('settings')}}">{{ trans('Settings') }}</a></li>
              <li class="active">{{ trans('Edit settings') }}</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <!--start container-->
  <div class="container">
      <!--jqueryvalidation-->
      <div id="jqueryvalidation" class="section">
        <div class="row">
          <div class="col s12 m12 l12">
              <div class="col s12 m12 l10">
                    <div class="card-panel">
                        <h4 class="header2">Edit settings</h4>
                        <div class="row">
                            {{ Form::open(['id'=>'edit_settings','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('edit-settings',$additional_setting_data->id)]) }}
                                <div class="row">
                                  <div class="input-field col s12">
                                      <label for="field_name">Field name</label>
                                      {{ Form::text('field_name',$additional_setting_data->field_name, ['class'=>'form-control','id'=>'field_name','autocomplete'=>false,'disabled'=>'disabled']) }}
                                  </div>
                                  <div class="input-field col s12">
                                      <label for="uname">Value</label>
                                      {{ Form::text('field_value',$additional_setting_data->field_value, ['class'=>'form-control','id'=>'field_value','autocomplete'=>false]) }}
                                      @if ($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
                                  </div>

                                    <div class="input-field col s12">
                                      {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit']) }}
                                      <a href="{{URL::route('settings')}}" class="btn waves-effect waves-light right red">Back</a>
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
@stop
