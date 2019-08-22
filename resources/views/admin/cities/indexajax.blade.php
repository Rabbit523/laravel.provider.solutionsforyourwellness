<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
foreach ($cities as $city){

	$edit_btn		=		'<a href="'.URL::route('edit_city',$city['id']).'"><i class="mdi-editor-border-color" title="edit"></i></a>';
	$delete_btn	=	'<a title="delete" data-toggle="tooltip" class="delete_record_btn" href="javascript:void(0);" data-url="'.URL::route('delete_city',$city['id']).'"> <i class="mdi-action-delete" title="Delete"></i></a>';
	$checkbox = '<input type="checkbox" id="'.'click'.$city['id'].'" value="'.$city['id'].'" name="chk_ids[]" class="checked_clinics" /><label for="'.'click'.$city['id'].'"></label>';



	$table_data['aaData'][]			=	array(
											'sno'			=>	$checkbox,
											'city_name'		=>	$city['city_name'],
											'description'	=>	$city['description'],
											'date'			=>	$city['created_at'],
											'action'		=>	$edit_btn.'&nbsp;'.$delete_btn,
										);
										$x++;
}
$table_data['iTotalRecords']			=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
