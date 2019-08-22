<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;

class TimeZoneModel extends Authenticatable
{
  use Notifiable;
  protected $table = 'timezone';

  /**
  * Function for get all announcement records.
  *
  * @param null.
  *
  * @return all announcements on success otherwise false.
  */
  public static function ChangeAnnouncementStatus($user_id,$announcement_id){
    $status = AnnouncementStatusModel::insert(array('provider_id'=>$user_id,'announcement_id'=>$announcement_id,'decline'=>1));
	if($status){
		return true;
	}else{
		return false;
	}
  }
}
