@extends('admin.layouts.default_layout')
@section('content')
@section('title','add admin')
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
              <h5 class="breadcrumbs-title">{{ trans('Admins') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('admins')}}">{{ trans('Admins') }}</a></li>
                  <li class="active">{{ trans('Add admin') }}</li>
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
                            <h4 class="header2">Add admin</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'add_admin_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('add-admin')]) }}
                                    <div class="row">
                                        <div class="input-field col s6">
                                            <label for="first_name">First name*</label>
                                            {{ Form::text('first_name',null, ['class'=>'form-control','id'=>'first_name','autocomplete'=>false]) }}
					                                  @if ($errors->has('first_name')) <p class="help-block">{{ $errors->first('first_name') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                            <label for="last_name">Last name*</label>
                                            {{ Form::text('last_name',null, ['class'=>'form-control','id'=>'last_name','autocomplete'=>false]) }}
  					                                @if ($errors->has('last_name')) <p class="help-block">{{ $errors->first('last_name') }}</p> @endif
                                        </div>

                                        <div class="input-field col s6">
                                          <label for="email">E-mail *</label>
                                          {{ Form::text('email',null, ['class'=>'form-control','id'=>'email','autocomplete'=>false]) }}
  					                              @if ($errors->has('email')) <p class="help-block">{{ $errors->first('email') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                          <label for="phone">Phone no *</label>
                                          {{ Form::text('phone',null, ['class'=>'form-control','id'=>'phone','autocomplete'=>false]) }}
					                                @if ($errors->has('phone')) <p class="help-block">{{ $errors->first('phone') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                          <label for="password">Password *</label>
                                          {{ Form::password('password',['class'=>'form-control','id'=>'password','autocomplete'=>false]) }}
                                          @if ($errors->has('password')) <p class="help-block">{{ $errors->first('password') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                          <label for="confirm_password">Confirm Password *</label>
                                          {{ Form::password('confirm_password',['class'=>'form-control','id'=>'confirm_password','autocomplete'=>false]) }}
				                                  @if ($errors->has('confirm_password')) <p class="help-block">{{ $errors->first('confirm_password') }}</p> @endif
                                        </div>
                                        <div class="input-field col s12">
                                          <label for="security_pin">Security pin (4 digits) *</label>
                                          {{ Form::password('security_pin',null, ['class'=>'form-control','id'=>'security_pin','autocomplete'=>false]) }}
					                                @if ($errors->has('security_pin')) <p class="help-block">{{ $errors->first('security_pin') }}</p> @endif
                                        </div>

                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createadmin']) }}
                                          <a href="{{URL::route('admins')}}" class="btn waves-effect waves-light right red">Back</a>
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
