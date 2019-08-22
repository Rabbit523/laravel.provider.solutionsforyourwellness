@extends('admin.layouts.default_layout')
@section('content')
@section('title','provider finance report')
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
              <h5 class="breadcrumbs-title">{{ trans('Providers finance report') }}</h5>
              <ol class="breadcrumbs">
                  <li><a href="{{ URL::route('admindashboard')}}">{{ trans('Dashboard') }}</a></li>
                  <li><a href="{{ URL::route('providers')}}">{{ trans('Providers') }}</a></li>
                  <li class="active">{{ trans('Providers finance report') }}</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!--breadcrumbs end-->
      <!--start container-->
      <div class="container">
        <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('edit-provider',$segment4)}}" name="action">Edit
          <i class="mdi-image-edit left"></i>
        </a>
        <a title="delete" data-toggle="tooltip" class="delete_record_btn btn waves-effect waves-light red dark" href="javascript:void(0);" data-url="{{ URL::route('delete-providers',$segment4)}}"> <i class="mdi-action-delete left" aria-hidden="true"></i> Delete </a>
        <a class="btn waves-effect waves-light grey dark" type="submit" href="{{ URL::route('provider-details',$segment4)}}" name="action">Provider details
          <i class="mdi-maps-local-atm left"></i>
        </a>
        <a class="btn waves-effect waves-light blue dark" type="submit" href="{{ URL::route('view-certificates',$segment4)}}" name="action">Certificates
          <i class="mdi-image-remove-red-eye left"></i>
        </a>
        <a class="btn waves-effect waves-light green" type="submit" href="{{ URL::route('provider-calender',$segment4)}}" name="action">Provider Calender
          <i class="mdi-image-edit left"></i>
        </a>
        <div id="invoice">
            <div class="invoice-table">
              <div class="row">
                <div class="col s12 m12 l12">
                  <table class="striped">
                    <thead>
                      <tr>
                        <th data-field="no" width="5%">No</th>
                        <th data-field="item" width="10%">Date</th>
                        <th data-field="item" width="25%">Location</th>
                        <th data-field="uprice" width="10%">Clock in</th>
                        <th data-field="price" width="10%">Clock out</th>
                        <th data-field="price" width="10%">Total time</th>
												<th data-field="price" width="10%">Mileage</th>
												<th data-field="price" width="15%">Drive time</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $x = 1;?>
                      @foreach($finance_reports as $finance_report)
                      <tr>
                        <td>{{ $x }}</td>
                        <td>{{ date('d-m-Y', strtotime($finance_report->create_timestamp)) }}</td>
                        <td>{{ $finance_report->location_name }}</td>
                        <td>{{ $finance_report->clock_in }}</td>
                        <td>{{ $finance_report->clock_out }}</td>
                        <td>{{ $final_value= round(((strtotime(($finance_report->clock_out))-strtotime($finance_report->clock_in))/60)) }} mins
                          <?php $final_array[] = $final_value; ?>
                        </td>
												<td>{{ $final_mileage= $finance_report->mileage }} miles
                        		<?php $total_mileage[] = $final_mileage; ?>
                        </td>
												<td>{{ $drive_time= $finance_report->drive_time }} mins
                        		<?php $total_drive_time[] = $drive_time; ?>
                        </td>
                      </tr>
                      <?php $x++;?>
                      @endforeach
                      <tr>
                        <td colspan="4"></td>
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
                      </tr>

                    </tbody>
                  </table>
                </div>

              </div>
            </div>

          </div>
      </div>

      <!--end container-->
    </section>
    <!-- END CONTENT -->
  <!-- /.content-wrapper -->
@stop
