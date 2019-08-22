@extends('admin.layouts.default_layout')
@section('content')
@section('title','certificate edit')
<section id="content">
  <div id="breadcrumbs-wrapper">
    <div class="container">
      <div class="row">
        <div class="col s12 m12 l12">
          <h5 class="breadcrumbs-title">{{ trans('Certificates') }}</h5>
          <ol class="breadcrumbs">
              <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
              <li><a href="{{ URL::route('certifications')}}">{{ trans('Certificates') }}</a></li>
              <li class="active">{{ trans('Edit certificate') }}</li>
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
                        <h4 class="header2">Edit certificate</h4>
                        <div class="row">
                            {{ Form::open(['id'=>'edit_certificate_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('edit-certificates',$certificate->certificate_id)]) }}
                                <div class="row">
                                  <div class="col s12">
                                    <label for="crole"> Provider *</label>
                                    <select class="browser-default" name="user_id" id="user_id" data-error=".errorTxt6" disabled="">
                                      @foreach ($providers as $provider)
                                        <option value="{{ $provider->id }}" @if($provider->id==$certificate->user_id){{ trans('selected')}} @endif>{{ $provider->first_name }} {{ $provider->last_name }}</option>
                                      @endforeach
                                    </select><br>
                                    @if ($errors->has('user_id')) <p class="help-block">{{ $errors->first('user_id') }}</p> @endif
                                  </div>
                                  <div class="input-field col s12">
                                      <label for="uname">Subject*</label>
                                      {{ Form::text('subject',$certificate->subject, ['class'=>'form-control','id'=>'subject','autocomplete'=>false]) }}
                                      @if ($errors->has('subject')) <p class="help-block">{{ $errors->first('subject') }}</p> @endif
                                  </div>
                                  <div class="input-field col s12">
                                    <label for="crole">Description *</label>
                                    {{ Form::textarea('description', $certificate->description, ['class' => 'materialize-textarea validate','id' => 'description']) }}
                                    @if ($errors->has('description')) <p class="help-block">{{ $errors->first('description') }}</p> @endif
                                  </div>

                                    <div class="input-field col s12">
                                        <label for="Certificates">Certificate* ( jpg, png, doc, pdf )</label><br><br>
                                        {{ Form::file('file', array('class'=>'form-control','id'=>'file')) }}
                                        <p><a href="{{ WEBSITE_UPLOADS_URL }}certificates/{{$certificate->file}}" target="_blank">{{  $certificate->file }}</a></p>
                                        @if ($errors->has('file')) <p class="help-block">{{ $errors->first('file') }}</p> @endif
                                    </div>

                                    <div class="input-field col s12">
                                      {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit']) }}
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
@stop
