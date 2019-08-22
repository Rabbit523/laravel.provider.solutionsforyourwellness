<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
foreach ($certificates as $certificate){
	$edit_btn		=		'<a href="'.URL::route('edit-certificates',$certificate->certificate_id).'" ><i class="mdi-editor-border-color" title="edit"></i></a>';
	$delete_btn		=		'<a href="'.URL::route('delete-certificate',$certificate->certificate_id).'" onclick="delete_confirm()"><i class="mdi-action-delete" title="delete"></i></a>';
	if($certificate->type == 'png' || $certificate->type == 'PNG' || $certificate->type == 'JPG'|| $certificate->type == 'JPEG' || $certificate->type == 'jpg' || $certificate->type == 'jpeg'){
		$file		=		'<a class="fancybox" href="'.WEBSITE_UPLOADS_URL.'certificates/'.$certificate->file.'"><img src="'.WEBSITE_UPLOADS_URL.'certificates/'.$certificate->file.'" style="height:40px" ></a>';
	}else{
		$file		=		'<a href="'.WEBSITE_UPLOADS_URL.'certificates/'.$certificate->file.'" target="_blank" >View file</a>';
	}

	$checkbox = '<input type="checkbox" id="'.'click'.$certificate->certificate_id.'" value="'.$certificate->certificate_id.'" name="id" class="checked_certificates" /><label for="'.'click'.$certificate->certificate_id.'"></label>';





	$table_data['aaData'][]			=	array(
											'sno'=>$checkbox,
											'name'=>$certificate->first_name,
											'subject'=>$certificate->subject,
											'file'=>$file,
											'uploaded at'=>$certificate->created_at,
											'action'=>$edit_btn.'&nbsp;'.$delete_btn.'&nbsp;',
										);
										$x++;
}
$table_data['iTotalRecords']					=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
