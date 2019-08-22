<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model\AdminUsers;
use App\Model\EmailTemplate;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;

class LoginController extends Controller
{
	public function index(){
		if(Auth::check()){
			Return Redirect::to('admin');
		}
		if(Input::isMethod('post')){
			$rules	=	array(
				'email'     => 	'Required|Email',
				'password'  =>	'Required',
			);
			$vali 	= 	Validator::make(Input::all(), $rules);
			if ($vali->fails()) {
				return Redirect::back()->withErrors($vali)->withInput();
			}else{
				 $userdata = array(
					'email'     => Input::get('email'),
					'password'  => Input::get('password'),
				);
				 if (Auth::attempt($userdata,true)) {
					//Session::forget('failed_attampt_login');
					Session::flash('flash_notice', 'You are now logged in!');
					return redirect::to('admin')->with('message','You are now logged in!');
				}else{
					Session::flash('error', 'Email or Password is incorrect.');
					return Redirect::back() ->withInput();
				}
			}
		}else{	
			return view::make('admin.loginRegister.index');
		}
	}
	
	public function register(){
		if(Auth::check()){
			Return Redirect::to('admin');
		}
		if(Input::isMethod('post')){
			$rules = array(
				'full_name' => 	'Required|Min:3|Max:80',
				'username' 	=> 	'Required|Min:3|Max:80|Alpha|Unique:users',
				'email'     => 	'Required|Between:3,64|Email|Unique:users',
				'password'  =>	'Required|Between:6,10',
				'repassword'=>	'Required|Between:6,10|same:password'
			);
			$vali 	= 	Validator::make(Input::all(), $rules);
			if ($vali->fails()) {
				return Redirect::back()->withErrors($vali)->withInput();
			}else{
				$auth 	= 	new AdminUsers();
				$save 	=	$auth->create_user();
				if($save){
					Session::flash('success',  trans("User successfully Register."));
					return Redirect::to('admin/login');
				}else{
					return Redirect::back()->withErrors($vali)->withInput();
				}
			}
		}else{
			return view::make('admin.loginRegister.register');
		}
	}
	public function dashboard(){
		if(Auth::check()){
			return view::make('admin/dashbord');
		}else{
			Return Redirect::to('admin/login');
		}
	}
	public function logout(){
		Auth::logout();
		Session::flash('success', 'You are now logged out!');
		return Redirect::to('admin/login');
	}
	public function forgotpassword(){
		if(Auth::check()){
			Return Redirect::to('admin');
		}
		if(Input::isMethod('post')){
			$messages = array(
				'email.required' 		=> trans('The email field is required.'),
				'email.email' 			=> trans('The email must be a valid email address.'),
			);
			$validator = Validator::make(
				Input::all(),
				array(
					'email' 			=> 'required|email',
				),$messages
			);
			if ($validator->fails()){		
				return Redirect::back()->withErrors($validator)->withInput();
			}else{
				$email			=	Input::get('email');
				$auth 			= 	new AdminUsers();
				$forgot			=	$auth->forgotpassword($email);
				
				if(!empty($forgot)){
					$emailinfo	=	EmailTemplate::where('action','=','forgot_password')->first();
					$cons 		= 	explode(',',$emailinfo->options);
					$constants 	= 	array();
					foreach($cons as $key=>$val){
						$constants[] = 	'{'.$val.'}';
					}
					
				}
			}
		}else{
			return view::make('admin/loginRegister/forgot');
		}
	}
}
