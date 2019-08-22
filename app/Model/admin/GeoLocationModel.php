<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;
use DateTime,DateTimeZone;

class GeoLocationModel extends Authenticatable
{
    use Notifiable;
	protected $table = 'geolocation';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone', 'address', 'time',
    ];
	
    /**
    * Function for get all providers data.
    *
    * @param search,start,length,column_id,column_name(for ajax datatable pagination).
    *
    * @return response usersdata on success otherwise false.
    */
    public static function GetProviderLocationByClinic($provider_id,$clinic_id,$clockin,$clockout, $clinic_lat, $clinic_long){
		
		$geodata = GeoLocationModel::selectRaw('( 6371 * acos( cos( radians('.$clinic_lat.') ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians('.$clinic_long.') ) + sin( radians('.$clinic_lat.') ) * sin( radians( `latitude` ) ) ) ) as distance, created_at, latitude, longitude')
		->where('user_id',$provider_id)
		->where('clinic_id',$clinic_id)
		->where('status',1)
		->whereBetween('created_at',[$clockin,$clockout])
		->get()->toArray();
		$response_data 	=	array();
		if(!empty($geodata)){
			foreach($geodata as $data){
				$response_data[date('d-m-Y H:i', strtotime($data['created_at']))] 	=	$data['distance'];
			}
		}
		return $response_data;
	}
	
	/**
    * Function for get red time data.
    *
    * @param search,start,length,column_id,column_name(for ajax datatable pagination).
    *
    * @return response usersdata on success otherwise false.
    */
    public static function GetProviderRedTime($provider_id,$clinic_id,$clockin,$clockout, $clinic_lat, $clinic_long){
		
		$red_data = DB::select("SELECT *,( 6371 * acos( cos( radians('".$clinic_lat."') ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians('".$clinic_long."') ) + sin( radians('".$clinic_lat."') ) * sin( radians( `latitude` ) ) ) )/1.60934 AS distance FROM `geolocation` WHERE created_at BETWEEN '".$clockin."' AND '".$clockout."' AND clinic_id='".$clinic_id."' AND user_id='".$provider_id."' AND ( 6371 * acos( cos( radians('".$clinic_lat."') ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians('".$clinic_long."') ) + sin( radians('".$clinic_lat."') ) * sin( radians( `latitude` ) ) ) )/1.60934 > 1");
		if(!empty($red_data)){
			return count($red_data);
		}
	}
	/**
    * Function for get red+yellow time data.
    *
    * @param search,start,length,column_id,column_name(for ajax datatable pagination).
    *
    * @return response usersdata on success otherwise false.
    */
    public static function GetProviderRedYellowTime($provider_id,$clinic_id,$clockin,$clockout, $clinic_lat, $clinic_long){
		
		$red_yellow_data = DB::select("SELECT *,( 6371 * acos( cos( radians('".$clinic_lat."') ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians('".$clinic_long."') ) + sin( radians('".$clinic_lat."') ) * sin( radians( `latitude` ) ) ) )/1.60934 AS distance FROM `geolocation` WHERE created_at BETWEEN '".$clockin."' AND '".$clockout."' AND clinic_id='".$clinic_id."' AND user_id='".$provider_id."' AND ( 6371 * acos( cos( radians('".$clinic_lat."') ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians('".$clinic_long."') ) + sin( radians('".$clinic_lat."') ) * sin( radians( `latitude` ) ) ) )/1.60934 > 0.5");
		;
		if(!empty($red_yellow_data)){
			return count($red_yellow_data);
		}
	}
	/**
    * Function for get red+yellow time data.
    *
    * @param search,start,length,column_id,column_name(for ajax datatable pagination).
    *
    * @return response usersdata on success otherwise false.
    */
    public static function GetProviderGreenTime($provider_id,$clinic_id,$clockin,$clockout, $clinic_lat, $clinic_long){
		
		$green_time = DB::select("SELECT *,( 6371 * acos( cos( radians('".$clinic_lat."') ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians('".$clinic_long."') ) + sin( radians('".$clinic_lat."') ) * sin( radians( `latitude` ) ) ) )/1.60934 AS distance FROM `geolocation` WHERE created_at BETWEEN '".$clockin."' AND '".$clockout."' AND clinic_id='".$clinic_id."' AND user_id='".$provider_id."' AND ( 6371 * acos( cos( radians('".$clinic_lat."') ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians('".$clinic_long."') ) + sin( radians('".$clinic_lat."') ) * sin( radians( `latitude` ) ) ) )/1.60934 > 0.03");
		if(!empty($green_time)){
			return count($green_time);
		}
	}
}
