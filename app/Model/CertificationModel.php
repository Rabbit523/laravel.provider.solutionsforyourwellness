<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;

class CertificationModel extends Authenticatable
{
  use Notifiable;
  protected $table = 'certifications';

  /**
  * Function for save new certificate into database.
  *
  * @param filename(provider image).
  *
  * @return response true on success otherwise false.
  */
  public static function SaveCertificate($filename='',$ext='',$input_data){
    $model				=	new CertificationModel;
    $model->user_id     =	$input_data['user_id'];
    $model->subject     =	$input_data['subject'];
    $model->description =	$input_data['description'];
    $model->file	      =	$filename;
    $model->type	      =	$ext;
    $model->date	      =	$input_data['date'];
    $model->save();
	  return $model->id;
  }
  /**
  * Function for get certificate from certificate id.
  *
  * @param certificate id.
  *
  * @return response certificate data on success otherwise false.
  */
  public static function GetCertificateById($id){
    $result = CertificationModel::where('certificate_id',$id)->get();
	return $result->toArray();
  }
  /**
  * Function for get certificate from user id.
  *
  * @param user id.
  *
  * @return response certificate data on success otherwise false.
  */
  public static function GetCertificatesByUserId($user_id){
    $result = CertificationModel::where('user_id',$user_id)->orderBy('certificate_id','DESC')->get();
	if(!empty($result)){
		return $result->toArray();
	}
	return false;
  }
  /**
  * Function for delete certificate 
  *
  * @param certificate id.
  *
  * @return  true on success otherwise false.
  */
  public static function DeleteCertificate($user_id,$certificate_id){
    $result = CertificationModel::where('certificate_id',$certificate_id)->where('user_id',$user_id)->delete();
	if($result){
		return true;
	}else{
		return false;
	}
	
  }

}
