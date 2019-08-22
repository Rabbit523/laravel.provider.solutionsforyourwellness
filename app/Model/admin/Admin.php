<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,DB;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable
{
    use Notifiable;
	protected $table 		= 	'users';
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
    $model->timezone             		=	Input::get('timezone');
		$model->image		              	=	$filename;
		$saved 				                = 	$model->save();
		if(!$saved){
			return false;
		}else{
			return true;
		}
	}
	public function UpdateNotificationSettings(){
		$user_id		 	                =	Auth::user()->id;
		$model 				                = 	Admin::find($user_id);
		$notify_only = Input::get('notify_only');
		if(Input::get('field1') == 'on'){
			if(Input::get('unfilled_notify') != null){
				$unfilled_notify = Input::get('unfilled_notify');
			}else{
				$default_unfilled = DB::table('admin_settings')->select('default_time_stay_in_feeds')->where('id',20)->get()->first();
				$unfilled_notify = $default_unfilled->default_time_stay_in_feeds;
			}
			
		}else{
			$unfilled_notify = 'off';
		}
		if(Input::get('field2') == 'on'){
			if(Input::get('x_time_unfilled_notify') != null){
				$x_time_unfilled_notify = Input::get('x_time_unfilled_notify');
			}else{
				$default_time_unfilled_notify = DB::table('admin_settings')->select('unfilled_before_time')->where('id',20)->get()->first();
				$x_time_unfilled_notify = $default_time_unfilled_notify->unfilled_before_time;
			}
		}else{
			$x_time_unfilled_notify = 'off';
		}
		
		
		if(Input::get('field3') == 'on'){
			$clinic_filled_notify = 'on';
		}else{
			$clinic_filled_notify = 'off';
		}
		
		
		if(Input::get('field4') == 'on'){
			$mileage_info_notify = 'on';
		}else{
			$mileage_info_notify = 'off';
		}
		
		
		if(Input::get('field5') == 'on'){
			$clinic_status_notify = 'on';
		}else{
			$clinic_status_notify = 'off';
		}
		
		
		if(Input::get('field6') == 'on'){
			$user_added_notify = 'on';
		}else{
			$user_added_notify = 'off';
		}
		
		
		if(Input::get('field7') == 'on'){
			$admin_added_notify = 'on';
		}else{
			$admin_added_notify = 'off';
		}
		
		
		if(Input::get('field8') == 'on'){
			$certifications_notify = 'on';
		}else{
			$certifications_notify = 'off';
		}
		

		if(Input::get('field9') == 'on'){
			if(Input::get('over_hour_month_notify') != null){
				$over_hour_month_notify = Input::get('over_hour_month_notify');
			}else{
				$over_hour_month_noti = DB::table('admin_settings')->select('default_max_scheduled_hours')->where('id',20)->get()->first();
				$over_hour_month_notify = $over_hour_month_noti->default_max_scheduled_hours;
			}
			
		}else{
			$over_hour_month_notify = 'off';
		}
		

		if(Input::get('field10') == 'on'){
			if(Input::get('over_hour_day_notify') != null){
				$over_hour_day_notify = Input::get('over_hour_day_notify');
			}else{
				$default_max_scheduled_per_day = DB::table('admin_settings')->select('default_max_scheduled_per_day')->where('id',20)->get()->first();
				$over_hour_day_notify = $default_max_scheduled_per_day->default_max_scheduled_per_day;
			}	
		}else{
			$over_hour_day_notify = 'off';
		}
		
		
		if(Input::get('field11') == 'on'){
			if(Input::get('over_hour_clinic_notify') != null){
				$over_hour_clinic_notify = Input::get('over_hour_clinic_notify');
			}else{
				$default_max_scheduled_per_clinic = DB::table('admin_settings')->select('default_max_scheduled_per_clinic')->where('id',20)->get()->first();
				$over_hour_clinic_notify = $default_max_scheduled_per_clinic->default_max_scheduled_per_clinic;
			}
			
		}else{
			$over_hour_clinic_notify = 'off';
		}
		
		
		if(Input::get('field12') == 'on'){
			if(Input::get('over_mileage_month_notify') != null){
				$over_mileage_month_notify = Input::get('over_mileage_month_notify');
			}else{
				$default_max_mileage_per_month = DB::table('admin_settings')->select('default_max_mileage_per_month')->where('id',20)->get()->first();
				$over_mileage_month_notify = $default_max_mileage_per_month->default_max_mileage_per_month;
			}	
		}else{
			$over_mileage_month_notify = 'off';
		}
		
		
		if(Input::get('field13') == 'on'){
			if(Input::get('over_mileage_day_notify') != null){
				$over_mileage_day_notify = Input::get('over_mileage_day_notify');
			}else{
				$default_max_mileage_per_day = DB::table('admin_settings')->select('default_max_mileage_per_day')->where('id',20)->get()->first();
				$over_mileage_day_notify = $default_max_mileage_per_day->default_max_mileage_per_day;
			}	
		}else{
			$over_mileage_day_notify = 'off';
		}
		if(Input::get('field14') == 'on'){
			if(Input::get('over_mileage_clinic_notify') != null){
				$over_mileage_clinic_notify = Input::get('over_mileage_clinic_notify');
			}else{
				$default_max_mileage_per_clinic = DB::table('admin_settings')->select('default_max_mileage_per_clinic')->where('id',20)->get()->first();
				$over_mileage_clinic_notify = $default_max_mileage_per_clinic->default_max_mileage_per_clinic;
			}	
		}else{
			$over_mileage_clinic_notify = 'off';
		}
		
		$model->notify_only            		=	$notify_only;
		$model->unfilled_notify            	=	$unfilled_notify;
		$model->x_time_unfilled_notify	    =	$x_time_unfilled_notify;
		$model->clinic_filled_notify	    =	$clinic_filled_notify;
		$model->mileage_info_notify	    	=	$mileage_info_notify;
		$model->clinic_status_notify	    =	$clinic_status_notify;
		$model->user_added_notify	    	=	$user_added_notify;
		$model->admin_added_notify	    	=	$admin_added_notify;
		$model->certifications_notify	    =	$certifications_notify;
		$model->over_hour_month_notify	    =	$over_hour_month_notify;
		$model->over_hour_day_notify		=	$over_hour_day_notify;
		$model->over_hour_clinic_notify		=	$over_hour_clinic_notify;
		$model->over_mileage_month_notify	=	$over_mileage_month_notify;
		$model->over_mileage_day_notify	    =	$over_mileage_day_notify;
		$model->over_mileage_clinic_notify	=	$over_mileage_clinic_notify;
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
