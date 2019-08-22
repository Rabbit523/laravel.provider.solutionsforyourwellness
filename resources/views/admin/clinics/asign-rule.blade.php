@extends('admin.layouts.default_layout')
@section('content')
@section('title','Asign rule')
<?php
	$segment2	=	Request::segment(1);
	$segment3	=	Request::segment(2);
	$segment4	=	Request::segment(3);
	$segment5	=	Request::segment(4);
?>
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Clinics') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Asign rules') }}</li>
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
                            <h4 class="header2">Asign rules</h4>
                            <div class="row">
                              <?php $accepted_status = CustomHelper::CheckAsignStatus($segment3);
															//prd($accepted_status);
                                if($accepted_status){
                                  echo '<p>Rules already asigned</p>';
                                }else{
                              ?>
                                {{ Form::open(['id'=>'asign_rules','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form']) }}
                                {{ Form::hidden('clinic_id',$segment3) }}
                                    <div class="row">
                                        @foreach ($clinic_records as $key => $clinic_record)
                                        <div class="input-field col s6">
                                            <label for="name">Name</label>
                                              {{ Form::hidden('provider_id[]',$clinic_record->provider_id) }}
                                            {{ Form::text('name[]',$clinic_record->first_name.' '.$clinic_record->last_name, ['class'=>'form-control','id'=>'name','autocomplete'=>false,'disabled'=>'disabled']) }}
                                            @if ($errors->has('name')) <p class="help-block">{{ $errors->first('name') }}</p> @endif
                                        </div>
                                        <div class="input-field col s6">
                                          <p>

                                          <?php for($i=0;$i<3;$i++){ ?>
                                          <input name="rule_type_{{$key}}" value="{{$i}}" type="radio" id="primary{{ $clinic_record->id.$i }}" required>
                                            <label for="primary{{ $clinic_record->id.$i }}">
                                              @if($i == 0)
                                                {{trans('Primary')}}
                                              @elseif($i == 1)
                                                {{trans('medtech')}}
                                              @else
                                                {{trans('Other')}}
                                              @endif

                                            </label>
                                          <?php } ?>
                                        </p>
                                        </div>
                                        <div class="clearfix"></div>
                                        @endforeach;
                                        <div class="input-field col s12">
                                          {{Form::submit('Submit',['class'=>'btn waves-effect waves-light right green submit','id'=>'createclinic']) }}
                                          <a href="{{URL::route('clinics')}}" class="btn waves-effect waves-light right red">Back</a>
                                        </div>
                                    </div>
                                {{ Form::close() }}
                                <?php
                               }
                              ?>
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
