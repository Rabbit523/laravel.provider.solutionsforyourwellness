<?php

namespace App\Model;
use App\Traits\Encryptable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Support\Facades\Hash;

class User_model extends Authenticatable
{
    use Notifiable;
	protected $table 		= 	'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','title','first_name','last_name','phone','address','social_security_number','provider_type','hourly_rate','max_hours','email_notification','image','status','remember_token','forgot_password_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        // 'remember_token',
    ];
	/**
     * function for get user details by email id.
     *
     * @param user email
     *
     * @return userdata on success otherwise false.
     */
	public static function GetUserByEmail($email){
		$userdata = User_model::where('email',$email)->first();
		if(empty($userdata)){
			return false;
		}
		return $userdata->toArray();
	}
	/**
     * function for get user details by id.
     *
     * @param user email
     *
     * @return userdata on success otherwise false.
     */
	public static function GetUserById($id){
		$userdata = User_model::where('id',$id)->first();
		if(empty($userdata)){
			return false;
		}
		return $userdata->toArray();
	}
	 /**
     * function for change password details by id.
     *
     * @param user id
     *
     * @return true on success otherwise false.
     */
	 public static function changepassword($user_id,$new_password){
		 $model = User_model::find($user_id);
		 $model->password = $new_password;
		 $saved = $model->save();
		 if(!$saved){
			 return false;
		 }
		 return true;
	 }
	 /**
     * function for update user email by id.
     *
     * @param user id
     *
     * @return true on success otherwise false.
     */
	 public static function update_email($user_id,$email){
		 $model 		    = User_model::find($user_id);
		 $model->email 	= $email;
		 $saved 		    = $model->save();
		 if(!$saved){
			 return false;
		 }
		 return true;
	 }
	 /**
     * function for update phone number by id.
     *
     * @param user id
     *
     * @return true on success otherwise false.
     */
	 public static function update_phone($user_id,$phone){
		 $model 		    = User_model::find($user_id);
		 $model->phone 	= $phone;
		 $saved 		    = $model->save();
		 if(!$saved){
			 return false;
		 }
		 return true;
	 }
	 /**
     * function for update prep time by id.
     *
     * @param user id, prep time
     *
     * @return true on success otherwise false.
     */
	 public static function update_prep_time($user_id,$prep_time){
		 $model 						= 	User_model::find($user_id);
		 $model->prep_time  = 	$prep_time;
		 $saved 						= 	$model->save();
		 if(!$saved){
			 return false;
		 }
		 return true;
	 }
   /**
     * function for update email notification by id.
     *
     * @param user id, status
     *
     * @return true on success otherwise false.
     */
	 public static function update_email_notification($user_id,$status){
		 $model 						          = 	User_model::find($user_id);
		 $model->email_notification   = 	$status;
		 $saved 						          = 	$model->save();
		 if(!$saved){
			 return false;
		 }
		 return true;
	 }

   /**
     * function for update social security number by id.
     *
     * @param user id
     *
     * @return true on success otherwise false.
     */
	 public static function update_social_security($user_id,$social_security_number){
		 $model 						= 	User_model::find($user_id);
		 $model->social_security_number = 	$social_security_number;
		 $saved 						= 	$model->save();
		 if(!$saved){
			 return false;
		 }
		 return true;
	 }
	 /**
     * function for update user profile details by id.
     *
     * @param user id
     *
     * @return true on success otherwise false.
     */
	 public static function updateprofile($user_id,$input_data,$filename=''){
			$user							        =	User_model::find($user_id);
			$user->first_name				  =	$input_data['first_name'];
			$user->last_name				  =	$input_data['last_name'];
			$user->address					  =	$input_data['address'];
			$user->provider_type			=	$input_data['provider_type'];
			$user->user_description		=	$input_data['user_description'];
			$user->image					    =	$filename;
			$saved							      =	$user->save();
      $last_insert_id           = $user->id;
			 if(!$saved){
				 return false;
			 }
			return $last_insert_id;
	 }
   /**
     * function for update user profile photo by id.
     *
     * @param user id
     *
     * @return true on success otherwise false.
     */
	 public static function updateprofilephoto($user_id,$filename=''){
			$user							        =	User_model::find($user_id);
			$user->image					    =	$filename;
			$saved							      =	$user->save();
      $last_insert_id           = $user->id;
			 if(!$saved){
				 return false;
			 }
			return $last_insert_id;
	 }
	 /**
	  * Function for set forgot password token.
	  *
	  * @param user id and forgot password token.
	  *
	  * @return response true on success otherwise false.
	  */
	  public static function SetForgotPasswordToken($user_id,$token){
		$user							            =	User_model::find($user_id);
		$user->reset_password_token		=	$token;
		$saved                        =   $user->save();
		if(!$saved){
		  return false;
		}
		return true;
	  }
	  /**
	  * Function for get user data by reset password token.
	  *
	  * @param token.
	  *
	  * @return response userdata on success otherwise false.
	  */
	  public static function GetUserByResetToken($token){
		$user = User_model::where('reset_password_token',$token)->first();
		if(!$user){
		  return false;
		}
		return $user->toArray();
	  }
	  /**
	  * Function for reset user password with reset token update.
	  *
	  * @param user id.
	  *
	  * @return response true on success otherwise false.
	  */
	  public static function UpdatePassword($user_id,$new_password){
		$response = User_model::where('id',$user_id)
		->update(array('password'=>$new_password,'reset_password_token'=>''));
		if(!$response){
			return false;
		}
		return true;
	  }
    /**
      * function for update notification settings by id.
      *
      * @param user id, clock_in, clock_out, leave_location, disable_email_confirmation
      *
      * @return true on success otherwise false.
      */
 	 public static function update__notification_settings($user_id,$clock_in,$clock_out,$leave_location,$disable_email_confirmation){
 		 $model 						                  = 	User_model::find($user_id);
 		 $model->reminder_clock_in            = 	$clock_in;
 		 $model->reminder_clock_out           = 	$clock_out;
 		 $model->location_leave_amount        = 	$leave_location;
 		 $model->disable_email_confirmation   = 	$disable_email_confirmation;
 		 $saved 						                  = 	$model->save();
 		 if(!$saved){
 			 return false;
 		 }
 		 return true;
 	 }
   /**
     * function for update notification settings by id.
     *
     * @param user id, clock_in, clock_out, leave_location, disable_email_confirmation
     *
     * @return true on success otherwise false.
     */
   public static function update_push_notification($user_id,$push_notification){
     $model 						                  = 	User_model::find($user_id);
     $model->push_notification            = 	$push_notification;
     $saved 						                  = 	$model->save();
     if(!$saved){
       return false;
     }
     return true;
   }
   /**
     * function for update user's clock out settings
     *
     * @param user id,clockout_value
     *
     * @return true on success otherwise false.
     */
	 public static function update_clockout_setting($user_id,$clockout_value){
		 $model 		= User_model::find($user_id);
		 $model->reminder_clock_out 	= $clockout_value;
		 $saved 		= $model->save();
		 if(!$saved){
			 return false;
		 }
		 return true;
	 }
   /**
     * function for update user's clock in settings
     *
     * @param user id,clockin_value
     *
     * @return true on success otherwise false.
     */
  public static function update_clockin_setting($user_id,$clockin_value){
    $model 		= User_model::find($user_id);
    $model->reminder_clock_in 	= $clockin_value;
    $saved 		= $model->save();
    if(!$saved){
      return false;
    }
    return true;
  }
  /**
    * function for update leave location
    *
    * @param user id,location_value
    *
    * @return true on success otherwise false.
    */
  public static function update_leave_location($user_id,$location_value){
    $model 		= User_model::find($user_id);
    $model->location_leave_amount 	= $location_value;
    $saved 		= $model->save();
    if(!$saved){
      return false;
    }
    return true;
  }
  /**
    * function for update disable email confirmation for the clinic
    *
    * @param user id,clockout_value
    *
    * @return true on success otherwise false.
    */
  public static function update_disable_email_status($user_id,$disable_status){
    $model 		= User_model::find($user_id);
    $model->disable_email_confirmation 	= $disable_status;
    $saved 		= $model->save();
    if(!$saved){
      return false;
    }
    return true;
  }
  /**
    * function for update user's notification group by
    *
    * @param user id,notification_groupBy_value
    *
    * @return true on success otherwise false.
    */
 public static function update_notification_group($user_id,$notification_groupBy_value){
   $model 		= User_model::find($user_id);
   $model->notification_groupby 	= $notification_groupBy_value;
   $saved 		= $model->save();
   if(!$saved){
     return false;
   }
   return true;
 }
 /**
   * function for update user's timezone
   *
   * @param user id,timezone value
   *
   * @return true on success otherwise false.
   */
