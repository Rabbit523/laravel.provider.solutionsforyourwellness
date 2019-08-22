<?php
	namespace App\Http\Controllers\admin;
	use App\Http\Controllers\BaseController;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\admin\AdminUser;
	use App\Model\admin\AdminProvince_model;
	use App\Model\admin\AdminNations_model;
	use App\Model\EmailAction;
	use App\Model\EmailTemplate;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Image;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class AdminUserController extends BaseController {
/**
* Function to Create user
*
*@return users list page
*/
		public function createuser(){
		 Input::replace($this->arrayStripTags(Input::all()));
			if(Input::isMethod('post')){
				$rules = array(
				'first_name'    	=> 'required',
				'last_name'  		=> 'required',
				'username'      	=> 'required',
				'email'      		=> 'required|email|unique:users,email',
				'password'   		=> 'required',
				'confirm_password'  => 'required|same:password',
				'phone'  			=> 'required',
				);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()){
					$messages = $validator->messages();
					 return Redirect::back()->withErrors($validator)->withInput();
				}else{
						$first_name 			=  	Input::get('first_name');
						$last_name 				=  	Input::get('last_name');
						$email 						=  	Input::get('email');
						$image						=		Input::file('image');
						$verify_string 		=  	$this->generate_random_string(20); // generate random string
						if($image){
							 /* image uploading process start */
							$filename  	= 	time() . '_'.$image->getClientOriginalName();
							$path 			= 	public_path('uploads/users/' . $filename);
							$path2 			= 	public_path('uploads/users/50x50/' . $filename);
							Image::make($image->getRealPath())->resize(50,50)->save($path2);
							Image::make($image->getRealPath())->save($path);
								$x 		= 	100;
								$y 		= 	100;
							for($i=1;$i<6;$i++){
								$path 	=	public_path('uploads/users/'.$x.'x'.$y.'/'. $filename);
								Image::make($image->getRealPath())->resize($x,$y)->save($path);
								$x = $x+100;
								$y = $y+100;
							}
							 /* image uploading process end */
						}
						$user 		= 	new AdminUser;
						$saveuser 	= 	$user->SaveUser($filename,$verify_string);  //calls function for create user.
						if($saveuser){
							/* Email sending process starts */
							$loginurl					=		url('/login');
							$full_name				=  	$first_name.' '.$last_name;
							$validateurl      =  	URL('/login/'.$verify_string);
							$route_url				=		URL::to('/login/'.$verify_string);
							$replace_array 		= 	array($full_name,$validateurl,$route_url);
							/* call email sending function */	$this->mail_send($action='account_verification',$email,$full_name,$replace_array);
						}else{
							Session::flash('flash_error', trans("Technical error please try again later."));
							return redirect()->route('userslist');
						}
						Session::flash('flash_success', trans("User is successfully registered & email has been sent to user with verification link."));
						return redirect()->route('userslist');
					}
		}else{
				return View::make('admin.pages.users.add');
			 }
	 }
