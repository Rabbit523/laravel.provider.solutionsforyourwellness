<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
$config_date 		= Config::get('date_format.date');
$config_month 		= Config::get('date_format.month');
$config_year 		= Config::get('date_format.year');
$config_separator 	= Config::get('date_format.separator');
foreach ($announcements as $announcement){
	if($announcement->status=='1'){
		$status 		= 		'active';
	}else{
		$status 	= 		'inactive';
	}
	$edit_btn		=		'  <a href="'.URL::route('edit-announcement',$announcement->id).'"><i class="mdi-editor-border-color" title="edit"></i></a>';
	if($announcement->status=='0'){
		$active_status	=	'<a data-toggle="tooltip" class="change_status_btn" href="javascript:void(0);" data-url="'.URL::route('announcement-status',$announcement->id).'" data-msg="Are you sure you want to activate this record?" title="Deactivated"> <i class="mdi-action-lock"></i></a>';
	}else{
		$active_status	=	'<a data-toggle="tooltip" class="change_status_btn" href="javascript:void(0);" data-url="'.URL::route('announcement-status',$announcement->id).'" data-msg="Are you sure you want to deactivate this record?" title="Activated"> <i class="mdi-action-lock-open"></i></a>';

	}
	if($announcement->image!=null){
	$image 	= '<a class="fancybox" href="'.WEBSITE_UPLOADS_URL.'announcement/'.$announcement->image.'"><img src="'.WEBSITE_UPLOADS_URL.'announcement/'.$announcement->image.'" height="50" width="50"></a>';
	}else{
		$image	= 'N/A';
	}
	$setting_btn = '<a  href="'.URL::route('announcement-setting',$announcement->id).'" class="" title="Setting"><i class="mdi-action-settings"></i></a>';
	$checkbox = '<input type="checkbox" id="'.'click'.$announcement->id.'" value="'.$announcement->id.'" name="chk_ids[]" class="checked_announcement" /><label for="'.'click'.$announcement->id.'"></label>';
	$delete_btn	=	'<a title="delete" data-toggle="tooltip" class="delete_record_btn" href="javascript:void(0);" data-url="'.URL::route('delete-announcements',$announcement->id).'"> <i class="mdi-action-delete" title="Delete"></i></a>';
	if(strlen($announcement->description)>50){
		$description = substr($announcement->description,0,50).'...';
	}else{
		$description = $announcement->description;
	}
	$upload_date   = date('Y-m-d',strtotime($announcement->created_at));

	$table_data['aaData'][]			=	array(
											'sno'=>$checkbox,
											'title'=>$announcement->title,
											'image'=>$image,
											'description'=>$description,
											'status'=>$status,
											'uploaded'=>date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($upload_date)),
											'action'=>$edit_btn.'&nbsp;'.$delete_btn.'&nbsp;'.$active_status.'&nbsp;'.$setting_btn,
										);
										$x++;
}
$table_data['iTotalRecords']					=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
