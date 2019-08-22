<?php

namespace App\Model\admin;
use Illuminate\Support\Facades\DB;
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
  public static function SaveCertificate($filename='',$ext=''){
    $model					        =	new CertificationModel;
    $model->user_id         =	Input::get('user_id');
    $model->subject         =	Input::get('subject');
    $model->description     =	Input::get('description');
    $model->file	          =	$filename;
    $model->type	          =	$ext;
    $model->save();
    return $model->id;
  }
  /**
  * Function for update single certificate.
  *
  * @param user id
  *
  * @return response true on success otherwise false.
  */
  public static function UpdateCertificate($filename,$fileExtension,$id){
    $model					        =		new CertificationModel;
    $subject     =	Input::get('subject');
    $description	      =	Input::get('description');
    $file		        =	$filename;
    $extension		      =	$fileExtension;
    $saved = $model::where('certificate_id', $id)
          ->update(['subject' => $subject,'description' => $description,'file' => $file,'type' => $extension]);
    if(!$saved){
      return false;
    }
    return true;
  }
  /**
  * Function for get all certificates data.
  *
  * @param search,start,length,column_id,column_name(for ajax datatable pagination).
  *
  * @return response resultdata on success otherwise false.
  */
  public static function GetCertificates($search="",$start,$length,$column_id,$column_order){
    $column_name = array('certificate_id','certificate_id','subject','file','certifications.updated_at');
    if($search){
        $resultdata = DB::table('certifications')
                            ->join('users', function ($join) {
                                $join->on('users.id', '=', 'certifications.user_id');
                            })
                            ->where('certifications.certificate_id', 'LIKE', '%'.$search.'%')
                            ->orWhere('certifications.subject', 'LIKE', '%'.$search.'%')
                            ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                            ->get();

    }else{
		if($column_order){
          $resultdata = DB::table('certifications')
                                ->join('users', function ($join) {
                                    $join->on('users.id', '=', 'certifications.user_id');
                                })
                                ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                                ->get();
		}else{
			$resultdata = DB::table('certifications')
                                ->join('users', function ($join) {
                                    $join->on('users.id', '=', 'certifications.user_id');
                                })
                                ->orderBy('certifications.user_id','desc')->limit($length)->offset($start)
                                ->get();
		}
    }
        if(empty($resultdata)){
          return false;
        }
        return $resultdata;
  }
  public static function Get_Filtered_Certificates($search="",$start,$length,$column_id,$column_order){
    $column_name = array('certifications.user_id','users.first_name','certifications.subject','certifications.file','certifications.description','certifications.updated_at');
    if($search){
      $search_ids = explode(",", $search);
      if(!in_array('all',$search_ids)){
        $resultdata = DB::table('users')
                            ->rightJoin('certifications', function ($join) {
                                $join->on('users.id', '=', 'certifications.user_id');
                            })
                            ->whereIn('users.id',$search_ids)
                            ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                            ->get();
      }
      else{
        $resultdata = DB::table('users')
                                  ->rightJoin('certifications', function ($join) {
                                      $join->on('users.id', '=', 'certifications.user_id');
                                  })
                                  ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                                  ->get();
      }
    }else{
    if($column_order){
      //print_r($column_name);die;
          $resultdata = DB::table('users')
                                ->rightJoin('certifications', function ($join) {
                                    $join->on('users.id', '=', 'certifications.user_id');
                                })
                                ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                                ->get();
    }else{
      $resultdata = DB::table('users')
                                ->rightJoin('certifications', function ($join) {
                                    $join->on('users.id', '=', 'certifications.user_id');
                                })
                                ->orderBy('certifications.user_id','desc')->limit($length)->offset($start)
                                ->get();
    }
    }
        if(empty($resultdata)){
          return false;
        }
        return $resultdata;
  }
}
