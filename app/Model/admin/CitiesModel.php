<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;

class CitiesModel extends Authenticatable
{
    use Notifiable;
	protected $table = 'cities';
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
    * Function for save new city into database.
    *
    * @param null.
    *
    * @return response true on success otherwise false.
    */
  	public static function SaveCity(){
  		$model				=	new CitiesModel;
  		$model->city_name  	=	Input::get('city_name');
  		$model->description	=	Input::get('description');
  		$saved				=	$model->save();
  		if(!$saved){
  			return false;
  		}
  		return true;
  	}
	/**
    * Function for update city.
    *
    * @param city id.
    *
    * @return response true on success otherwise false.
    */
  	public static function UpdateCity($id){
  		$model				=	CitiesModel::find($id);
  		$model->city_name  	=	Input::get('city_name');
  		$model->description	=	Input::get('description');
  		$saved				=	$model->save();
  		if(!$saved){
  			return false;
  		}
  		return true;
  	}
    
    /**
    * Function for count all city records.
    *
    * @param null.
    *
    * @return response numbers of provider record.
    */
    public static function CountCities(){
      $count = CitiesModel::count();
      return $count;
    }
    /**
    * Function for get all providers data.
    *
    * @param search,start,length,column_id,column_name(for ajax datatable pagination).
    *
    * @return response usersdata on success otherwise false.
    */
    public static function GetCities($search="",$start,$length,$column_id,$column_order){
      $column_name = array('id','city_name','description','created_at');
      if($search){
          $citiesdata = CitiesModel::where(function ($query) use ($search) {
            })->where(function ($query) use ($search) {
            $query->where('cities.city_name', 'LIKE', '%'.$search.'%')
                ->orWhere('cities.description', 'LIKE', '%'.$search.'%')
                ->orWhere('cities.created_at', 'LIKE', '%'.$search.'%');
          })->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
      }else{
			if($column_order != 'desc'){
				$citiesdata = CitiesModel::orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
			  }else{
				$citiesdata = CitiesModel::orderBy('id','desc')->limit($length)->offset($start)->get();
			  }
      }
          if(empty($citiesdata)){
            return false;
          }
          return $citiesdata->toArray();
    }
}
