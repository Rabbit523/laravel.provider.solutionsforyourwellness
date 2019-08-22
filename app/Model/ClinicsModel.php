<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;

class ClinicsModel extends Authenticatable
{
  use Notifiable;
  protected $table = 'clinics';

  /**
  * Function for get all announcement records.
  *
  * @param null.
  *
  * @return all announcements on success otherwise false.
  */
  public static function GetAll(){
    $clinics = ClinicsModel::orderBy('id','desc')->get();
    if(empty($clinics)){
      return false;
    }
    return $clinics->toArray();
  }
  /**
  * Function for get clinics by user id.
  *
  * @param user id.
  *
  * @return clinics of given user.
  */
  public static function GetClinicByUserId($user_id){
	$colname = 'provider_id';
    $clinics = ClinicsModel
						::whereRaw('FIND_IN_SET("'.$user_id.'",provider_id)', [$colname])
						->orderBy('id','desc')
						->get()->toArray();
	if(!empty($clinics)){
		return $clinics;
	}else{
		return false;
	}
  }
  
}
