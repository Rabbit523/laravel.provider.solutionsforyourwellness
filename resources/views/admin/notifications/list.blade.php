@extends('admin.layouts.default_layout')
@section('content')
@section('title','Nofication')
<!-- Content Wrapper. Contains page content -->
<section id="content">
        <!--start container-->
        <div class="container">
          <p class="caption">Admin nofications page.</p>
          <div class="divider"></div>
          <!--Basic Card-->
          <div id="basic-card" class="section">
            <h4 class="header">Notifications</h4>
			@if(!empty($notifications))
				@foreach($notifications as $notification)
				<div class="row">
				  <div class="col s12 m8 l9">
					<div class="row">
					  <div class="col s12 m8 l9">
						<div class="card  light-blue">
						  <div class="card-content white-text">
							<span class="card-title">{{ ucwords(str_replace("_", " ", $notification->notification_type)) }}</span>
							<h6>{{ CustomHelper::time_elapsed_string($notification->created_at) }}</h6//>
							<p>{{ $notification->message }}</p>
						  </div>
						</div>
					  </div>
					</div>
				  </div>
				</div>
				@endforeach
			@else
			<h3>Notifications not available</h3>
			@endif
          </div>
        </div>
        <!--end container-->

      </section>
      
@stop
