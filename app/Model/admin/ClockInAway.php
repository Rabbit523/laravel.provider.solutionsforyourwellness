<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,DB;
use Illuminate\Support\Facades\Hash;

class ClockInAway extends Authenticatable
{
    use Notifiable;
	protected $table 		= 	'clock_in_away';
	/* public function getUpdatedAtColumn() {
		return null;
	} */
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','username', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];
	/**
  * Function for get all clock in away records data.
  *
  * @param search,start,length,column_id,column_name(for ajax datatable pagination).
  *
  * @return response clock in away records on success otherwise false.
  */
  public static function GetData($search="",$start,$length,$column_id,$column_order){
    $column_name = array('user_id','user_id','clinic_id','latitude','longitude');
    if($search){
        $usersdata = ClockInAway::where(function ($query) use ($search) {
          })->where(function ($query) use ($search) {
          $query->where('clock_in_away.user_id', 'LIKE', '%'.$search.'%')
              ->orWhere('clock_in_away.clinic_id', 'LIKE', '%'.$search.'%')
              ->orWhere('clock_in_away.latitude', 'LIKE', '%'.$search.'%')
              ->orWhere('clock_in_away.longitude', 'LIKE', '%'.$search.'%');
        })->join('clinics','clinics.id','=','clock_in_away.clinic_id')->join('users','users.id','=','clock_in_away.user_id')->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
    }else{
		if($column_order){
			$usersdata = ClockInAway::selectRaw('clock_in_away.*,users.first_name,users.last_name,clinics.name,clinics.date')->join('clinics','clinics.id','=','clock_in_away.clinic_id')->join('users','users.id','=','clock_in_away.user_id')->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
		}else{
			$usersdata = ClockInAway::selectRaw('clock_in_away.*,users.first_name,users.last_name,clinics.name,clinics.date')->join('clinics','clinics.Id','=','clock_in_away.clinic_id')->orderBy('id','desc')->limit($length)->offset($start)->get();
		}   
    }
        if(empty($usersdata)){
          return false;
        }
        return $usersdata;
  }
	
}
