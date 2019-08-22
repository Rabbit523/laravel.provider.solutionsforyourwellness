<?php
$table_data				=	array();
$table_data['aaData']	=	array();
foreach ($records as $record){
		$button						=	'<button type="button" class="btn btn-primary" disabled>View details</a>';
	if(isset($record['timesheet_status']) && $record['timesheet_status'] == 0 ){
		$checkbox = '<input type="checkbox" id="'.$record['provider_id'].'" value="'.$record['provider_id'].'" name="download_timesheet_excel[]" />';
		$table_data['aaData'][]			=	array(
											'sno'=>'',
											'name'=>CustomHelper::GetUserNameById($record['provider_id']),
											'rate'=>trans('Not Available'),
											'total_time'=>trans('Not Available'),
											'total_mileage'=>trans('Not Available'),
											'drive_time_total'=>trans('Not Available'),
											'total_price'=>trans('Not Available'),
											'action'=>$button,
										);
	}elseif(isset($record['timesheet_status']) && $record['timesheet_status'] == 1){
		$view_btn 					=	'<a class="btn btn-primary" href="'.URL::route('timesheet_single_view',$record['provider_id']).'">View details</a>';
		$checkbox = '<input type="checkbox" id="'.$record['provider_id'].'" value="'.$record['provider_id'].'" name="download_timesheet_excel[]" /><label for="'.$record['provider_id'].'"></label>';
		$table_data['aaData'][]			=	array(
											'sno'=>$checkbox,
											'name'=>CustomHelper::GetUserNameById($record['provider_id']),
											'rate'=>$record['hourly_rate'],
											'total_time'=>$record['total_spend_time'].' hours',
											'total_mileage'=>$record['total_mileage'].' Miles',
											'drive_time_total'=>$record['total_drive_time'].' hours',
											'total_price'=>'$'.number_format($record['income_total'],2),
											'action'=>$view_btn,
										);
	}
}
$table_data['iTotalRecords']			=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
