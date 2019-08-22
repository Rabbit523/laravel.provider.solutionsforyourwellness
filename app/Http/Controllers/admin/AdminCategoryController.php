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

	class AdminCategoryController extends BaseController {		
		public function createcategory(){
		 if(Input::isMethod('post')){
			$rules = array(
			'category_name'			=> 'required',
			);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			  } else {
						$category					=	new AdminCategory;
						$category->category_name	=	Input::get('category_name');
						$category->description		=	Input::get('description');
						$category->save();
						Session::flash('flash_message_success', trans("Category successfully created."));
						return Redirect::to('admin/category-list');
						}
		 }else{
				return view('admin.pages.category.createcategory');
			  }
	   }
	 public function editCategory($id=0){
		 if(Input::isMethod('post')){
			$rules = array(
			'category_name'			=> 'required',
			);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			  } else {
					$category		=	Input::get('category_name');
					$description	=	Input::get('description');
					AdminCategory::where('id',$id)->update(['category_name' => $category,'description'=>$description]);
					Session::flash('flash_message_success', trans("Category successfully updated."));
					return Redirect::to('admin/category-list');
				}
		   }else{
			   $category 			= 	AdminCategory::where('id',$id)->first();
			   return view('admin.pages.category.editcategory', ['category'=>$category]);
		   }	
		}	
	 function categorylist(){
			if(!Auth::check()){
				Return Redirect::to('admin');
			}
			$categorydetails = AdminCategory::get();
			return view('admin.pages.category.categorylist', ['categorydetails' => $categorydetails]);
	 }
	 // function to display data in databale using ajax
	
	function ajaxloadcategory(){
		$user			=	Auth::user();
		$length			= 	Input::get('iDisplayLength');
		$start			= 	Input::get('iDisplayStart');
		$search			= 	Input::get('sSearch');		
		$total_data 	=	AdminItem::orderby('id','desc')->get()->toArray();
		$total_filtered_data	=	count($total_data);
		$total_data				=	count($total_data);
		$user_id	=	$user->id;
		if($search != null){			
			$itemdetails 		= 	AdminItem::select('items.*','category.category_name','users.first_name','users.last_name')->join('category', 'items.category', '=', 'category.id')->join('users', 'items.user_id', '=', 'users.id')->where(function ($query) use ($user_id,$search) {
										$query->where('status', '=', 'Active')
											  ->where('user_id', '=', $user_id);
									})->where(function ($query) use ($user_id,$search) {
										$query->where('dealers_price', 'LIKE', '%'.$search.'%')
											  ->orWhere('retail_price', 'LIKE', '%'.$search.'%')
											  ->orWhere('category_name', 'LIKE', '%'.$search.'%')
											  ->orWhere('description', 'LIKE', '%'.$search.'%')
											  ->orWhere('dealers_only', 'LIKE', '%'.$search.'%')
											  ->orWhere('status', 'LIKE', '%'.$search.'%');
									})->orderBy('created_at','desc')->limit($length)->offset($start)->get()->toArray();	
		}else{
			$itemdetails 			= 	AdminItem::where('status','Active')->orderBy('created_at','desc')->limit($length)->offset($start)->get()->toArray();
		}
		$table_data		=	array();
		foreach($itemdetails as &$item){
			$images = Media_model::select('image')->where('item_id',$item['id'])->where('type','main')->get()->toArray();
			$image_name 	=	"";
			if(!empty($images)){
				$image_name 	=	$images[0]['image'];	
			}
			$item['image']	=	$image_name;
			
			$categories 	= 	AdminCategory::select('category_name')->where('id',$item['category'])->get()->toArray();
			$category_name 	=	"";
			if(!empty($categories)){
				$category_name 	=	$categories[0]['category_name'];	
			}
			$item['category_name']	=	$category_name;
		}
		return view('admin.pages.items.itemajax', ['itemdetails' => $itemdetails,'total_data' => $total_data,'total_filtered_data' => $total_filtered_data]);
	}
	public function deleteCategory(){
		$id 			= 	Input::get('category_id');
		$checkboxdata 	= 	Input::get('checkbox');
		 if($id){
			AdminCategory::where('id',$id)->delete();
			echo 1;
		 }
		 if($checkboxdata){
			AdminCategory::whereIn('id',$checkboxdata)->delete();
			Session::flash('flash_message_success', trans("Category successfully deleted."));
			return Redirect::to(URL::route("categorylist"));
		 }else{
			Session::flash('flash_message_error', trans("Please select at least one record."));
			return Redirect::to(URL::route("categorylist")); 
		 }
	 }
		public function activeCategory($id=0){
			$category = AdminCategory::where('id',$id)->first();
			if($category->status == 'Active'){
				AdminCategory::where('id',$id)->update(['status' => 'Deactive']);
				Session::flash('flash_message_success', trans("Item successfully deactivated."));
				return Redirect::to('admin/category-list');
			}else{
				AdminCategory::where('id',$id)->update(['status' => 'Active']);
				Session::flash('flash_message_success', trans("Item successfully activated."));
				return Redirect::to('admin/category-list');
			}
		}
}