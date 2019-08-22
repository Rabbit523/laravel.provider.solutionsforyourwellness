<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
foreach ($userdetails as $user){
	if($user['image']){
		$image 		= 		'<a class="fancybox" href="'.URL::to('/').'/public/uploads/users/'.$user['image'].'">
										<img src="'.URL::to('/').'/public/uploads/users/50x50/'.$user['image'].'" class = "img-thumbnail" alt="profile-image" height = "50px"></a>';
	}else{
		$image 	= 		'<a class="fancybox" href="'.URL::to('/').'/public/uploads/users/noprev.jpg">
									<img src="'.URL::to('/').'/public/uploads/users/noprev.jpg" class = "img-thumbnail" alt="profile-image" height = "50px"></a>';
	}
	$edit_btn		=		'<a href="'.URL::route('adminedituser',$user['id']).'" class="btn btn-info btn-xs"><i class="fa fa-edit "></i></a>';
	$delete_btn	=		'<button class="btn btn-danger btn-xs btn-delete-user" onclick="delete_user(this)" data-id="'.URL::route('admindeleteuser',$user['id']).'" title="delete" ><i class="fa fa-trash"></i></button>';
	$table_data['aaData'][]			=	array(
											'sno'=>$x,
											'name'=>$user['first_name'].' '.$user['last_name'],
											'image'=>$image,
											'username'=>$user['username'],
											'email'=>$user['email'],
											'phone'=>$user['phone'],
											'status'=>'<span class="badge badge-success">'.$user['status'].'</span>',
											'action'=>$edit_btn.'&nbsp;'.$delete_btn,
										);
										$x++;
}
$table_data['iTotalRecords']					=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
