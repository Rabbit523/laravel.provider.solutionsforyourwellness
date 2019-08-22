<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Support\Facades\Hash;

class AdminCategory extends Authenticatable
{
    use Notifiable;
	protected $table = 'categorie';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
		
	public function create_user(){
		return AdminUsers::create([
            'username' 	=> 	Input::get('username'),
            'email' 	=> 	Input::get('email'),
            'password' 	=> 	Hash::make(Input::get('password')),
            'name' 		=> 	Input::get('full_name'),
        ]);
	
	}
	public function forgotpassword($email){
		$forgot_string	=	'';
		$userdetails	=	AdminUsers::where('email',$email)->first();
		$return			=	array();
		if(!empty($userdetails)){
			$forgot_string		= 	md5($userdetails->email);
			AdminUsers::where('email',$email)->update(array('forgot_password_token'=>$forgot_string));
			$return['string']	=	$forgot_string;
			$return['email']	=	$userdetails->email;
			return $return;
		}
	}
}