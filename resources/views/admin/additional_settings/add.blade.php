@extends('admin.layouts.default_layout')
@section('content')
@section('title','add certificate')
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
              <h5 class="breadcrumbs-title">{{ trans('Certificates') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('certifications')}}">{{ trans('Certificates') }}</a></li>
                  <li class="active">{{ trans('Add Certificate') }}</li>
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
                            <h4 class="header2">Add certificate</h4>
                            <div class="row">
                                {{ Form::open(['id'=>'add_certificate_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('add-certificate')]) }}
                                    <div class="row">
                                      <div class="col s12">
                                        <label for="crole">Select Provider *</label>
                                        <select class="browser-default" name="user_id" id="user_id" data-error=".errorTxt6">
                                          <option value="" disabled selected>Select Provider</option>
                                          @foreach ($providers as $provider)
                                            <option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                          @endforeach
                                        </select>
                                        @if ($errors->has('user_id')) <p class="help-block">{{ $errors->first('user_id') }}</p> @endif
                                      </div>
                                      <script>
                                          var currentGender = null;
                                          for(var i=0; i!=document.querySelector("#user_id").querySelectorAll("option").length; i++)
                                          {
                                              currentGender = document.querySelector("#user_id").querySelectorAll("option")[i];
                                              if(currentGender.getAttribute("value") == "{{ old("user_id") }}")
                                              {
                                                  currentGender.setAttribute("selected","selected");
                                              }
                                          }
                                      </script>

                                      <div class="input-field col s12">
                                          <label for="uname">Subject*</label>
                                          {{ Form::text('subject',null, ['class'=>'form-control','id'=>'subject','autocomplete'=>false]) }}
                                          @if ($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
                                      </div>
                                      <div class="input-field col s12">
                                        <label for="crole">Description *</label>
                                        {{ Form::textarea('description', null, ['class' => 'materialize-textarea validate','id' => 'description']) }}
                                        @if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
                                      </div>

                                        <div class="input-field col s12">
                                            <label for="Certificates">Certificate* ( jpg, png, doc, pdf )</label><br><br>
                                            {{ Form::file('file', array('class'=>'form-control','id'=>'file')) }}
  					                                @if ($errors->has('file')) <p class="help-block">{{ $errors->first('file') }}</p> @endif
                                        </div>

                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'addcertificate']) }}
                                          <a href="{{URL::route('certifications')}}" class="btn waves-effect waves-light right red">Back</a>
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
