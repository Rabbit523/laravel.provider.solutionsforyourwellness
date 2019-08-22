<?php

namespace App\Model;
use App\Traits\Encryptable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Support\Facades\Hash;

class GeoLocationModel extends Authenticatable
{
    use Notifiable;
	protected $table 		= 	'geolocation';
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
    * Function for save user details with lat long.
    *
    * @param null.
    *
    * @return  success otherwise false.
    */
    public static function SaveLatLong($user_id,$clinic_id,$longitude,$latitude){
      $model = new GeoLocationModel;
      $model->user_id     = $user_id;
      $model->clinic_id   = $clinic_id;
      $model->latitude    = $latitude;
      $model->longitude   = $longitude;
      $save_data          =  $model->save();
      if(empty($save_data)){
        return false;
      }
      return true;
    }
}
