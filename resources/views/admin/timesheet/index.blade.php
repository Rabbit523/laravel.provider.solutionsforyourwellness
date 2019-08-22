@extends('admin.layouts.default_layout')
@section('content')
@section('title','Timesheet')
<style>
.cyan.darken-1 {
    background-color: #18497a !important;
}
#doughnut-chart-wrapper{
	padding-bottom:15%
}
</style>
<?php
		$segment4	=	Request::segment(3);
		$segment5	=	Request::segment(4);
	?>
<?php
if(isset($timesheet['day_view']) && isset($timesheet['gross_info']) && isset($timesheet['month_view']) && isset($timesheet['week_view'])  && isset($timesheet['pay_period'])){
 if(isset($timesheet['day_view'])){
	foreach($timesheet['day_view'] as $time){
		if($time['status'] != -1){
			$days[]	  		=	$time['day'];
			$income[] 		= 	number_format($time['income'],2);
			$mileage[] 		= 	isset($time['mileage'])?$time['mileage']:0;
			$hours_time[] 	= 	isset($time['hours_time'])?$time['hours_time']:0;
			$drive_time[] 	= 	isset($time['drive_time'])?$time['drive_time']:0;
		}
	} 
 }
 if(isset($timesheet['gross_info'])){
			$gross_income_total 		= 	isset($timesheet['gross_info']['gross_income_total'])?$timesheet['gross_info']['gross_income_total']:0;
			$gross_mileage_total 		= 	isset($timesheet['gross_info']['gross_mileage_total'])?$timesheet['gross_info']['gross_mileage_total']:0;
			$gross_hours_time_total 	= 	isset($timesheet['gross_info']['gross_hours_time_total'])?$timesheet['gross_info']['gross_hours_time_total']:0;
			$gross_drive_time_total 	= 	isset($timesheet['gross_info']['gross_drive_time_total'])?$timesheet['gross_info']['gross_drive_time_total']:0;
 }
 if(isset($timesheet['month_view'])){
	foreach($timesheet['month_view'] as $month_incm){
			$month[]	  		=	$month_incm['month'];
			$month_income[] 	= 	isset($month_incm['income'])?number_format($month_incm['income'],2):0;
	}
 }
 if(isset($timesheet['week_view'])){
	foreach($timesheet['week_view'] as $week){
			$week_income[] 		= 	isset($week['income'])?number_format($week['income'],2):0;
			$week_mileage[] 	= 	isset($week['mileage'])?$week['mileage']:0;
			$week_hours_time[] 	= 	isset($week['hours_time'])?$week['hours_time']:0;
			$week_drive_time[] 	= 	isset($week['drive_time'])?$week['drive_time']:0;
			$week_day[] 		= 	$week['week'];
	} 
 }
 if(isset($timesheet['pay_period'])){
	foreach($timesheet['pay_period'] as $pay){
			$pay_income[] 		= 	number_format($pay['income'],2);
			$pay_mileage[] 		= 	isset($pay['mileage'])?$pay['mileage']:0;
			$pay_hours_time[] 	= 	isset($pay['hours_time'])?$pay['hours_time']:0;
			$pay_drive_time[] 	= 	isset($pay['drive_time'])?$pay['drive_time']:0;
			$pay_period[] 		= 	isset($pay['pay_period'])?$pay['pay_period']:0;
			$pay_period2[] 		= 	"'".$pay['pay_period']."'";
	}	
 }
 $as_string = implode(',',isset($pay_period2)?$pay_period2:[0,0]);
			 //prd($as_string);
}
			?>
