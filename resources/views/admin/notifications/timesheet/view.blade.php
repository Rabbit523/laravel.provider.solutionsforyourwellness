@extends('admin.layouts.default_layout')
@section('content')
@section('title','Timesheet')
<?php
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
              <h5 class="breadcrumbs-title">{{ trans('Timesheet') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('timesheet_view')}}">{{ trans('Timesheet') }}</a></li>
                  <li class="active">{{ trans('Timesheet View') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
        {!! Form::open(['id'=>'timesheet_download_form','class'=>'form-horizontal','files'=>'true','method'=>'post','role' => 'form', 'url' =>URL::route('timesheet_download')]) !!}
		<div class="row">
		  <div class="input-field col s2">
			<input type="submit" class="btn btn-danger" value="Download Excel"/>
		  </div>
		</div>
        <div id="invoice">
            <div class="invoice-table">
				<hr>
                <div class="col s12 m12 l12">
                  <table class="striped">
                    <thead>
                      <tr>
                        <th data-field="no" width="5%">
							S.No.
						</th>
						<th data-field="name" width="5%">Name</th>
                        <th data-field="item" width="10%">Date</th>
                        <th data-field="item" width="25%">Location</th>
                        <th data-field="uprice" width="10%">Clock in</th>
                        <th data-field="price" width="10%">Clock out</th>
                        <th data-field="price" width="10%">Total time</th>
						<th data-field="price" width="10%">Mileage</th>
						<th data-field="price" width="15%">Drive time</th>
						<th data-field="price" width="15%">Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      @php($x=1)
					  @if(!empty($records))
						  @foreach($records as $record)
						  <tr>
							<td>{{ $x }}</td>
							<td>{{ $record->first_name.' '.$record->last_name }}</td>
							<td>{{ date('d-m-Y', strtotime($record->create_timestamp)) }}</td>
							<td>{{ $record->location_name }}</td>
							<td>{{ $record->clock_in }}</td>
							<td>{{ $record->clock_out }}</td>
							<td>
							<?php 
								$clinic_spend_time	= round(((strtotime(($record->clock_out))-strtotime($record->clock_in))/60))
							?>
								{{ $clinic_spend_time }} mins
							  <?php $final_array[] = isset($clinic_spend_time)?$clinic_spend_time:[]; ?>
							</td>
							<td>{{ $final_mileage= $record->mileage }} miles
								<?php $total_mileage[] = isset($final_mileage)?$final_mileage:[]; ?>
							</td>
							<td>
							{{ $drive_time= $record->drive_time }} mins
							<?php $total_drive_time[] = isset($drive_time)?$drive_time:[]; ?>
							</td>
							<td>
								<?php 
									$price			=	($record->drive_time+$clinic_spend_time)*$record->hourly_rate;
									$price_array[]	=	($record->drive_time+$clinic_spend_time)*$record->hourly_rate;
								?>
								${{ $price }}
							</td>
						  </tr>
						  @php($x++)
						  @endforeach
                      <tr>
                        <td colspan="4"></td>
                        <td  class="white-text"></td>
                        <td  class="cyan white-text">Sub Total:</td>
                        <?php if(isset($final_array)){?>
                        <td  class="cyan white-text">{{$grand_total = array_sum($final_array)}} mins</td>
                        <?php } else{ ?>
                          <td class="cyan white-text">0 mins</td>
                      <?php  } ?>
									<?php if(isset($total_mileage)){?>
									<td class="cyan white-text">{{$total_mileage = array_sum($total_mileage)}} miles</td>
									<?php } else{ ?>
										<td class="cyan white-text">0 miles</td>
								<?php  } ?>
								<?php if(isset($total_drive_time)){?>
								<td class="cyan white-text">{{$total_drive_time = array_sum($total_drive_time)}} mins</td>
								<?php } else{ ?>
									<td class="cyan white-text">0 mins</td>
							<?php  } ?>
							<td class="cyan white-text">${{ isset($price_array)?array_sum($price_array):0 }}</td>
                      </tr>
					@else
						<h3>No records found.</h3>
					@endif
                    </tbody>
                  </table>
                </div>

              </div>
            </div>
			{!! Form::close() !!}
          </div>
      </div>

      <!--end container-->
    </section>
    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
@stop
