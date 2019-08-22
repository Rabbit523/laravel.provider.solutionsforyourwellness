<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
foreach ($userdata as $user){
	if($user['status']=='1'){
		$status 		= 		'active';
	}else{
		$status 	= 		'inactive';
	}
	$edit_btn		=		'  <a href="'.URL::route('edit-admin',$user['id']).'"><i class="mdi-editor-border-color" title="edit"></i></a>';
	$delete_btn	=	'<a title="delete" data-toggle="tooltip" class="delete_record_btn" href="javascript:void(0);" data-url="'.URL::route('delete_admin',$user['id']).'"> <i class="mdi-action-delete" title="Delete"></i></a>';
	if($user['status']=='0'){
		$active_status	=	'<a data-toggle="tooltip" class="change_status_btn" href="javascript:void(0);" data-url="'.URL::route('admin-status',$user['id']).'" data-msg="Are you sure you want to activate this record?" title="Deactivated"> <i class="mdi-action-lock"></i></a>';
	}else{
		$active_status	=	'<a data-toggle="tooltip" class="change_status_btn" href="javascript:void(0);" data-url="'.URL::route('admin-status',$user['id']).'" data-msg="Are you sure you want to deactivate this record?" title="Activated"> <i class="mdi-action-lock-open"></i></a>';
	}

	$checkbox = '<input type="checkbox" id="'.'click'.$user['id'].'" value="'.$user['id'].'" name="chk_ids[]" class="checked_admin" /><label for="'.'click'.$user['id'].'"></label>';




	$table_data['aaData'][]			=	array(
											'sno'=>$checkbox,
											'name'=>$user['first_name'].' '.$user['last_name'],
											'email'=>$user['email'],
											'phone'=>$user['phone'],
											'status'=>$status,
											'action'=>$edit_btn.'&nbsp;'.$delete_btn.'&nbsp;'.$active_status,
										);
										$x++;
}
$table_data['iTotalRecords']					=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
