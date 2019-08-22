@extends('admin.layouts.default_layout')
@section('content')
@section('title','provider setting')
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
          <!-- Search for small screen -->
          <div class="header-search-wrapper grey hide-on-large-only">
              <i class="mdi-action-search active"></i>
              <input type="text" name="Search" class="header-search-input z-depth-2" placeholder="Explore Materialize">
          </div>
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Providers') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('providers')}}">{{ trans('Providers') }}</a></li>
                  <li class="active">{{ trans('Provider setting') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
        <div id="accordion" class="section">
          <div id="card-alert" class="card green success" style="display:none">
              <div class="card-content white-text">
                <p></p>
              </div>
              <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
          </div>
          <div id="card-alert" class="card red error" style="display:none">
              <div class="card-content white-text">
                <p></p>
              </div>
              <button type="button" class="close white-text" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
          </div>
          {{ Form::open(['id'=>'provider_setting_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('edit-clinic',$provider->id)]) }}
          <div class="col s12 m8 l9">
                <ul class="collapsible collapsible-accordion" data-collapsible="accordion">
                  <li>
                    <div class="collapsible-header active">Edit provider rate</div>
                    <div class="collapsible-body">
                      <div class="row">
                        <div class="input-field col s8 offset-s1">
                            <label for="rate">provider rate*</label>
                            {{ Form::text('rate',$provider->rate, ['class'=>'form-control','id'=>'rate','autocomplete'=>false]) }}
                            @if ($errors->has('rate')) <p class="help-block">{{ $errors->first('rate') }}</p> @endif
                        </div>
                        <div class="input-field col s2 ">
                            <button type="button" data-id="{{ $provider->id }}" class="btn waves-effect waves-light right green change_rate" >Update</button>
                        </div>
                    </div>
                  </div>
                  </li>
                  <li>
                    <div class="collapsible-header ">Edit mileage info per clinic</div>
                    <div class="collapsible-body">
                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="collapsible-header ">Edit drive time per clinic</div>
                    <div class="collapsible-body">
                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                      </p>
                    </div>
                  </li>
                  <li>
                    <div class="collapsible-header ">Edit time card per clinic</div>
                    <div class="collapsible-body">
                      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                      </p>
                    </div>
                  </li>
                </ul>
              </div>
              {{ Form::close() }}
          </div>
        </div>
      <!--end container-->
    </section>
    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
@stop
