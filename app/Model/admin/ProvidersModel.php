<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;

class ProvidersModel extends Authenticatable
{
    use Notifiable;
	protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
    * Function for save new providers into database.
    *
    * @param filename(provider image).
    *
    * @return response true on success otherwise false.
    */
  	public static function SaveProvider($cityname=null,$filename=null){
		$global_data = DB::table('users')->where('id',Auth::user()->id)->orderBy('id','ASC')->first();
		if(!empty(Input::get('timezone'))){
			$timezone = Input::get('timezone');
		}else{
			$timezone = $global_data->timezone;
		}
  		$model							=	new ProvidersModel;
  		$model->admin_id        		=	Auth::user()->id;
  		$model->first_name      		=	Input::get('first_name');
  		$model->last_name	    		=	Input::get('last_name');
  		$model->email					=	Input::get('email');
  		$model->password				=	Hash::make(Input::get('password'));
  		$model->phone					=	Input::get('phone');
		$model->address	        		=	Input::get('location');
		$model->image	        		=	$filename;
		$model->latitude	    		=	Input::get('lat');
		$model->longitude	    		=	Input::get('lng');
		$model->city_name       		= 	$cityname;
		$model->social_security_number	=	Input::get('social_security_number');
		$model->provider_type			=	Input::get('provider_type');
		$model->timezone				=	$timezone;
		$model->hourly_rate	    		=	Input::get('hourly_rate');
		  if(Input::get('max_hours') != null){
			 $max_hours    		=	Input::get('max_hours');
		  }else{
			  $default_max_data = 	DB::table('admin_settings')->where('id',20)->get()->toArray();
			  $max_hours   		=  	$default_max_data[0]->default_max_scheduled_hours;
		  }
			$model->max_hours	=	$max_hours;
			$saved				=	$model->save();
			$last_id            = 	$model->id;
			if(!$last_id){
				return false;
			}
			return $last_id;
  	}
    /**
    * Function for update single provider.
    *
    * @param user id
    *
    * @return response true on success otherwise false.
    */
    public static function UpdateProvider($id,$cityname=null,$filename=null){
		$global_data = DB::table('users')->where('id',Auth::user()->id)->orderBy('id','ASC')->first();
		if(!empty(Input::get('timezone'))){
			$timezone = Input::get('timezone');
		}else{
			$timezone = $global_data->timezone;
		}
  		$model					=	ProvidersModel::find($id);
		$model->first_name      =	Input::get('first_name');
  		$model->last_name	    =	Input::get('last_name');
  		$model->email			=	Input::get('email');
      if(Input::get('password') !=''){
        $model->password		=	Hash::make(Input::get('password'));
      }
  		$model->phone			=	Input::get('phone');
		$model->image	        =	$filename;
		$model->city_name	    =	$cityname;
  		$model->address	        =	Input::get('location');
      $model->latitude	        =	Input::get('lat');
      $model->longitude	        =	Input::get('lng');
      if(Input::get('social_security_number') !=''){
        $model->social_security_number	=	Input::get('social_security_number');
      }
	  $model->timezone		    =	$timezone;
      $model->provider_type	  =	Input::get('provider_type');
      $model->hourly_rate	  =	Input::get('hourly_rate');
      if(Input::get('max_hours') == null){
        $default_max_data = DB::table('admin_settings')->where('id',20)->get()->toArray();
        $max_hours   =  $default_max_data[0]->default_hours;
      }else{
        $max_hours = Input::get('max_hours');
      }
      $model->max_hours	      =	$max_hours;
  		$saved					        =	$model->save();
  		if(!$saved){
  			return false;
  		}
  		return true;
  	}
    /**
    * Function for count all provider records.
    *
    * @param null.
    *
    * @return response numbers of provider record.
    */
    public static function CountProviders(){
      $count = ProvidersModel::where('role_id','=',0)->count();
      return $count;
    }
    /**
    * Function for get all providers data.
    *
    * @param search,start,length,column_id,column_name(for ajax datatable pagination).
    *
    * @return response usersdata on success otherwise false.
    */
    public static function GetProviders($search="",$start,$length,$column_id,$column_order){
      $column_name = array('id','first_name','email','phone','provider_type','address','status','status');
      if($search){
			 $usersdata = ProvidersModel::where('role_id',0)->where(function ($query) use ($search) {
            })->where(function ($query) use ($search) {
            $query->where('users.first_name', 'LIKE', '%'.$search.'%')
                ->orWhere('users.last_name', 'LIKE', '%'.$search.'%')
                ->orWhere('users.email', 'LIKE', '%'.$search.'%')
                ->orWhere('users.phone', 'LIKE', '%'.$search.'%')
                ->orWhere('users.provider_type', 'LIKE', '%'.$search.'%')
                ->orWhere('users.status', 'LIKE', '%'.$search.'%');
          })->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get(); 
      }else{
			if($column_order){
				$usersdata = ProvidersModel::where('role_id',0)->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
			  }else{
				$usersdata = ProvidersModel::where('role_id',0)->orderBy('id','desc')->limit($length)->offset($start)->get();
			  }
      }
          if(empty($usersdata)){
            return false;
          }
          return $usersdata;
    }
	/**
	* Function for get user data from user Id
	*
	* @param user Id
	*
	* @return user data on success otherwise false.
	*/
	public static function GetUserById($id){
		$result = ProvidersModel::where('id',$id)->first();
		if(!empty($result)){
			return $result;
		}
		return false;
	}
	
	public static function getallproviders(){
    $providers = ProvidersModel::select('id')->where('role_id',0)->where('status',1)->get()->toArray();
	return $providers;
  }
}
