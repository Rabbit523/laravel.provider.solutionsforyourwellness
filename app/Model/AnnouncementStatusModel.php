<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;

class AnnouncementStatusModel extends Authenticatable
{
  use Notifiable;
  protected $table = 'announcement_status';

  /**
  * Function for get all announcement records.
  *
  * @param null.
  *
  * @return all announcements on success otherwise false.
  */
  public static function ChangeAnnouncementStatus($user_id,$announcement_id){
    $model = new AnnouncementStatusModel;
    $model->provider_id      = $user_id;
    $model->announcement_id  = $announcement_id;
    $model->decline          = 1;
    $status = $model->save();
  	if($status){
  		return true;
  	}else{
  		return false;
  	}
  }
}
