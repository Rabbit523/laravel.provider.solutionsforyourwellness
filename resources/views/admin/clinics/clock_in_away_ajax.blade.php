<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
foreach ($clinicdetails as $clinic){

	$delete_btn	=	'<a title="delete" data-toggle="tooltip" class="delete_record_btn" href="javascript:void(0);" data-url="'.URL::route('delete-clockin',$clinic['id']).'"> <i class="mdi-action-delete" title="Delete"></i></a>';
	$checkbox = '<input type="checkbox" id="'.'click'.$clinic['id'].'" value="'.$clinic['id'].'" name="chk_ids[]" class="checked_clinics" /><label for="'.'click'.$clinic['id'].'"></label>';
	$clinic_name = '<a title="Go to clinic" data-toggle="tooltip" href="'.URL::route('edit-clinic',$clinic['clinic_id']).'">'.$clinic['name'].'</a>';
	//prd($clinic);
	$table_data['aaData'][]			=	array(
											'sno'=>'<input type="checkbox" id="'.'click'.$clinic['id'].'" value="'.$clinic['id'].'" name="chk_ids[]" class="checked_clinics" /><label for="'.'click'.$clinic['id'].'"></label>',
											'clinic_name'=>$clinic_name,
											'provider_name'=>$clinic['first_name'].' '.$clinic['last_name'],
											'clocked_in_lat'=>$clinic['latitude'],
											'clocked_in_long'=>$clinic['longitude'],
											'clinic_date'=>$clinic['date'],
											'action'=>$delete_btn,
										);
										$x++;
}
$table_data['iTotalRecords']					=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
