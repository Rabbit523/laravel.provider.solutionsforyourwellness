<?php

namespace App\Model;
use App\Traits\Encryptable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Support\Facades\Hash;

class Notifications extends Authenticatable
{
    use Notifiable;
	protected $table 		= 	'notifications';
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
    * Function for save notification into database.
    *
    * @param filename(provider image).
    *
    * @return response true on success otherwise false.
    */
    public static function SaveCertificateNotification($device_id='',$user_id,$type,$message){
      $model				      =	new Notifications;
      $model->user_id     =	$user_id;
      $model->device_id   =	$device_id;
      $model->message     =	$message;
      $model->type	      =	$type;
      $saved				      =	$model->save();
      if($saved){
        return true;
      }
      else{
        return false;
      }
    }
}
