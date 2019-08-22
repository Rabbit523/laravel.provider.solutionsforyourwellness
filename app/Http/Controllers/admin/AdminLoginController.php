<?php
	namespace App\Http\Controllers\admin;
	//use Illuminate\Http\Request;
	use App\Http\Controllers\BaseController;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\admin\Admin;
	use App\User;
	use App\Model\admin\AdminSettings;
	use App\Model\EmailAction;
	use App\Model\EmailTemplate;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Image,Toast;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	//use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class AdminLoginController extends BaseController {
		public function login(){
		if(Input::isMethod('post')){
			$rules = array(
			'email'   => 'required',
			'password'=> 'required',
		);
			$validator = Validator::make(Input::all(), $rules);
			 if ($validator->fails()) {
				//$messages = $validator->messages();
				 return Redirect::back()->withErrors($validator)->withInput();
			} else {
					$user_role  = DB::table('users')->select('role_id')->where('email',Input::get('email'))->get()->first();
					if(!empty($user_role)){
						$role_id  = $user_role->role_id;
						 	$userdata = array(
								 	'email'  		=> Input::get('email'),
									'password'  	=> Input::get('password'),
									'role_id'		=> $role_id,
							);
						 if(Auth::attempt($userdata,true)) {
							 Toast::success(trans('You have successfully logged in!'));
							 return redirect()->route('admindashboard');
						}else{
							Toast::error(trans('Username or Password is incorrect!'));
							return Redirect::back()->withInput();
						}
					}else{
						Toast::error(trans('Invalid login details!'));
						return Redirect::back();
					}
					}
				}else{
					return View::make('admin.login.login');
				}
		}
	 public function logout(){
		Auth::logout();
		Toast::success(trans('You are now logged out!'));
		return Redirect::to('/')->with('message', 'You are now logged out!');
	 }
 public function changepassword(){
					$currentpassword	=		Input::get('old_password');
					$newpassword			=	  Hash::make(Input::get('new_password'));
					$confirmpassword	=	  Hash::make(Input::get('confirm_password'));
					$userid						=		Auth::user()->id;
					$savedpassword		=		Auth::user()->password;
						if(Hash::check($currentpassword,$savedpassword)){
 								Admin::ChangePassword($userid);
 								echo 1;
 					  }else{
 									echo 0;
 					   	}
	 }
	 public function admin_change_pin(){
					 $currentpin	=		Input::get('old_pin');
					 $newpin			=	  Input::get('new_pin');
					 $confirmpin	=	  Input::get('confirm_pin');
					 $userid			=		Auth::user()->id;
					 $savedpin		=		Auth::user()->social_security_number;
						 if($currentpin==$savedpin){
									 Admin::where('id',$userid)->update(array('social_security_number' => $newpin));
									 echo 1;
							 }else{
										 echo 0;
								 }
		}
	 public function EditProfile(){
		 if(Input::isMethod('post')){
			 if(Input::get('social_security_number')!=null){
		     $security_number_validation = 'required|digits:4';
		     }else{
		       $security_number_validation = '';
		     }
			$rules = array(
			'first_name'						=> 'required',
			'last_name'    					=> 'required',
			'email'     						=> 'required|email',
			'phone'     						=> 'required|numeric',
			'social_security_number' => $security_number_validation,
			);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			   } else {
							$image				=		Input::file('image');
							if($image){
								$filename 	= 	$this->ImageUpload($image,$folder='users',true);
							}
							$filename			=		isset($filename)?$filename:Auth::user()->image;
							$admin  			=		new Admin;
							$admin->Updateprofile($filename);  //calls function for update profile.
							Toast::success(trans('Profile successfully updated!'));
							return Redirect::back();
				     }
				}else{
					$model					=		new Admin;
					$timezones = DB::table('timezone')->get();
					return View::make('admin.edit_profile',compact('timezones'));
				}
		}
		public function admin_notification_setting(){
		 if(Input::isMethod('post')){
			$rules = array(
			
			);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			   } else {
						$admin  			=		new Admin;
						$admin->UpdateNotificationSettings();  //calls function for update profile.
						Toast::success(trans('Notification settings successfully updated!'));
						return Redirect::back();
				     }
				}else{
					return View::make('admin.edit_profile');
				}
		}
	   public function register(){
		$rules = array(
        'username'       	=> 'required',
        'email'         	=> 'required|email|unique:users',
        'password'			=> 'required|min:6',
        'confirm_password'  => 'required|same:password'
		);
		$validator = Validator::make(Input::all(),$rules);
		 if ($validator->fails()) {
			/* $messages = $validator->messages();
			return Redirect::to('insert')
            ->withErrors($validator)->withInput();	 */
			return Redirect::back()->withErrors($validator)->withInput();
		  } else {
					$user								=		new Admin;
					$user->user_role_id	=		1;
					$user->username			=		Input::get('username');
					$user->email				=		Input::get('email');
					$user->password			= 	Hash::make(Input::get('password'));
					$user->save();
					Toast::success(trans('Record inserted successfully!'));
					return Redirect::to(URL::route('adminlogin'));
				}
	   }
  /**
     * function for reset password.
     *
     * @param validate token
     *
     * @return response with user data on success otherwise error.
     */
	public function resetPassword($reset_password_token=null){
	  if($reset_password_token!=null){
		if(Input::isMethod('post')){
			$rules = array(
				'newpassword'	   => 'required',
				'confirmpassword'  => 'required|same:newpassword'
			);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			  } else {
					$reset_password_token	=	Input::get('reset_password_token');
					$newpassword			=	Hash::make(Input::get('newpassword'));
					$user					=	Admin::GetFromToken($reset_password_token);
					if(!empty($user)){
						/* calls function for Reset password */
						$response = Admin::ResetPassword($user->id,$newpassword);
						if($response){
							/* email sending to user if password successfully changed. */
							$full_name 				=	$user->first_name.' '.$user->last_name;
							$subject_array =	array();
							$replace_array 			= 	array($full_name);
							/* call email sending function */
							$mail_send = $this->mail_send($action='reset_password',$user->email,$full_name,$subject_array,$replace_array);
							Toast::success(trans('Password successfully changed!'));
							return Redirect::to(URL::route('adminlogin'));
						}else{
							Toast::error(trans('Technical error please try again later!'));
							return Redirect::to(URL::route('adminlogin'));
						}

					}else{
						Toast::error(trans('Your token is mismatch please generate new token!'));
						return Redirect::to(URL::route('adminlogin'));
					}

				  }
				}else{
					 return View::make('admin.login.resetpassword' ,compact('reset_password_token'));
				}
			}else{
				Toast::error(trans('You are using wrong link or your link is expired!'));
				return Redirect::to(URL::route('adminlogin'));
			}

	}
  /**
  * Function for forgot password request.
  *
  * @param null.
  *
  * @return .
  */
	public function forgotpassword(){
		if(Input::isMethod('post')){
			$rules = array(
			'email'  => 'required|email',
			);
			$validator 		= Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			  } else {
					$email				=	Input::get('email');
					$userdetails 		=	Admin::GetUserByEmail($email);
						if(!empty($userdetails)){
								$reset_password_token 	=  	$this->generate_random_string(20); // generate reset password token
								$url					=	URL::route('admin_reset_password',$reset_password_token);
								$full_name 				=	$userdetails->first_name.' '.$userdetails->last_name;
								$subject_array 			=	array();
								$replace_array 			= 	array($full_name,$url,$url);
								/* call email sending function */
								$mail_send = $this->mail_send($action='forgot_password',$userdetails->email,$full_name,$subject_array,$replace_array);
								if($mail_send){
									Admin::SetForgotPasswordToken($userdetails->id,$reset_password_token);
									Toast::success(trans('Email has been sent to your inbox please check your email to reset your password!'));
									return Redirect::to(URL::route('adminlogin'));
								}else{
											Toast::error(trans('Technical error please try again later!'));
											return Redirect::to(URL::route('admin_forgot_password'));
								}
						}else{
								Toast::error(trans('Your email is not registered with us!'));
								return Redirect::to(URL::route('admin_forgot_password'));
						}
				  }
			}else{
						// load view
						return View::make('admin.login.forgotpassword');
			}
		}
}
