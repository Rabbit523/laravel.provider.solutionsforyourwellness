<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use App\Model\ClinicsModel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;

class UpcomingSwipeModel extends Authenticatable
{
  use Notifiable;
  protected $table = 'unfilled_rejected_clinics';

  /**
  * Function for update clock out time for clinic.
  *
  * @param clinic id.
  *
  * @return true on success otherwise false.
  */
  public static function SwipeClinic($user_id,$clinic_id){
		$model 						     = 	new UpcomingSwipeModel;
		$model->provider_id	   =	$user_id;
		$model->clinic_id			 =	$clinic_id;
		$model->decline				 =	1;
		$saved						     =	$model->save();
		if($saved){
			return true;
		}else{
			return false;
		}
  }
}
