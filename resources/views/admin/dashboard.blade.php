
@extends('admin.layouts.default_layout')
@section('content')
@section('title','Homepage')
<section id="content">
    <!--start container-->
    <div class="container">
      <h1>Welcome to Dashboard</h1>
      <div class="row">
        <div class="col s12 m6 l3">
            <div class="card">
                <div class="card-content  green white-text">
                    <p class="card-stats-title"><i class="mdi-social-group-add"></i> Total Providers</p>
                    <h4 class="card-stats-number">{{ DB::table('users')->where('role_id',0)->count() }}</h4>
                    <p class="card-stats-compare">
                    <?php
                      if (strpos($provider_diffrence, '-') !== false) {
                    ?>
                    <i class="mdi-hardware-keyboard-arrow-down"></i>
                    <?php } else { ?>
                    <i class="mdi-hardware-keyboard-arrow-up"></i>
                    <?php } ?>
                     {{ round($provider_diffrence) }} % <span class="green-text text-lighten-5">from last month</span>
                    </p>
                </div>
                <div class="card-action  green darken-2">
                    <div id="provider-bar" class="center-align"></div>
                </div>
            </div>
        </div>
        <div class="col s12 m6 l3">
            <div class="card">
                <div class="card-content purple white-text">
                    <p class="card-stats-title"><i class="mdi-editor-insert-drive-file"></i>Certificates uploaded</p>
                    <h4 class="card-stats-number">{{ DB::table('certifications')->count() }}</h4>
                       <p class="card-stats-compare">
                        <?php
                          if (strpos($certificate_diffrence, '-') !== false) {
                        ?>
                        <i class="mdi-hardware-keyboard-arrow-down"></i>
                        <?php } else { ?>
                        <i class="mdi-hardware-keyboard-arrow-up"></i>
                        <?php } ?>
                         {{ round($certificate_diffrence) }} % <span class="green-text text-lighten-5">from last month</span>
                        </p>
                </div>
                <div class="card-action  purple darken-2">
                    <div id="certificate-bar" class="center-align"></div>
                </div>
            </div>
        </div>
        <div class="col s12 m6 l3">
            <div class="card">
                <div class="card-content blue-grey white-text">
                    <p class="card-stats-title"><i class="mdi-action-trending-up"></i> Total admin</p>
                    <h4 class="card-stats-number">{{ DB::table('users')->where('role_id',2)->count() }}</h4>
                      <p class="card-stats-compare">
					  <?php
                      if (strpos($admin_diffrence, '-') !== false) {
						?>
						<i class="mdi-hardware-keyboard-arrow-down"></i>
						<?php } else { ?>
						<i class="mdi-hardware-keyboard-arrow-up"></i>
						<?php } ?>
						 {{ round($admin_diffrence) }} % <span class="green-text text-lighten-5">from last month</span>
						</p>
                    </p>
                </div>
                <div class="card-action  blue-grey darken-2">
                    <div id="admin-bar" class="center-align"></div>
                </div>
            </div>
        </div>
        <div class="col s12 m6 l3">
            <div class="card">
                <div class="card-content deep-purple white-text">
                    <p class="card-stats-title"><i class="mdi-action-settings-voice"></i> Total announcements</p>
                    <h4 class="card-stats-number">{{ DB::table('announcement')->where('status',1)->count() }}</h4>
                       <p class="card-stats-compare">
                        <?php
                          if (strpos($announcement_difference, '-') !== false) {
                        ?>
                        <i class="mdi-hardware-keyboard-arrow-down"></i>
                        <?php } else { ?>
                        <i class="mdi-hardware-keyboard-arrow-up"></i>
                        <?php } ?>
                         {{ round($announcement_difference) }} % <span class="green-text text-lighten-5">from last month</span>
                        </p>
                </div>
                <div class="card-action  deep-purple darken-2">
                    <div id="announcement-bar" class="center-align"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="work-collections" class="seaction">
      <div class="row">
        <div class="col s12 m12 l4">
          <ul id="projects-collection" class="collection" style="overflow-y:scroll;max-height:400px">
            <li class="collection-item avatar">
              <i class="mdi-alert-warning circle red"></i>
              <span class="collection-header">Late clockout</span>
              <p>providers</p>
            </li>
            <?php if(isset($late_clockout_data)){
			?>
              @foreach($late_clockout_data as $late_clockouts)
			  <a href="{{ URL::route('edit_clinic_status',[$late_clockouts->id,$late_clockouts->clinic_id]) }}">
				  <li class="collection-item">
					<div class="row">
					  <div class="col s6">
						<p class="collections-title">{{ $late_clockouts->first_name.' '.$late_clockouts->last_name }}</p>
						<p class="collections-content">clock time was: {{ $late_clockouts->date}} {{ $late_clockouts->end_time }}</p>
					  </div>
					  <div class="col s6">
						<p class="collections-content">clockout at :<br> {{ $late_clockouts->clock_out }}</p>
					  </div>

					</div>
				  </li>
			  </a>
                @endforeach
              <?php } else{  ?>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s12">
                          <p class="collections-title">No record found</p>
                        </div>
                    </div>
                </li>
              <?php } ?>

          </ul>
        </div>
        <div class="col s12 m12 l4">
            <ul id="issues-collection" class="collection" style="overflow-y:scroll;max-height:400px">
              <li class="collection-item avatar">
                  <i class="mdi-communication-location-off green circle"></i>
                  <span class="collection-header">Providers with</span>
                  <p>no or wrong location</p>
              </li>
              <?php if(isset($empty_location_users) && !empty($empty_location_users)){ ?>
              @foreach($empty_location_users as $empty_location_user)
			  <a href="{{ URL::route('edit-provider',$empty_location_user->id) }}">
				  <li class="collection-item">
					  <div class="row">
						  <div class="col s7">
							<p class="collections-title">{{ $empty_location_user->first_name.' '.$empty_location_user->last_name }}</p>
							<p class="collections-content">{{ $empty_location_user->email }}</p>
						  </div>
						  <div class="col s5">
							<div id="project-line-1" >{{ $empty_location_user->phone }}</div>
						  </div>
					  </div>
				  </li>
			  </a>
              @endforeach
              <?php }  else {?>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s12">
                          <p class="collections-title">No provider found</p>
                        </div>
                    </div>
                </li>
              <?php }?>
          </ul>
        </div>
        <div class="col s12 m12 l4">
            <ul id="issues-collection" class="collection" style="overflow-y:scroll;max-height:400px">
              <li class="collection-item avatar">
                  <i class="mdi-editor-insert-comment blue circle"></i>
                  <span class="collection-header">Unfilled</span>
                  <p>Clinics</p>
              </li>
              <?php if(!empty($unfilled_clinics)){?>
              @foreach($unfilled_clinics as $unfilled_clinic)
              <a href="{{ URL::route('edit-clinic',$unfilled_clinic->id) }}"><li class="collection-item">
                  <div class="row">
                      <div class="col s7">
                        <p class="collections-title">{{ $unfilled_clinic->name }}</p>
                        <p class="collections-content">{{ $unfilled_clinic->location_name }}</p>
                      </div>
                      <div class="col s5">
                        <div id="project-line-1" >{{ $unfilled_clinic->date.' '.$unfilled_clinic->time }}</div>
                      </div>
                  </div>
              </li></a>
              @endforeach
              <?php }  else {?>
                <li class="collection-item">
                    <div class="row">
                        <div class="col s12">
                          <p class="collections-title">No unfilled clinics found</p>
                        </div>
                    </div>
                </li>
              <?php }?>
          </ul>
        </div>
      </div>
      <div class="row">
        <div class="col s12 m12 l6">
          <ul id="projects-collection" class="collection">
            <li class="collection-item avatar">
              <i class="mdi-file-folder circle light-blue"></i>
              <span class="collection-header">Recently</span>
              <p>Uploaded certificates</p>
              <a class="secondary-content"><i class="mdi-action-grade"></i></a>
            </li>

            @foreach($certifications as $certification)
            <li class="collection-item">
              <div class="row">
                <div class="col s6">
                  <p class="collections-title">{{ $certification->subject }}</p>
                  <p class="collections-content">by: {{ $certification->first_name }}</p>
                </div>
                <div class="col s3">
                  <span class="task-cat teal darken-3"><a href="{{ WEBSITE_UPLOADS_URL.'certificates/'.$certification->file }}" target="_blank" class="white-text">View File</a></span>
                </div>
                <div class="col s3">
                  <div id="project-line-1" >{{  date('m-d-Y', strtotime($certification->updated_at)) }}</div>
                </div>
              </div>
            </li>
              @endforeach
          </ul>
        </div>
        <div class="col s12 m12 l6">
            <ul id="issues-collection" class="collection">
              <li class="collection-item avatar">
                  <i class="mdi-action-bug-report red circle"></i>
                  <span class="collection-header">Recently</span>
                  <p>Uploaded announcements</p>
                  <a class="secondary-content"><i class="mdi-action-grade"></i></a>
              </li>
              @foreach($announcements as $announcement)
              <li class="collection-item">
                  <div class="row">
                      <div class="col s7">
                          <p class="collections-title">{{ Str::limit($announcement->title, 27) }}</p>
                          <p class="collections-content">{{ Str::limit($announcement->description, 27) }}</p>
                      </div>
                      <div class="col s2">
                          <span class="task-cat red accent-2"><a href="{{ WEBSITE_UPLOADS_URL.'announcement/'.$announcement->image }}" target="_blank" class="white-text">View File</a></span>
                      </div>
                      <div class="col s3">
                        <div id="project-line-1" >{{  date('m-d-Y', strtotime($announcement->updated_at)) }}</div>
                      </div>
                  </div>
              </li>
              @endforeach
          </ul>
        </div>
      </div>
    </div>
    </div>
    <!--end container-->
