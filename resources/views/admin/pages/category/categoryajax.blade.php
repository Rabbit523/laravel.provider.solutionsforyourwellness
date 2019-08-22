<?php 
$table_data			=	array();
$table_data['data']	=	array();
foreach ($itemdetails as $item){
	$table_data['data'][]			=	array(
											'checkbox'=>'<input class="rowcheck" name="checkbox[]" type="checkbox" value="'.$item['id'].'">',
											'image'=>"<a class='fancybox' href='". URL::asset('public/uploads/items/'.$item['image'])."'> 	<img src='". URL::asset('public/uploads/items/100x100/'.$item['image']) ."' alt='profile-image' height = '80px'></a>",
											'name'=>$item['first_name']).' '$item['last_name'],
											'dealers_price'=>isset($item['dealers_price'])?"$".$item['dealers_price']:'',
											'retail_price'=>isset($item['retail_price'])?"$".$item['retail_price']:'',
											'dealers_only'=>$item['dealers_only'],
											'category'=>$item['category_name'],
											'description'=>$item['description'],
											'action'=>'<div class="fr"> <a href="'.URL::route('edititem', $item['id']).'" class="btn btn-primary btn-mini">Edit</a> <a href="javascript:void(0);" data-id="'.$item['id'].'" class="btn btn-danger btn-mini delete_item_btn" id="delete_item_btn_'.$item['id'].'">Delete</a></div>',
										);		
}
$table_data['recordsTotal']			=	$total_data;
$table_data['recordsFiltered']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>