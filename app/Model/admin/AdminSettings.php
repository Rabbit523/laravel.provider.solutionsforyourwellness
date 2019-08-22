<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Support\Facades\Hash;

class AdminSettings extends Authenticatable
{
    use Notifiable;
	protected $table = 'admin_settings';

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
        'password', 'remember_token',
    ];
    public static function updatecopyright(){
      $model					        =	AdminSettings::find(5);
  		$model->field_name      =	'copyright_text';
  		$model->field_value	    =	Input::get('copyright_value');
      $model->save();
    }
    public static function GetSettingsByFieldname($field_name){
      $result = AdminSettings::where('field_name',$field_name)->first();
      if(!empty($result)){
        return $result;
      }else{
        return false;
      }
    }
}
