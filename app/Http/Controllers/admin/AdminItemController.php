<?php
	namespace App\Http\Controllers\admin;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\admin\AdminUser;	
	use App\Model\admin\AdminItem;	
	use App\Model\admin\Media_model;	
	use App\Model\admin\AdminCategory;	
	use App\Model\EmailAction;
	use App\Model\EmailTemplate;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class AdminItemController extends BaseController {
		function adminsessionStore(){
			$session_name  	= 	'all_item_ids';
			$session_value  = 	explode(',',ltrim(Input::get('item_ids'),','));
			Session::put($session_name,$session_value);
		}
		function AdminAddItem(){
			$all_item_ids	=	Session::get('all_item_ids');
			foreach($all_item_ids as $ids){
				$media[$ids]  		= 	Media_model::where('item_id',$ids)->get()->count();
			}
			$categorydetails	= 	AdminCategory::get();
			$userdetails  		= 	AdminUser::where('user_role_id',0)->get();
			$itemdetails 		= 	AdminItem::whereIn('deballage_products.id',$all_item_ids)->orderBy('id','desc')->get()->toArray();
			return view('admin.pages.items.createitem', ['categorydetails'=>$categorydetails,'itemdetails'=>$itemdetails,'usersdetails'=>$userdetails,'mediadetails'=>$media]);
	 }
	 function AdminUpdateItem(){
		$data	=	array(
					'item_id'=>Input::get('item_id'),
					'user_id'=>Input::get('user_id'),
					'dealers_price'=>Input::get('dealers_price'),
					'retails_price'=>Input::get('retails_price'),
					'description'=>Input::get('description'),
					'category'=>Input::get('category'),
					'dealers_only'=>Input::get('dealers_only'),
				);
		$item_obj = new AdminItem;		
		$item_obj->AdminUpdateItem($data);
		}
	function UpdateAllItems(){
			$itemdata = Input::get('itemdata');
			if(!empty($itemdata)){
				$item_obj = new AdminItem;
				foreach($itemdata as $item){
					$item_obj->AdminUpdateItem($item);		
				}
			}	
			Session::flash('flash_message_success', trans("Items successfully created."));
			return Redirect::to(URL::route('adminitemlist'));
	   }
	 public function editItem($id=0){
		 if(Input::isMethod('post')){
			$rules = array(
			/* 'dealers_price'		=> 'required',
			'retails_price'    		=> 'required',
			'category'     			=> 'required',
			'dealers_only'     		=> 'required',
			'user'     				=> 'required',
			'description'     		=> 'required', */
			);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			  } else{
					$dealers_price	=	Input::get('dealers_price');
					$retails_price	=	Input::get('retails_price');
					$category		=	Input::get('category');
					$dealers_only	=	Input::get('dealers_only');
					$user_id		=	Input::get('user');
					$description	=	Input::get('description');
					AdminItem::where('id',$id)->update(['dealers_price' => $dealers_price,'retails_price'=>$retails_price,'category'=>$category,'dealers_only'=>$dealers_only,'user_id'=>$user_id,'description'=>$description]);
					if($user_id){
						Media_model::where('item_id',$id)->update(['user_id' => $user_id]);
					}
					Session::flash('flash_message_success', trans("Item successfully updated."));
					return Redirect::to(URL::route('adminitemlist'));
				}
		   }else{
			   $items 			= 	AdminItem::where('id',$id)->first();
			   $categorydetails = 	AdminCategory::get();
			   $usersdetails 	= 	AdminUser::where('user_role_id',0)->get();
			   $media  			= 	Media_model::where('item_id',$id)->get()->count();
			   return view('admin.pages.items.edititem', ['items' => $items,'categorydetails'=>$categorydetails,'usersdetails'=>$usersdetails,'mediadetails'=>$media]);
		   }	
		}	
	 function itemslist(){
		$itemdetails 	= 	AdminItem::orderBy('id','desc')->limit(10)->get()->toArray();
		foreach($itemdetails as &$item){
			$users 		= 	AdminUser::select('NomeUtente')->where('IDantiquario',$item['user_id'])->get()->toArray();
			$username 	=	"";
			if(!empty($users)){
				$username 	=	$users[0]['NomeUtente'];	
			}
			$item['NomeUtente'] =	$username;
			$categories 	= 	AdminCategory::select('Nome')->where('IDcategoria',$item['category'])->get()->toArray();
			$category_name 	=	"";
			if(!empty($categories)){
				$category_name 	=	$categories[0]['Nome'];	
			}
			$item['Nome']	=	$category_name;
		}
			return view('admin.pages.items.itemslist', ['items' => $itemdetails]);
	 }
	 
	 // function to display data in databale using ajax
	
	function ajaxloaditem(){
		$user			=	Auth::user();
		$length			= 	Input::get('iDisplayLength');
		$start			= 	Input::get('iDisplayStart');
		$search			= 	Input::get('sSearch');		
		$total_data 	=	AdminItem::orderby('id','desc')->get()->toArray();
		$total_filtered_data	=	count($total_data);
		$total_data				=	count($total_data);
		$user_id	=	$user->id;
		if($search != null){			
			$itemdetails 		= 	AdminItem::select('deballage_products.*','categorie.Nome','user.NomeUtente')->leftJoin('categorie', 'deballage_products.category', '=', 'categorie.IDcategoria')->leftJoin('user', 'deballage_products.user_id', '=', 'user.IDantiquario')->where(function ($query) use ($user_id,$search) {
										;
									})->where(function ($query) use ($user_id,$search) {
										$query->where('deballage_products.dealers_price', 'LIKE', '%'.$search.'%')
											  ->orWhere('deballage_products.retails_price', 'LIKE', '%'.$search.'%')
											  ->orWhere('categorie.Nome', 'LIKE', '%'.$search.'%')
											  ->orWhere('deballage_products.description', 'LIKE', '%'.$search.'%')
											  ->orWhere('deballage_products.dealers_only', 'LIKE', '%'.$search.'%')
											  ->orWhere('user.NomeUtente', 'LIKE', '%'.$search.'%');
									})->orderBy('deballage_products.id','desc')->limit($length)->offset($start)->get()->toArray();	
		}else{
			$itemdetails			=	AdminItem::select('deballage_products.*','categorie.Nome','user.NomeUtente')->leftJoin('categorie', 'deballage_products.category', '=', 'categorie.IDcategoria')->leftJoin('user', 'deballage_products.user_id', '=', 'user.IDantiquario')->orderBy('id','desc')->limit($length)->offset($start)->get()->toArray();
		}
		$table_data		=	array();
		foreach($itemdetails as &$item){
			$categories 	= 	AdminCategory::select('Nome')->where('IDcategoria',$item['category'])->get()->toArray();
			$category_name 	=	"";
			if(!empty($categories)){
				$category_name 	=	$categories[0]['Nome'];	
			}
			$item['category_name']	=	$category_name;
		}
		return view('admin.pages.items.itemajax', ['itemdetails' => $itemdetails,'total_data' => $total_data,'total_filtered_data' => $total_filtered_data]);
	}
	/* This is for get Single image by id for a item 
	*/
	function GetItemImageById($item_id){	
		$mediaImages	=	Media_model::Select('image')->where('item_id',$item_id)->get()->toArray();
		return $mediaImages;
	}
	public function deleteItem(){
		$id = Input::get('item_id');
		 if($id){
			$item_images 	= 	$this->GetItemImageById($id);
			$x 		= 	100;
			$y 		= 	100;
			for($i=1;$i<=6;$i++){
				if($i == 1){
					$path 		= 	public_path('uploads/items/');	
				}else{	
					$path 	=	public_path('uploads/items/'.$x.'x'.$y.'/');				
					$x = $x+100;
					$y = $y+100;
				}			
				foreach($item_images as $row){	
					File::delete($path. $row['image']);
				}
			}
			AdminItem::where('id',$id)->delete();
			Media_model::where('item_id',$id)->delete();
			echo 1;
			/* Session::flash('flash_message_success', trans("Item successfully deleted."));
			return Redirect::to('admin/items-list'); */
		 }else{
			$checkboxdata = Input::get('checkboxvalue');
			if(!empty($checkboxdata)){				
				for($i=0; $i < count($checkboxdata); $i++){
					$item_images = $this->GetItemImageById($checkboxdata[$i]);	
					$path1 		= 	public_path('uploads/items/');							
					$path2 		= 	public_path('uploads/items/100x100/');							
					$path3 		= 	public_path('uploads/items/200x200/');							
					$path4 		= 	public_path('uploads/items/300x300/');							
					$path5 		= 	public_path('uploads/items/400x400/');							
					$path6 		= 	public_path('uploads/items/500x500/');							
					foreach($item_images as $row){	
						File::delete($path1. $row['image']);
						File::delete($path2. $row['image']);
						File::delete($path3. $row['image']);
						File::delete($path4. $row['image']);
						File::delete($path5. $row['image']);
						File::delete($path6. $row['image']);
					}
				}
				AdminItem::whereIn('id', $checkboxdata)->delete();
				Media_model::whereIn('item_id',$checkboxdata)->delete();
				Session::flash('flash_message_success', trans("Item successfully deleted."));
				return Redirect::to('admin/items-list');
			}else{ 
				return Redirect::to('admin/items-list');
			}
		}
	}
	 public function GetImageNameByMediaID($media_id){
		 $imagedata = Media_model::Select('image')->where('id',$media_id)->get()->toArray();
		 return $imagedata;
	 }
	 public function deleteMediaImages(){
			$item_id 		= 	Input::get('item_id');
			$media_id 		= 	Input::get('media_id');
			$item_images 	= 	$this->GetImageNameByMediaID($item_id);
			if($item_id!=""){
			$x 		= 	100;
			$y 		= 	100;
			for($i=1;$i<=6;$i++){
				if($i == 1){
					$path 		= 	public_path('uploads/items/');	
				}else{	
					$path 	=	public_path('uploads/items/'.$x.'x'.$y.'/');				
					$x = $x+100;
					$y = $y+100;
				}			
				foreach($item_images as $row){	
					File::delete($path. $row['image']);
				}
			}
			Media_model::where('id',$media_id)->delete();
			$mediacount	=	Media_model::where('item_id',$item_id)->get()->count();
			echo $mediacount;
		}	
	}
	 function GetMediaImages($item_id = 0){
			if($item_id == 0){
				$item_id		=	Input::get('id');
			}
			if($item_id!=""){
				$mediadetails 		= 	Media_model::where('item_id',$item_id)->get();
				echo view('admin.pages.items.mediaImages',['mediadetails' => $mediadetails]);
			}
		}
	 /* This is for upload multiple images and create multiple items 
	*/
	function AdminUploadImages(){
		$images		= 	Input::file('files');
		$files 		=	array();
		if(!empty($images)){
			foreach($images as $image){
				$filename  	= 	time() . '_'.$image->getClientOriginalName();
				$path 		= 	public_path('uploads/items/' . $filename);
				\Image::make($image->getRealPath())->save($path);
					$x 		= 	100;
					$y 		= 	100;
				for($i=1;$i<6;$i++){
					$path 	=	public_path('uploads/items/'.$x.'x'.$y.'/'. $filename);
					\Image::make($image->getRealPath())->resize($x,$y)->save($path);
					$x = $x+100;
					$y = $y+100;
				}
				$item			=	new AdminItem;
				$item->picture1	=	$filename;
				$item->save();
				$item_id		=	$item->id;

				$files[]		=	array(
										'id'=>$image->getClientOriginalName(),
										'name'=>$image->getClientOriginalName(),
										'size'=>$image->getClientSize(),
										'type'=>$image->getClientOriginalExtension(),
										'user'=>$image->getClientOriginalName(),
										'insert'=>$image->getClientOriginalName(),
										'url'=>$image->getClientOriginalName(),
										'thumbnailUrl'=>$image->getClientOriginalName(),
										'deleteUrl'=>$image->getClientOriginalName(),
										'deleteType'=>'DELETE',
										'item_id'=>$item_id,
									);				
			}
		}
		return Response::json(array('files'=>$files));
	}
    // Upload multiple images for perticular item.
	function UploadMultipleImages(){
		$images		= 	Input::file('files');
		$item_id	= 	Input::get('item_id');
		$files 		=	array();
		if(!empty($images)){
			foreach($images as $image){
				$filename  	= 	time() . '_'.$image->getClientOriginalName();
				$path 		= 	public_path('uploads/items/' . $filename);
				\Image::make($image->getRealPath())->save($path);
					$x 		= 	100;
					$y 		= 	100;
				for($i=1;$i<6;$i++){
					$path 	=	public_path('uploads/items/'.$x.'x'.$y.'/'. $filename);
					\Image::make($image->getRealPath())->resize($x,$y)->save($path);
					$x = $x+100;
					$y = $y+100;
				}
				$media			=	new Media_model;
				$media->image	=	$filename;
				$media->item_id	=	$item_id;
				$media->save();
				$media_id		=	$media->id;
				$files[]		=	array(
										'id'=>$image->getClientOriginalName(),
										'name'=>$image->getClientOriginalName(),
										'size'=>$image->getClientSize(),
										'type'=>$image->getClientOriginalExtension(),
										'user'=>$image->getClientOriginalName(),
										'insert'=>$image->getClientOriginalName(),
										'url'=>$image->getClientOriginalName(),
										'thumbnailUrl'=>$image->getClientOriginalName(),
										'deleteUrl'=>$image->getClientOriginalName(),
										'deleteType'=>'DELETE',
										'id'=>$media_id,
									);					
			}
		}
		return Response::json(array('files'=>$files));
	}
	function EditSingleImage(){
		$images		= 	Input::file('files');
		$item_id	= 	Input::get('item_id');
		$files 		=	array();
		if(!empty($images)){
			foreach($images as $image){
				$filename  	= 	time() . '_'.$image->getClientOriginalName();
				$path 		= 	public_path('uploads/items/' . $filename);
				\Image::make($image->getRealPath())->save($path);
					$x 		= 	100;
					$y 		= 	100;
				for($i=1;$i<6;$i++){
					$path 	=	public_path('uploads/items/'.$x.'x'.$y.'/'. $filename);
					\Image::make($image->getRealPath())->resize($x,$y)->save($path);
					$x = $x+100;
					$y = $y+100;
				}
				$item			=	AdminItem::find($item_id);
				$item->picture1	=	$filename;
				$item->save();
				$files[]		=	array(
										'id'=>$image->getClientOriginalName(),
										'name'=>$image->getClientOriginalName(),
										'size'=>$image->getClientSize(),
										'type'=>$image->getClientOriginalExtension(),
										'user'=>$image->getClientOriginalName(),
										'insert'=>$image->getClientOriginalName(),
										'url'=>$image->getClientOriginalName(),
										'thumbnailUrl'=>$image->getClientOriginalName(),
										'deleteUrl'=>$image->getClientOriginalName(),
										'deleteType'=>'DELETE',
										'uploadname'=>$filename,
									);					
			}
		}
		return Response::json(array('files'=>$files));
	}	
		
		public function activeItem($id=0){
			$user = AdminItem::where('id',$id)->first();
			if($user->status == 'Active'){
				AdminItem::where('id',$id)->update(['status' => 'Deactive']);
				Session::flash('flash_message', trans("Item successfully deactivated."));
				return Redirect::to('admin/items-list');
			}else{
				AdminItem::where('id',$id)->update(['status' => 'Active']);
				Session::flash('flash_message', trans("Item successfully activated."));
				return Redirect::to('admin/items-list');
			}
		}
}