public static function update_user_timezone($user_id,$timezone_value){
  $model 		= User_model::find($user_id);
  $model->timezone 	= $timezone_value;
  $saved 		= $model->save();
  if(!$saved){
    return false;
  }
  return true;
}
/**
  * function for update user's timeformat
  *
  * @param user id,timeformat value
  *
  * @return true on success otherwise false.
  */
public static function update_user_timeformat($user_id,$timeformat_value){
 $model 		= User_model::find($user_id);
 $model->time_format 	= $timeformat_value;
 $saved 		= $model->save();
 if(!$saved){
   return false;
 }
 return true;
}
/**
  * function for update user's notification group by
  *
  * @param user id,notification_groupBy_value
  *
  * @return true on success otherwise false.
  */
  public static function update_system_calender($user_id,$system_calender_value){
   $model 		= User_model::find($user_id);
   $model->system_calender 	= $system_calender_value;
   $saved 		= $model->save();
   if(!$saved){
     return false;
   }
   return true;
  }
  /**
    * function for update system calender status
    *
    * @param user id,clockout_value
    *
    * @return true on success otherwise false.
    */
  public static function update_system_calender_status($user_id,$status){
    $model 		= User_model::find($user_id);
    $model->system_calender_status 	= $status;
    $saved 		= $model->save();
    if(!$saved){
      return false;
    }
    return true;
  }
}