</section>
<?php if(isset($current_month_days_data)){
$current_month_days = implode(', ',$current_month_days_data);
}else{
$current_month_days = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
}


if(isset($current_month_certificates)){
$current_month_certificates_data = implode(', ',$current_month_certificates);
}else{
$current_month_certificates_data = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
}


if(isset($current_month_days_data_admin)){
$current_month_days_data_admnn = implode(', ',$current_month_days_data_admin);
}else{
$current_month_days_data_admnn = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
}

if(isset($current_month_announs)){
$current_month_announsmnt = implode(', ',$current_month_announs);
}else{
$current_month_announsmnt = '0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0';
}

?>
<script type="text/javascript">
  $(document).ready(function(){

    $("#provider-bar").sparkline([{{ $current_month_days }}], {
        type: 'bar',
        height: '25',
        barWidth: 5,
        barSpacing: 2,
        barColor: '#C7FCC9',
        negBarColor: '#81d4fa',
        zeroColor: '#81d4fa',
    });
    //clientsBar.setOptions({chartArea: {width: 100}});
    $("#certificate-bar").sparkline([{{ $current_month_certificates_data }}], {
        type: 'line',
        width: '100%',
        height: '25',
        lineWidth: 2,
        lineColor: '#E1D0FF',
        fillColor: 'rgba(233, 30, 99, 0.4)',
        highlightSpotColor: '#E1D0FF',
        highlightLineColor: '#E1D0FF',
        minSpotColor: '#f44336',
        maxSpotColor: '#4caf50',
        spotColor: '#E1D0FF',
        spotRadius: 4,
        });
    $("#admin-bar").sparkline([{{ $current_month_days_data_admnn }}], {
        type: 'bar',
        height: '25',
        barWidth: 7,
        barSpacing: 4,
        barColor: '#C7FCC9',
        negBarColor: '#81d4fa',
        zeroColor: '#81d4fa',
    });
    $("#announcement-bar").sparkline([{{ $current_month_announsmnt }}], {
      type: 'line',
      width: '100%',
      height: '25',
      lineWidth: 2,
      lineColor: '#ffcc80',
      fillColor: 'rgba(255, 152, 0, 0.5)',
      highlightSpotColor: '#ffcc80',
      highlightLineColor: '#ffcc80',
      minSpotColor: '#f44336',
      maxSpotColor: '#4caf50',
      spotColor: '#ffcc80',
      spotRadius: 4,
        });
        });
    </script>
@stop