public function VerifyUser($verify_string=null){
	$userdetails	=	User_model::where('verify_string',$verify_string)->get()->toArray();
	if(!empty($userdetails)){
		User_model::where('verify_string',$verify_string)->update(array('is_verified'=>1,'verify_string'=>''));
		$email 					=  	$userdetails[0]['email'];
		$password 				=  	$userdetails[0]['password'];
		$full_name				=  	$userdetails[0]['first_name'].' '.$userdetails[0]['last_name'];
		$replace_array 			= 	array($full_name,$email,$password);
		/* choose email template and actions */
		$template				=	EmailTemplate::where('action','=','user_registration')->get();
		/* call email sending function */
		$send_mail 				= 	$this->mail_send($email,$full_name,$template,$replace_array);
		/* Email sending process ends */
		Session::flash('flash_success', trans("Your email is successfully verified please login and check your email."));
		return View::make('pages.users.login');
	}else{
		return View::make('errors.404');
	}
}
/**
* Function to edit user
*
* @param user id (default null )
*
* @return users list page
*/
	public function editUser($id=0){
	 if(Input::isMethod('post')){
			$rules = array(
			'first_name'    	=> 'required',
			'last_name'  		=> 'required',
			'username'      	=> 'required',
			'email'      		=> 'required|email|unique:users,id',
			'phone'  			=> 'required',
			);
		$validator = Validator::make(Input::all(),$rules);
		 if ($validator->fails()) {
			$messages = $validator->messages();
			return Redirect::back()->withErrors($validator)->withInput();
		  } else {
							if(Input::file('image')){
								$image 					=  	Input::file('image');
								$filename  			= 	time() . '_'.$image->getClientOriginalName();
								$path 					= 	public_path('uploads/users/' . $filename);
								$path2 					= 	public_path('uploads/users/50x50/' . $filename);
								Image::make($image->getRealPath())->resize(50,50)->save($path2);
								Image::make($image->getRealPath())->save($path);
									$x 		= 	100;
									$y 		= 	100;
								for($i=1;$i<6;$i++){
									$path 	=	public_path('uploads/users/'.$x.'x'.$y.'/'. $filename);
									Image::make($image->getRealPath())->resize($x,$y)->save($path);
									$x = $x+100;
									$y = $y+100;
								}
							}else{
								$user			=	AdminUser::GetById($id);  // getting user data by id.
								$filename	=	$user->image;
							}
							$update_user = AdminUser::UpdateUser($id,$filename);    // calls function for update user data.
								if($update_user){
									Session::flash('flash_success', trans("User successfully updated."));
									return redirect()->route('userslist');
								}else{
									Session::flash('flash_error', trans("Technical error while updating user."));
									return redirect()->route('userslist');
								}
					}
	   }else{
						$user = AdminUser::GetById($id); // calls function for getting user data by id.
						return view('admin.pages.users.edit', ['user' => $user]);
	   }
	}
	/**
	* Function to show user list
	*
	* @return users list page
	*/
		function userslist(){
			if(!Auth::check()){
				Return Redirect::to('login');
			}
			$users = AdminUser::where('user_role_id',0)->orderBy('id','desc')->get();
			return view('admin.pages.users.index', ['records' => $users]);
		}
	 // function to display user data in databale using ajax
	 /**
 	* Function for get users data use in ajax datatable pagination.
 	*
 	* @param null
 	*
 	* @return users ajax list page.
 	*/
	function ajaxloadUser(){
		$length								= 	Input::get('length');
		$start								= 	Input::get('start');
		$search								= 	Input::get('search');
		$totaldata 						= 	AdminUser::CountUsers(); // function counts records in user table.
		$total_filtered_data	=		$totaldata;
		$search								=		$search['value'];
		$order							  =		Input::get('order');
		$column_id						=		$order[0]['column'];
		$column_order					=		$order[0]['dir'];
		if($search != null){
			$userdetails		=		AdminUser::GetUsers($search,$start,$length,$column_id,$column_order);   // calls function for getting users data.
		}else{
			$userdetails			=	AdminUser::GetUsers($search="",$start,$length,$column_id,$column_order);
		}
		$table_data		=	array();
		return view('admin.pages.users.indexajax', ['userdetails' => $userdetails,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
	}
	/**
	* Function for delete users.
	*
	* @param user id (default null )
	*
	* @return users list page
	*/
	public function deleteUser($id=0){
		 if($id){
							$delete = AdminUser::DeleteUser($id);
							if($delete){
											Session::flash('flash_success', trans("User successfully deleted."));
											return redirect()->route('userslist');
							}else{
										Session::flash('flash_error', trans("Technical error please try again later."));
										return redirect()->route('userslist');
							}
		 }else{
					 $checkboxdata = Input::get('checkboxvalue');
					 if(!empty($checkboxdata)){
								$delete = AdminUser::DeleteUser($checkboxdata);
								if($delete){
													Session::flash('flash_success', trans("User successfully deleted."));
													return redirect()->route('userslist');
								}else{
													Session::flash('flash_error', trans("Technical error please try again later."));
													return redirect()->route('userslist');
								}
						}else{
								Session::flash('flash_error', trans("Please select at least 1 user."));
								return redirect()->route('userslist');
							}
		    }
	 }
}
