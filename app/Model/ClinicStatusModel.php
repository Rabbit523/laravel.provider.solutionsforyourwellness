<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use App\Model\ClinicsModel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;

class ClinicStatusModel extends Authenticatable
{
  use Notifiable;
  protected $table = 'clinic_status';

  /**
  * Function for update clock out time for clinic.
  *
  * @param clinic id.
  *
  * @return true on success otherwise false.
  */
  public static function AcceptRejectClinic($user_id,$clinic_id,$status,$provider_type){
		$model 						= 	new ClinicStatusModel;
		$model->provider_id	    	=	$user_id;
		$model->clinic_id			=	$clinic_id;
		$model->status				=	$status;
		$model->provider_type		=	$provider_type;
		$model->create_timestamp	=	date('Y-m-d h:i:s');
		$saved						=	$model->save();
		if($saved){
			return true;
		}else{
			return false;
		}
  }
  /**
  * Function for update clock in time for clinic.
  *
  * @param clinic id.
  *
  * @return true on success otherwise false.
  */
  public static function UpdateClockIn($clinic_id,$user_id){
    $status = ClinicStatusModel::where('clinic_id',$clinic_id)->where('provider_id',$user_id)->update(['clock_in'=>date('Y-m-d H:i:s')]);
	if($status){
		return true;
	}else{
		return false;
	}
  }
  /**
  * Function for update clock out time for clinic.
  *
  * @param clinic id.
  *
  * @return true on success otherwise false.
  */
  public static function UpdateClockOut($clinic_id,$user_id){
    $clockInTime = ClinicStatusModel::select('clock_in')->where('clinic_id',$clinic_id)->where('provider_id',$user_id)->get()->first();
    $clock_in   = $clockInTime['clock_in'];
    $clock_out  = date('Y-m-d H:i:s');
    $clinic_spend_time = round((strtotime($clock_out)-strtotime($clock_in))/60);
    $status = ClinicStatusModel::where('clinic_id',$clinic_id)->where('provider_id',$user_id)->update(array('clock_out'=>$clock_out,'clinic_spend_time'=>$clinic_spend_time));
	if($status){
		return true;
	}else{
		return false;
	}
  }
  /**
  * Function for check clinic available or not for prefred feature.
  *
  * @param clinic id.
  *
  * @return true on available otherwise false.
  */
  public static function CheckClinicAvailablity($clinic_id,$user_id){
	  $count 	= 	ClinicStatusModel::
					where('clinic_id',$clinic_id)
					->where('provider_id',$user_id)
					->where('status',1)->count();
		if($count==0){
			return 1;
		}
		return 0;
  }
  /**
  * Function for check clinic rejected or not
  *
  * @param clinic id.
  *
  * @return true on available otherwise false.
  */
  public static function CheckClinicRejected($clinic_id){
	  $count 	= 	ClinicStatusModel::
					where('clinic_id',$clinic_id)
					->where('status',0)->count();
		if($count==0){
			return 1;
		}
		return 0;
  }
  /**
  * Function for check clinic rejected or not
  *
  * @param clinic id.
  *
  * @return true on available otherwise false.
  */
  public static function Upcomig_clinic_limit($clinic_id,$date,$days){
	  $count 	= 	ClinicsModel::
					where('id',$clinic_id)
          ->where('date',$date)
					->get()->toArray();
		if($count != null){
			$clinic_date =  $count[0]['date'];
      $date = strtotime("+".$days." days", strtotime($clinic_date));
      return  date('Y-m-d',$date);
      //return $date;
		}
		return false;
  }
  /**
  * Function for check clinic clocked in or clocked out.
  *
  * @param clinic id ,user Id.
  *
  * @return true on available otherwise false.
  */
  public static function GetClinicStatusById($clinic_id,$user_id){
	  $clinic 	= 	ClinicStatusModel::
					where('clinic_id',$clinic_id)
					->where('provider_id',$user_id)
          ->where('status',1)
					->first();
	  if(!empty($clinic)){
		  return $clinic;
	  }else{
		  return false;
	  }
  }
  /**
  * Function for update mileage data of a clinic.
  *
  * @param provider_id,clinic id,mileage,drive_time.
  *
  * @return true on success otherwise false.
  */
  public static function UpdateMileage($provider_id,$clinic_id,$mileage,$drivetime){
    $status = ClinicStatusModel::where('clinic_id',$clinic_id)->where('provider_id',$provider_id)->update(['mileage'=>$mileage,'drive_time'=>$drivetime]);
	  if($status){
		return true;
	  }else{
		return false;
	  }
  }
  /**
  * Function for sync clinic with offline api
  *
  * @param clinic id.
  *
  * @return true on success otherwise false.
  */
  public static function UpdateSyncClockout($user_id,$clinic_id,$clock_out_time,$mileage,$drive_time,$clinic_spend_time){
    $status = ClinicStatusModel::where('clinic_id',$clinic_id)->where('provider_id',$user_id)->update([
																'clock_out'=>$clock_out_time,
																'mileage'=>$mileage,
																'drive_time'=>$drive_time,
																'clinic_spend_time'=>$clinic_spend_time,
																]);
	if($status){
		
		return true;
	}else{
		return false;
	}
  }
  /**
  * Function for update clock in time for clinic sync offline.
  *
  * @param clinic id.
  *
  * @return true on success otherwise false.
  */
  public static function UpdateSyncClockIn($user_id,$clinic_id,$clock_in_time){
    $status = ClinicStatusModel::where('clinic_id',$clinic_id)->where('provider_id',$user_id)->update(['clock_in'=>$clock_in_time]);
	if($status){
		return true;
	}else{
		return false;
	}
  }
}
