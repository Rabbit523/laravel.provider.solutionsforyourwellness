<?php 
$response_data	=	array();
if(!empty($fieldsData)){
	foreach($fieldsData as $fields){
		$columns_data 	=	array();
		foreach ($fields as $column_name=>$column_value){
			if ($column_name=='id'){
				$clm_val = '<label class="pos-rel"><input type="checkbox" name="chk_ids[]" value = "'.$column_value.'"  class="ace allcheck" /><span class="lbl"></span></label>';
			}else{
				if($column_name=='status'){
					$clm_val = '<span class="label label-sm '.(($column_value=='Active')?"label-success":"label-warning").'">'.$column_value.'</span>';
				}else{
					$clm_value 	= 	explode('.', $column_value);
					$last_val	=	end($clm_value);
					if($last_val == 'jpg' || $last_val == 'jpeg' || $last_val == 'png' || $last_val == 'gif' || $last_val == 'psd' || $last_val == 'bmp' || $last_val == 'tiff'){
						$clm_val = "<img style='height:60px;' src='".URL::asset('public/uploads/team/'.$column_value)."'>"; 
					} else{ 
						$clm_val	=  $column_value; 
					}
				}
			}
			$columns_data[]	=	$clm_val;
		}
		if($fields['status'] == 'Active') {
			$status = '<a title="Inactive" data-toggle="tooltip" class="blue" href="'.URL::route('activeinactive', ['key' => $key,'id' => $fields['id']]).'"><i class="ace-icon glyphicon glyphicon-ok"></i></a>';
		}else{
			$status = '<a title="Active" data-toggle="tooltip" class="blue" href="'.URL::route('activeinactive', ['key' => $key,'id' => $fields['id']]).'"><i class="ace-icon glyphicon glyphicon-remove"></i></a>';
		}
		
		$columns_data[]	=	'<div class="hidden-sm hidden-xs action-buttons">'. $status .'<a title="edit" data-toggle="tooltip" class="green" href="'.URL::route('editfields', ['key' => $key,'id' => $fields['id']]).'"><i class="ace-icon fa fa-pencil bigger-130"></i></a> <a title="Delete" data-toggle="tooltip" class="red delete_item_btn" href="javascript:void(0);" data-url = "'.URL::route('deletefields', ['key' => $key,'id' => $fields['id']]).'"><i class="ace-icon fa fa-trash-o bigger-130"></i></a> </div>';
		$response_data[]	=	$columns_data;
	} 
}
echo json_encode(array('recordsTotal'=>$total,'recordsFiltered'=>$total,'data'=>$response_data));die;