<!-- Content Wrapper. Contains page content -->
<section id="content">

      <!--breadcrumbs start-->
      <div id="breadcrumbs-wrapper">
        <div class="container">
          <div class="row">
            <div class="col s12 m12 l12">
              <h5 class="breadcrumbs-title">{{ trans('Timesheet') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li class="active">{{ trans('Timesheet Report') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
	  <?php
		if(isset($timesheet['day_view']) && isset($timesheet['gross_info']) && isset($timesheet['month_view']) && isset($timesheet['week_view'])  && isset($timesheet['pay_period'])){ ?>
	  <div class="row">
	  <div class="col s12 m12 l12">
	  <form method="GET" name="type_form" style="margin-bottom:10px;color:#f7464a">
		<select name="view_type" class="browser-default" onchange="this.form.submit()">
			<option selected disabled>Choose view type</option>
			<option value="day" <?php if(isset($_REQUEST['view_type']) && $_REQUEST['view_type'] =='day'){ echo 'selected';}?>>Day view</option>
			<option value="week" <?php if(isset($_REQUEST['view_type']) && $_REQUEST['view_type'] =='week'){ echo 'selected';}?>>Week view</option>
			<option value="pay-period" <?php if(isset($_REQUEST['view_type']) && $_REQUEST['view_type'] =='pay-period'){ echo 'selected';}?>>Pay period</option>
		</select>
	</form>
	<?php if((isset($_REQUEST['view_type']) && $_REQUEST['view_type']=='day') || !isset($_REQUEST['view_type'])){?>
		<div class="card">
			<div class="card-move-up waves-effect waves-block waves-light">
				<div class="move-up cyan darken-1">
					<div>
						<span class="chart-title white-text">Revenue for <?php echo date('F')?></span>
						<div class="chart-revenue cyan darken-2 white-text">
							<p class="chart-revenue-total">$ {{ isset($gross_income_total)?$gross_income_total:0 }} from <?php echo date('F')?></p>
							
						</div>
						
					</div>
					<div class="trending-line-chart-wrapper">
						<canvas id="trending-line-chart" height="70"></canvas>
					</div>
				</div>
			</div>
			<div class="card-content">
				<a class="btn-floating btn-move-up waves-effect waves-light darken-2 right"><i class="mdi-content-add activator"></i></a>
				<div class="col s12 m3 l3"  >
					<div id="doughnut-chart-wrapper">
						<canvas id="doughnut-chart" height="200"></canvas>
						<div class="doughnut-chart-status" style="margin-top:-10%">${{ isset($gross_income_total)?$gross_income_total:0 }}
							<p class="ultra-small center-align">Total Income</p>
						</div>
					</div>
				</div>
				<div class="col s12 m2 l2">
					<ul class="doughnut-chart-legend">
						<li class="kitchen ultra-small"><span class="legend-color"></span> Income</li>
						<li class="mobile ultra-small"><span class="legend-color"></span>Hours Time</li>
						<li class="home ultra-small"><span class="legend-color"></span> Mileage</li>
					</ul>
				</div>
				<div class="col s12 m5 l6">
					<div class="trending-bar-chart-wrapper">
						<canvas id="trending-bar-chart" height="90"></canvas>                                                
					</div>
				</div>
			</div>

			<div class="card-reveal">
				<span class="card-title grey-text text-darken-4">Revenue by Day <i class="mdi-navigation-close right"></i></span>
				<table class="responsive-table">
					<thead>
						<tr>
							<th data-field="month">Day</th>
							<th data-field="item-sold">Mileage</th>
							<th data-field="item-price">Drive Time</th>
							<th data-field="total-profit">Hours Time</th>
							<th data-field="total-profit">Income</th>
						</tr>
					</thead>
					<tbody>
						@foreach($timesheet['day_view'] as $time)
							@if($time['status'] != -1)
						<tr>
							<td>{{ $time['day'] }}</td>
							<td>{{ $time['mileage'] }}</td>
							<td>{{ $time['drive_time'] }}</td>
							<td>{{ $time['hours_time'] }}</td>
							<td>{{ $time['income'] }}</td>
						</tr>
							@endif
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	<?php } elseif((isset($_REQUEST['view_type']) && $_REQUEST['view_type']=='week')){ ?>
		<div class="card">
			<div class="card-move-up waves-effect waves-block waves-light">
				<div class="move-up cyan darken-1">
					<div>
						<span class="chart-title white-text">Revenue for <?php echo date('F')?></span>
						<div class="chart-revenue cyan darken-2 white-text">
							<p class="chart-revenue-total">$ {{ $gross_income_total }} from <?php echo date('F')?></p>
							
						</div>
						
					</div>
					<div class="trending-line-chart-wrapper">
						<canvas id="trending-line-chart" height="70"></canvas>
					</div>
				</div>
			</div>
			<div class="card-content">
				<a class="btn-floating btn-move-up waves-effect waves-light darken-2 right"><i class="mdi-content-add activator"></i></a>
				<div class="col s12 m3 l3">
					<div id="doughnut-chart-wrapper">
						<canvas id="doughnut-chart" height="200"></canvas>
						<div class="doughnut-chart-status" style="margin-top:-10%">${{ $gross_income_total }}
							<p class="ultra-small center-align">Total Income</p>
						</div>
					</div>
				</div>
				<div class="col s12 m2 l2">
					<ul class="doughnut-chart-legend">
						<li class="kitchen ultra-small"><span class="legend-color"></span> Income</li>
						<li class="mobile ultra-small"><span class="legend-color"></span>Hours Time</li>
						<li class="home ultra-small"><span class="legend-color"></span> Mileage</li>
					</ul>
				</div>
				<div class="col s12 m5 l6">
					<div class="trending-bar-chart-wrapper">
						<canvas id="trending-bar-chart" height="90"></canvas>                                                
					</div>
				</div>
			</div>

			<div class="card-reveal">
				<span class="card-title grey-text text-darken-4">Revenue by Day <i class="mdi-navigation-close right"></i></span>
				<table class="responsive-table">
					<thead>
						<tr>
							<th data-field="month">Day</th>
							<th data-field="item-sold">Mileage</th>
							<th data-field="item-price">Drive Time</th>
							<th data-field="total-profit">Hours Time</th>
							<th data-field="total-profit">Income</th>
						</tr>
					</thead>
					<tbody>
						@foreach($timesheet['day_view'] as $time)
							@if($time['status'] != -1)
						<tr>
							<td>{{ $time['day'] }}</td>
							<td>{{ $time['mileage'] }}</td>
							<td>{{ $time['drive_time'] }}</td>
							<td>{{ $time['hours_time'] }}</td>
							<td>{{ $time['income'] }}</td>
						</tr>
							@endif
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	<?php } elseif((isset($_REQUEST['view_type']) && $_REQUEST['view_type']=='pay-period')){ ?>
		<div class="card">
			<div class="card-move-up waves-effect waves-block waves-light">
				<div class="move-up cyan darken-1">
					<div>
						<span class="chart-title white-text">Revenue for <?php echo date('F')?></span>
						<div class="chart-revenue cyan darken-2 white-text">
							<p class="chart-revenue-total">$ {{ $gross_income_total }} from <?php echo date('F')?></p>
							
						</div>
						
					</div>
					<div class="trending-line-chart-wrapper">
						<canvas id="trending-line-chart" height="70"></canvas>
					</div>
				</div>
			</div>
			<div class="card-content">
				<a class="btn-floating btn-move-up waves-effect waves-light darken-2 right"><i class="mdi-content-add activator"></i></a>
				<div class="col s12 m3 l3">
					<div id="doughnut-chart-wrapper">
						<canvas id="doughnut-chart" height="200"></canvas>
						<div class="doughnut-chart-status" style="margin-top:-10%">${{ $gross_income_total }}
							<p class="ultra-small center-align">Total Income</p>
						</div>
					</div>
				</div>
				<div class="col s12 m2 l2">
					<ul class="doughnut-chart-legend">
						<li class="kitchen ultra-small"><span class="legend-color"></span> Income</li>
						<li class="mobile ultra-small"><span class="legend-color"></span>Hours Time</li>
						<li class="home ultra-small"><span class="legend-color"></span> Mileage</li>
					</ul>
				</div>
				<div class="col s12 m5 l6">
					<div class="trending-bar-chart-wrapper">
						<canvas id="trending-bar-chart" height="90"></canvas>                                                
					</div>
				</div>
			</div>

			<div class="card-reveal">
				<span class="card-title grey-text text-darken-4">Revenue by Day <i class="mdi-navigation-close right"></i></span>
				<table class="responsive-table">
					<thead>
						<tr>
							<th data-field="month">Day</th>
							<th data-field="item-sold">Mileage</th>
							<th data-field="item-price">Drive Time</th>
							<th data-field="total-profit">Hours Time</th>
							<th data-field="total-profit">Income</th>
						</tr>
					</thead>
					<tbody>
						@foreach($timesheet['day_view'] as $time)
							@if($time['status'] != -1)
						<tr>
							<td>{{ $time['day'] }}</td>
							<td>{{ $time['mileage'] }}</td>
							<td>{{ $time['drive_time'] }}</td>
							<td>{{ $time['hours_time'] }}</td>
							<td>{{ $time['income'] }}</td>
						</tr>
							@endif
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	<?php } ?>
                            </div>
						</div>
	<?php } ?>
        <!---<a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('provider-details',$segment4)}}" name="action">Provider details
          <i class="mdi-maps-local-atm left"></i>
        </a>
        <a class="btn waves-effect waves-light blue dark" type="submit" href="{{ URL::route('view-certificates',$segment4)}}" name="action">Certificates
          <i class="mdi-image-remove-red-eye left"></i>
        </a>
        <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('provider-calender',$segment4)}}" name="action">Provider Calender
          <i class="mdi-image-edit left"></i>
        </a>-->
        <div id="invoice">
            <div class="invoice-table">
              <div class="row">
					<!---<div class="col s12 m12 l5 offset-l4">
					  {!! Form::open(['id'=>'search_timesheet_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form','url'=>URL::route('timesheet_user_search')]) !!}
					  <div class="row">
						  <div class="col s10" style="margin-top:4%">
							<select class="browser-default valid" multiple name="provider[]" id="SelectedProvider" data-error=".errorTxt6" style="width:300px;">
							  <option value="all">All</option>
							  @foreach ($providers as $provider)
								<option value="{{ $provider->id }}">{{ $provider->first_name }} {{ $provider->last_name }}</option>
							  @endforeach
							</select>
						  </div>
					  <div class="input-field col s2">
						<input type="button" id="btnSearch" class="btn waves-effect waves-light grey dark" value="Search"/>
					  </div>
					</div>
					  {!! Form::close() !!}
					</div>-->
                </div>
				{!! Form::open(['id'=>'timesheet_download_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('timesheet_download')]) !!}
				<input type="submit" name="submit" value="Download Excel" class="btn btn-success">
				<hr>
                <div class="col s12 m12 l12">
                  <table class="striped" id="timesheet_records_datatable">
                    <thead>
                      <tr>
                        <th data-field="no" width="5%">
							<input type="checkbox" class="form-control selectall" value="0" id="selectall">
							<label for="selectall">All</label>
						</th>
						<th data-field="name" width="5%">Name</th>
						<th data-field="name" width="10%">Rate</th>
                        <th data-field="price" width="10%">Total Time</th>
						<th data-field="price" width="10%">Total Mileage</th>
						<th data-field="price" width="15%">Total Drive time</th>
						<th data-field="price" width="15%">Total Price</th>
						<th data-field="price" width="15%">Action</th>
                      </tr>
                    </thead>
                    <tbody>
						@if(!empty($records))
							@foreach($records as $record)
							@if($record['timesheet_status'] == 0)
							<tr>
								<td>
									<input type="checkbox" id="{{ $record['provider_id'] }}" value="{{ $record['provider_id'] }}" name="chk_ids[]"/>
								</td>
								<td>{{ CustomHelper::GetUserNameById($record['provider_id']) }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>{{ trans('Not Available') }}</td>
								<td>
									<button type="button" class="btn btn-primary" disabled>View</a>
								</td>
							</tr>
							@else
							<tr>
								<td>
									<input type="checkbox" id="{{ $record['provider_id'] }}" value="{{ $record['provider_id'] }}" name="download_timesheet_excel[]" />
									<label for="{{ $record['provider_id'] }}"></label>
								</td>
								<td>{{ $record['first_name'].' '.$record['last_name'] }} </td>
								<td>{{ $record['hourly_rate'] }} Mins</td>
								<td>{{ $record['total_spend_time'] }} Mins</td>
								<td>{{ $record['total_mileage'] }} Miles</td>
								<td>{{ $record['total_drive_time'] }} Mins</td>
								<td>${{ $record['income_total'] }}</td>
								<td>
									<a class="btn btn-primary" href="{{ URL::route('timesheet_single_view',$record['provider_id']) }}">View</a>
								</td>
							</tr>
							@endif
						@endforeach
						@else
						<h3>No records found.</h3>
						@endif
                    </tbody>
                  </table>
                </div>
				{!! Form::close() !!}
              </div>
            </div>

          </div>
      </div>

      <!--end container-->
    </section>
    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
  <script type="text/javascript">
<?php
if(isset($timesheet['day_view']) && isset($timesheet['gross_info']) && isset($timesheet['month_view']) && isset($timesheet['week_view'])  && isset($timesheet['pay_period'])){ ?>
  $(document).ready(function(){
<?php if((isset($_REQUEST['view_type']) && $_REQUEST['view_type']=='day') || !isset($_REQUEST['view_type'])){?>
var trendingLineChart;	
var data = {
  labels : [{{ implode(',',$days) }}],
	datasets : [
		{
			label: "First dataset",
			fillColor : "rgba(128, 222, 234, 0.6)",
			strokeColor : "#5AD3D1",
			pointColor : "#5AD3D1",
			pointStrokeColor : "#5AD3D1",
			pointHighlightFill : "#5AD3D1",
			pointHighlightStroke : "#5AD3D1",
			data: [{{ implode(',',$income) }}]
		},
		{
			label: "Second dataset",
			fillColor : "rgba(128, 222, 234, 0.3)",
			strokeColor : "#fdb45c",
			pointColor : "#fdb45c",
			pointStrokeColor : "#fdb45c",
			pointHighlightFill : "#fdb45c",
			pointHighlightStroke : "#fdb45c",
			data: [{{ implode(',',$mileage) }}]
		},
		{
			label: "Third dataset",
			fillColor : "rgba(128, 222, 234, 0.3)",
			strokeColor : "#F7464A",
			pointColor : "#F7464A",
			pointStrokeColor : "#F7464A",
			pointHighlightFill : "#F7464A",
			pointHighlightStroke : "#F7464A",
			data: [{{ implode(',',$hours_time) }}]
		}
	]
};
 
var doughnutData = [
	{
		value: {{ $gross_income_total }},
		color:"#5AD3D1",
		highlight: "#5AD3D1",
		label: "Income"
	},
	{
		value: {{ $gross_mileage_total }},
		color: "#fdb45c",
		highlight: "#fdb45c",
		label: "Mileage"
	},
	{
		value: {{ $gross_hours_time_total }},
		color: "#F7464A",
		highlight: "#F7464A",
		label: "Hours Time"
	}

];

/*
Trending Bar Chart
*/
var dataBarChart = {
    labels : ["JAN","FEB","MAR","APR","MAY","JUNE","JULY","AUG","SEP","OCT","NOV","DEC"],
    datasets: [
        {
            label: "Bar dataset",
            fillColor: "#46BFBD",
            strokeColor: "#46BFBD",
            highlightFill: "rgba(70, 191, 189, 0.4)",
            highlightStroke: "rgba(70, 191, 189, 0.9)",
            data: [{{ implode(',',$month_income) }}]
        }
    ]
};
<?php } elseif((isset($_REQUEST['view_type']) && $_REQUEST['view_type']=='week')){ ?>
var trendingLineChart;	
var data = {
  labels : [1,2,3,4],
	datasets : [
		{
			label: "First dataset",
			fillColor : "rgba(128, 222, 234, 0.6)",
			strokeColor : "#5AD3D1",
			pointColor : "#5AD3D1",
			pointStrokeColor : "#5AD3D1",
			pointHighlightFill : "#5AD3D1",
			pointHighlightStroke : "#5AD3D1",
			data: [{{ implode(',',$week_income) }}]
		},
		{
			label: "Second dataset",
			fillColor : "rgba(128, 222, 234, 0.3)",
			strokeColor : "#fdb45c",
			pointColor : "#fdb45c",
			pointStrokeColor : "#fdb45c",
			pointHighlightFill : "#fdb45c",
			pointHighlightStroke : "#fdb45c",
			data: [{{ implode(',',$week_mileage) }}]
		},
		{
			label: "Third dataset",
			fillColor : "rgba(128, 222, 234, 0.3)",
			strokeColor : "#F7464A",
			pointColor : "#F7464A",
			pointStrokeColor : "#F7464A",
			pointHighlightFill : "#F7464A",
			pointHighlightStroke : "#F7464A",
			data: [{{ implode(',',$week_hours_time) }}]
		}
	]
};
 
var doughnutData = [
	{
		value: {{ $gross_income_total }},
		color:"#5AD3D1",
		highlight: "#5AD3D1",
		label: "Income"
	},
	{
		value: {{ $gross_mileage_total }},
		color: "#fdb45c",
		highlight: "#fdb45c",
		label: "Mileage"
	},
	{
		value: {{ $gross_hours_time_total }},
		color: "#F7464A",
		highlight: "#F7464A",
		label: "Hours Time"
	}

];

/*
Trending Bar Chart
*/
var dataBarChart = {
    labels : ["JAN","FEB","MAR","APR","MAY","JUNE","JULY","AUG","SEP","OCT","NOV","DEC"],
    datasets: [
        {
            label: "Bar dataset",
            fillColor: "#46BFBD",
            strokeColor: "#46BFBD",
            highlightFill: "rgba(70, 191, 189, 0.4)",
            highlightStroke: "rgba(70, 191, 189, 0.9)",
            data: [{{ implode(',',$month_income) }}]
        }
    ]
};	
<?php } elseif((isset($_REQUEST['view_type']) && $_REQUEST['view_type']=='pay-period')){ ?>
var trendingLineChart;	
var data = {
  labels : [<?php echo $as_string;?>],
	datasets : [
		{
			label: "First dataset",
			fillColor : "rgba(128, 222, 234, 0.6)",
			strokeColor : "#5AD3D1",
			pointColor : "#5AD3D1",
			pointStrokeColor : "#5AD3D1",
			pointHighlightFill : "#5AD3D1",
			pointHighlightStroke : "#5AD3D1",
			data: [{{ implode(',',$pay_income) }}]
		},
		{
			label: "Second dataset",
			fillColor : "rgba(128, 222, 234, 0.3)",
			strokeColor : "#fdb45c",
			pointColor : "#fdb45c",
			pointStrokeColor : "#fdb45c",
			pointHighlightFill : "#fdb45c",
			pointHighlightStroke : "#fdb45c",
			data: [{{ implode(',',$pay_mileage) }}]
		},
		{
			label: "Third dataset",
			fillColor : "rgba(128, 222, 234, 0.3)",
			strokeColor : "#F7464A",
			pointColor : "#F7464A",
			pointStrokeColor : "#F7464A",
			pointHighlightFill : "#F7464A",
			pointHighlightStroke : "#F7464A",
			data: [{{ implode(',',$pay_hours_time) }}]
		}
	]
};
 
var doughnutData = [
	{
		value: {{ $gross_income_total }},
		color:"#5AD3D1",
		highlight: "#5AD3D1",
		label: "Income"
	},
	{
		value: {{ $gross_mileage_total }},
		color: "#fdb45c",
		highlight: "#fdb45c",
		label: "Mileage"
	},
	{
		value: {{ $gross_hours_time_total }},
		color: "#F7464A",
		highlight: "#F7464A",
		label: "Hours Time"
	}

];

/*
Trending Bar Chart
*/
var dataBarChart = {
    labels : ["JAN","FEB","MAR","APR","MAY","JUNE","JULY","AUG","SEP","OCT","NOV","DEC"],
    datasets: [
        {
            label: "Bar dataset",
            fillColor: "#46BFBD",
            strokeColor: "#46BFBD",
            highlightFill: "rgba(70, 191, 189, 0.4)",
            highlightStroke: "rgba(70, 191, 189, 0.9)",
            data: [{{ implode(',',$month_income) }}]
        }
    ]
};	
<?php } ?>


/*
Trending Bar Chart
*/
window.onload = function(){
	var trendingLineChart = document.getElementById("trending-line-chart").getContext("2d");
	window.trendingLineChart = new Chart(trendingLineChart).Line(data, {		
		scaleShowGridLines : true,///Boolean - Whether grid lines are shown across the chart		
		scaleGridLineColor : "rgba(255,255,255,0.4)",//String - Colour of the grid lines		
		scaleGridLineWidth : 1,//Number - Width of the grid lines		
		scaleShowHorizontalLines: true,//Boolean - Whether to show horizontal lines (except X axis)		
		scaleShowVerticalLines: false,//Boolean - Whether to show vertical lines (except Y axis)		
		bezierCurve : true,//Boolean - Whether the line is curved between points		
		bezierCurveTension : 0.4,//Number - Tension of the bezier curve between points		
		pointDot : true,//Boolean - Whether to show a dot for each point		
		pointDotRadius : 5,//Number - Radius of each point dot in pixels		
		pointDotStrokeWidth : 2,//Number - Pixel width of point dot stroke		
		pointHitDetectionRadius : 20,//Number - amount extra to add to the radius to cater for hit detection outside the drawn point		
		datasetStroke : true,//Boolean - Whether to show a stroke for datasets		
		datasetStrokeWidth : 3,//Number - Pixel width of dataset stroke		
		datasetFill : true,//Boolean - Whether to fill the dataset with a colour				
		animationSteps: 15,// Number - Number of animation steps		
		animationEasing: "easeOutQuart",// String - Animation easing effect			
		tooltipTitleFontFamily: "'Roboto','Helvetica Neue', 'Helvetica', 'Arial', sans-serif",// String - Tooltip title font declaration for the scale label		
		scaleFontSize: 12,// Number - Scale label font size in pixels		
		scaleFontStyle: "normal",// String - Scale label font weight style		
		scaleFontColor: "#fff",// String - Scale label font colour
		tooltipEvents: ["mousemove", "touchstart", "touchmove"],// Array - Array of string names to attach tooltip events		
		tooltipFillColor: "rgba(255,255,255,0.8)",// String - Tooltip background colour		
		tooltipTitleFontFamily: "'Roboto','Helvetica Neue', 'Helvetica', 'Arial', sans-serif",// String - Tooltip title font declaration for the scale label		
		tooltipFontSize: 12,// Number - Tooltip label font size in pixels
		tooltipFontColor: "#000",// String - Tooltip label font colour		
		tooltipTitleFontFamily: "'Roboto','Helvetica Neue', 'Helvetica', 'Arial', sans-serif",// String - Tooltip title font declaration for the scale label		
		tooltipTitleFontSize: 14,// Number - Tooltip title font size in pixels		
		tooltipTitleFontStyle: "bold",// String - Tooltip title font weight style		
		tooltipTitleFontColor: "#000",// String - Tooltip title font colour		
		tooltipYPadding: 8,// Number - pixel width of padding around tooltip text		
		tooltipXPadding: 16,// Number - pixel width of padding around tooltip text		
		tooltipCaretSize: 10,// Number - Size of the caret on the tooltip		
		tooltipCornerRadius: 6,// Number - Pixel radius of the tooltip border		
		tooltipXOffset: 10,// Number - Pixel offset from point x to tooltip edge
		responsive: true
		});

		var doughnutChart = document.getElementById("doughnut-chart").getContext("2d");
		window.myDoughnut = new Chart(doughnutChart).Doughnut(doughnutData, {
			segmentStrokeColor : "#fff",
			tooltipTitleFontFamily: "'Roboto','Helvetica Neue', 'Helvetica', 'Arial', sans-serif",// String - Tooltip title font declaration for the scale label		
			percentageInnerCutout : 50,
			animationSteps : 15,
			segmentStrokeWidth : 4,
			animateScale: true,
			percentageInnerCutout : 60,
			responsive : true
		});

		var trendingBarChart = document.getElementById("trending-bar-chart").getContext("2d");
		window.trendingBarChart = new Chart(trendingBarChart).Bar(dataBarChart,{
			scaleShowGridLines : false,///Boolean - Whether grid lines are shown across the chart
			showScale: true,
			animationSteps:15,
			tooltipTitleFontFamily: "'Roboto','Helvetica Neue', 'Helvetica', 'Arial', sans-serif",// String - Tooltip title font declaration for the scale label		
			responsive : true
		});

		window.trendingRadarChart = new Chart(document.getElementById("trending-radar-chart").getContext("2d")).Radar(radarChartData, {
		    
		    angleLineColor : "rgba(255,255,255,0.5)",//String - Colour of the angle line		    
		    pointLabelFontFamily : "'Roboto','Helvetica Neue', 'Helvetica', 'Arial', sans-serif",// String - Tooltip title font declaration for the scale label	
		    pointLabelFontColor : "#fff",//String - Point label font colour
		    pointDotRadius : 4,
		    animationSteps:15,
		    pointDotStrokeWidth : 2,
		    pointLabelFontSize : 12,
			responsive: true
		});

		// var pieChartArea = document.getElementById("pie-chart-area").getContext("2d");
		// window.pieChartArea = new Chart(pieChartArea).Pie(pieData,{
		// 	responsive: true		
		// });

		var lineChart = document.getElementById("line-chart").getContext("2d");
		window.lineChart = new Chart(lineChart).Line(lineChartData, {
			scaleShowGridLines : false,
			bezierCurve : false,
			scaleFontSize: 12,
			scaleFontStyle: "normal",
			scaleFontColor: "#fff",
			responsive: true,			
		});

		
		if (typeof getContext != "undefined") {
			var polarChartCountry = document.getElementById("polar-chart-country").getContext("2d");
			window.polarChartCountry = new Chart(polarChartCountry).PolarArea(polarData, {
				segmentStrokeWidth : 1,			
				responsive:true
			});
		}
};
        });
<?php } ?>
    </script>
@stop
