<?php
	namespace App\Http\Controllers\front;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\Pages_model;	
	use App\Model\Items_model;
	use App\Model\Media_model;
	use App\Model\Category_model;
	use App\Model\EmailAction;
	use App\Model\EmailTemplate;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class PagesController extends BaseController{
	 function homepage(){
			return view('pages.homepage');
	 }
	 function dashboard(){
		$user			=	Auth::user();
		$totalitems 	=	$this->getTotalitems();
		Session::put('totalItems',$totalitems);
		$itemdetails 	= 	Items_model::where('user_id',$user->id)->orderBy('id','desc')->limit(10)->get()->toArray();
		foreach($itemdetails as &$item){
			$categories 	= 	Category_model::select('Nome')->where('IDcategoria',$item['category'])->get()->toArray();
			$category_name 	=	"";
			if(!empty($categories)){
				$category_name 	=	$categories[0]['Nome'];	
			}
			$item['Nome']	=	$category_name;
		}
		return view('pages.dashboard',['itemdetails'=>$itemdetails]);		
	 }
	 function register(){
			return view('pages.register');
	 }
	 function getTotalitems(){
		$user			=	Auth::user();
		$users = DB::table('deballage_products')
				 ->select(DB::raw('count(*) as user_count'))
				 ->where('user_id',$user->id)
				 ->get();
		foreach($users as $row){
			return $row->user_count;
		}			 
	}
}