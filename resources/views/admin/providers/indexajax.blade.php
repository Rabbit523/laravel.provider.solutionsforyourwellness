<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
foreach ($providerdetails as $provider){
	if($provider['status']=='1'){
		$status 		= 		'active';
	}else{
		$status 	= 		'inactive';
	}
	$edit_btn		=		'  <a href="'.URL::route('edit-provider',$provider['id']).'" title="edit provider"><i class="mdi-editor-border-color" title="edit"></i></a>';
	$delete_btn	=	'<a title="delete" data-toggle="tooltip" class="delete_record_btn" href="javascript:void(0);" data-url="'.URL::route('delete-providers',$provider['id']).'"> <i class="mdi-action-delete" title="Delete"></i></a>';
	if($provider['status']=='0'){
		$active_status	=	'<a data-toggle="tooltip" class="change_status_btn" href="javascript:void(0);" data-url="'.URL::route('provider-status',$provider['id']).'" data-msg="Are you sure you want to activate this record?" title="Deactivated"> <i class="mdi-action-lock"></i></a>';
	}else{
		$active_status	=	'<a data-toggle="tooltip" class="change_status_btn" href="javascript:void(0);" data-url="'.URL::route('provider-status',$provider['id']).'" data-msg="Are you sure you want to deactivate this record?" title="Activated"> <i class="mdi-action-lock-open"></i></a>';
	}
	$setting_btn = '<a  href="'.URL::route('provider-details',$provider['id']).'" class="" title="Provider details"><i class="mdi-action-assignment"></i></a>';
	$view_btn = '<a  href="'.URL::route('view-certificates',$provider['id']).'" class="" title="view certificates"><i class="mdi-image-remove-red-eye"></i></a>';
	$time_sheet = '<a  href="'.URL::route('timesheet_single_view',$provider['id']).'" class="" title="Timesheet finance"><i class="mdi-maps-local-atm"></i></a>';
	$provider_calender = '<a  href="'.URL::route('provider-calender',$provider['id']).'" class="" title="Provider calendar"><i class="mdi-notification-event-available"></i></a>';
	$checkbox = '<input type="checkbox" id="'.'click'.$provider->id.'" value="'.$provider->id.'" name="chk_ids[]" class="checked_certificates" /><label for="'.'click'.$provider->id.'"></label>';



	$table_data['aaData'][]			=	array(
											'sno'=>$checkbox,
											'name'=>$provider['first_name'].' '.$provider['last_name'],
											'email'=>$provider['email'],
											'phone'=>$provider['phone'],
											'provider_type'=>$provider['provider_type'],
											'address'=>$provider['address'],
											'status'=>$status,
											'action'=>$setting_btn.'&nbsp;'.$delete_btn.'&nbsp;'.$active_status.'&nbsp;'.$view_btn.'&nbsp;'.$edit_btn.'&nbsp;'.$provider_calender.'&nbsp;'.$time_sheet,
										);
										$x++;
}
$table_data['iTotalRecords']					=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
