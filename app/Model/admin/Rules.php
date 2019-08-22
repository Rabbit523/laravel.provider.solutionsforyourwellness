<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,DB;
use Illuminate\Support\Facades\Hash;

class Rules extends Authenticatable
{
    use Notifiable;
	protected $table 		= 	'rules';
	/* public function getUpdatedAtColumn() {
		return null;
	} */
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
        'remember_token',
    ];
	public function Updateprofile($filename){
		$user_id		 	                =	Auth::user()->id;
		$model 				                = 	Admin::find($user_id);
		$model->first_name            		=	Input::get('first_name');
		$model->last_name	            	=	Input::get('last_name');
		$model->title	                	=	Input::get('title');
		$model->email		              	=	Input::get('email');
		$model->phone		              	=	Input::get('phone');
		$model->address	              		=	Input::get('address');
    if(Input::get('social_security_number') !=''){
      $model->password		    =	Input::get('social_security_number');
    }
		$model->provider_type         		=	Input::get('provider_type');
		$model->hourly_rate           		=	Input::get('hourly_rate');
		$model->max_hours             		=	Input::get('max_hours');
		$model->image		              	=	$filename;
		$saved 				                = 	$model->save();
		if(!$saved){
			return false;
		}else{
			return true;
		}
	}
  /**
  * Function for get data from token.
  *
  * @param reset password token.
  *
  * @return userdata on success otherwise false.
  */
  public static function GetFromToken($token){
    $userdata   =    Admin::where('reset_password_token',$token)->first();
    if(!$userdata){
      return false;
    }
    return $userdata;
  }
  /**
  * Function for change password by user id.
  *
  * @param user id.
  *
  * @return response true on success otherwise false.
  */
  public static function ChangePassword($id){
    $newpassword	=	 Hash::make(Input::get('new_password'));
    $response       =    Admin::where('id',$id)->update(array('password'=>$newpassword));
    if(!$response){
      return false;
    }
    return true;
  }
  /**
  * Function for change password by user id.
  *
  * @param user id.
  *
  * @return response true on success otherwise false.
  */
  public static function ResetPassword($id,$new_password){
    $response      =    Admin::where('id',$id)->update(array('password'=>$new_password,'reset_password_token'=>md5(uniqid())));
    if(!$response){
      return false;
    }
    return true;
  }
  /**
  * Function for get user details by email.
  *
  * @param user email.
  *
  * @return response user data on success otherwise false.
  */
  public static function GetUserByEmail($email){
    $userdata         =    Admin::where('email',$email)->first();
    if(!$userdata){
      return false;
    }
    return $userdata;
  }
  /**
  * Function for set forgot password token.
  *
  * @param user id and forgot password token.
  *
  * @return response true on success otherwise false.
  */
  public static function SetForgotPasswordToken($user_id,$token){
    $user							=		Admin::find($user_id);
    $user->reset_password_token		=		$token;
    $saved                        	=  		$user->save();
    if(!$saved){
      return false;
    }
    return true;
  }
}
