<?php
namespace App\Model\admin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;
class AnnouncementModel extends Authenticatable
{
  use Notifiable;
protected $table = 'announcement';

  /**
  * Function for save new announcement into database.
  *
  * @param filename(provider image).
  *
  * @return response true on success otherwise false.
  */
  public static function SaveAnnouncement($filename=''){
	  if(empty(Input::get('visible_providers'))){
		  $visible_provider = null;
	  }else{
		   $visible_provider = implode(',', (array) Input::get('visible_providers'));
	  }
	  if(empty(Input::get('visible_cities'))){
		  $visible_cities = null;
	  }else{
		   $visible_cities = implode(',', (array) Input::get('visible_cities'));
	  }
	$user_id 						= Auth::user()->id;
    $model					        =	new AnnouncementModel;
	$model->admin_id           		=	$user_id;
    $model->title           		=	Input::get('title');
    $model->description	    		=	Input::get('description');
    $model->image			      	=	$filename;
    $model->visible_providers      	=	$visible_provider;
    $model->visible_cities      	=	$visible_cities;
    $model->notification_alert	   	=	Input::get('notification_alert');
    $model->email_alert	           	=	Input::get('email_alert');
    if(Input::get('stable_time')  	!= null){
      $model->stable_time	        =	Input::get('stable_time')*24;
    }
    else{
      $setting_data                =  DB::table('admin_settings')->select('default_time_stay_in_feeds')->where('id',20)->get();
      $model->stable_time          = $setting_data[0]->default_time_stay_in_feeds;
    }
    $model->save();
    return $model->id;
  }
  /**
  * Function for update single announcement.
  *
  * @param user id
  *
  * @return response true on success otherwise false.
  */
  public static function UpdateAnnouncement($filename='',$id){
	  if(empty(Input::get('visible_providers'))){
		  $visible_provider = null;
	  }else{
		   $visible_provider = implode(',', (array) Input::get('visible_providers'));
	  }
	  if(empty(Input::get('visible_cities'))){
		  $visible_cities = null;
	  }else{
		   $visible_cities = implode(',', (array) Input::get('visible_cities'));
	  }
	  $start_time   =  date('H:i:s');
	  $start_date   =  date('Y-m-d',strtotime(Input::get('date')));
	  $last_date   =  $start_date.' '.$start_time;
    $model					=	AnnouncementModel::find($id);
    $model->title           =	Input::get('title');
    $model->description	    =	Input::get('description');
	$model->created_at	    =	$last_date;
    $model->image		    =	$filename;
	$model->visible_providers      =	$visible_provider;
    $model->visible_cities         =	$visible_cities;
    $model->notification_alert	   =	Input::get('notification_alert');
    $model->email_alert	           =	Input::get('email_alert');
    if(Input::get('stable_time')  != null){
      $model->stable_time	       =	Input::get('stable_time')*24;
    }
    else{
      $setting_data                =  DB::table('admin_settings')->select('default_time_stay_in_feeds')->where('id',20)->get();
      $model->stable_time          = $setting_data[0]->default_time_stay_in_feeds;
    }
    $saved					=	$model->save();
    if(!$saved){
      return false;
    }
    return true;
  }
  /**
  * Function for get all announcement data.
  *
  * @param search,start,length,column_id,column_name(for ajax datatable pagination).
  *
  * @return response announcement data on success otherwise false.
  */
  public static function GetAnnouncement($search="",$start,$length,$column_id,$column_order){
    $column_name = array('title','title','image','description','status','status');
    if($search){
		$usersdata = AnnouncementModel::where(function ($query) use ($search) {
		  })->where(function ($query) use ($search) {
		  $query->where('announcement.title', 'LIKE', '%'.$search.'%')
			  ->orWhere('announcement.status', 'LIKE', '%'.$search.'%');
		})->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
    }else{
		if($column_order != 'desc'){
			$usersdata = AnnouncementModel::orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
		}else{
			$usersdata = AnnouncementModel::orderBy('id','desc')->limit($length)->offset($start)->get();
		}
    }
        if(empty($usersdata)){
          return false;
        }
        return $usersdata;
  }
  public static function UpdateAnnouncementSetting($id){
	$start_time   				   =  date('H:i:s');
	$start_date   				   =  date('Y-m-d',strtotime(Input::get('date')));
	$last_date   				   =  $start_date.' '.$start_time;
    $model					       =	AnnouncementModel::find($id);
    $model->visible_providers      =	implode(',', (array) Input::get('visible_providers'));
    $model->visible_cities         =	implode(',', (array) Input::get('visible_cities'));
    $model->notification_alert	   =	Input::get('notification_alert');
	$model->created_at	    	   =	$last_date;
    $model->email_alert	           =	Input::get('email_alert');
    if(Input::get('stable_time')  != null){
      $model->stable_time	       =	Input::get('stable_time')*24;
    }
    else{
      $setting_data                =  DB::table('admin_settings')->select('default_time_stay_in_feeds')->where('id',20)->get();
      $model->stable_time          = $setting_data[0]->default_time_stay_in_feeds;
    }
    $saved					 =	$model->save();
    if(!$saved){
      return false;
    }
    return true;
  }
  
  public static function getallproviders(){
    $providers = DB::table('users')->select('id')->where('role_id',0)->where('status',1)->get()->toArray();
	return $providers->id;
  }
}
