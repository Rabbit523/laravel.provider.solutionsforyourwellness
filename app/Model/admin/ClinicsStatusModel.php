<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;

class ClinicsStatusModel extends Authenticatable
{
    use Notifiable;
	protected $table = 'clinic_status';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone', 'address', 'time',
    ];
    /**
    * Function for save new providers into database.
    *
    * @param filename(provider image).
    *
    * @return response true on success otherwise false.
    */
	public static function CheckClinicStatus($clinic_id){
		$status = ClinicsStatusModel::where('clinic_id',$clinic_id)->whereNotNull('clock_in')->whereNotNull('clock_out')->get();
		if(!empty($status->toArray())){
			$data = 0;
		}else{
			$data = 1;
		}
		return $data;
		
	}

}
