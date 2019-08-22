<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;

class AnnouncementModel extends Authenticatable
{
  use Notifiable;
  protected $table = 'announcement';

  /**
  * Function for save new certificate into database.
  *
  * @param filename(provider image).
  *
  * @return response true on success otherwise false.
  */
  public static function GetLatest($user_id,$user_city){
    $announce = AnnouncementModel::whereRaw('FIND_IN_SET('.$user_id.',visible_providers)')->orWhereRaw('FIND_IN_SET("'.$user_city.'",visible_cities)')->orderBy('id', 'desc')->limit(3)
                ->get()->toArray();
    if(empty($announce)){
      return false;
    }
    return $announce;
  }
  /**
  * Function for get all announcement records.
  *
  * @param null.
  *
  * @return all announcements on success otherwise false.
  */
  public static function GetAll(){
    $announce = AnnouncementModel::orderBy('id','desc')->get();
    if(empty($announce)){
      return false;
    }
    return $announce->toArray();
  }
  /**
  * Function for get all announcement records.
  *
  * @param null.
  *
  * @return all announcements on success otherwise false.
  */
  public static function ChangeAnnouncementStatus($announcement_id){
    $status = AnnouncementModel::where('id',$announcement_id)->update(array('status'=>0));
	if($status){
		return true;
	}else{
		return false;
	}
  }
  /**
  * Function for get announcement from announcement id.
  *
  * @param announcement id.
  *
  * @return response announcement data on success otherwise false.
  */
  public static function GetAnnouncementById($id){
    $result = AnnouncementModel::where('id',$id)->get();
	return $result->toArray();
  }
}
