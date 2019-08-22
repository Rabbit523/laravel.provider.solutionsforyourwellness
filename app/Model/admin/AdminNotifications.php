<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;

class AdminNotifications extends Authenticatable
{
    use Notifiable;
	protected $table = 'admin_notifications';

	/**
    * Function for save notification of new provider
    *
    * @param provider_id
    *
    * @return response true on success otherwise false.
    */
    public static function SaveNotification($provider_id){
      $message                  = 'a new provider added';
      $type                     = 'provider';
  		$model					          =	new AdminNotifications;
  		$model->message           =	$message;
  		$model->required_id	      =	$provider_id;
  		$model->type			        =	$type;
  		$saved					        =	$model->save();
  		if(!$saved){
  			return false;
  		}
  		return true;
  	}

}
