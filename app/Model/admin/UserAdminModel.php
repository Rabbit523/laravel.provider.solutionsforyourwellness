<?php

namespace App\Model\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Support\Facades\Hash;
class UserAdminModel extends Authenticatable
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
  * Function for save new admin into database.
  *
  * @param filename(provider image).
  *
  * @return response true on success otherwise false.
  */
  public static function SaveAdmin(){
	$model					 		=	new UserAdminModel;
	$model->admin_id         		=	Auth::user()->id;
    $model->role_id          		=	1;
    $model->first_name      		=	Input::get('first_name');
    $model->last_name	      		=	Input::get('last_name');
    $model->email			      	=	Input::get('email');
    $model->password		    	=	Hash::make(Input::get('password'));
    $model->phone			      	=	Input::get('phone');
    $model->social_security_number	=	Input::get('security_pin');
	$saved					        =	$model->save();
	$last_id            			= 	$model->id;
	if($saved){
	return $last_id;
	}else{
		return false;
	}
    
  }
  /**
  * Function for update single admin.
  *
  * @param user id
  *
  * @return response true on success otherwise false.
  */
  public static function UpdateAdmin($id){
    $model					   =	UserAdminModel::find($id);
    $model->first_name         =	Input::get('first_name');
    $model->last_name	       =	Input::get('last_name');
    $model->email			   =	Input::get('email');
    if(Input::get('password') !=''){
      $model->password		   =	Hash::make(Input::get('password'));
    }
    $model->phone			   =	Input::get('phone');
    if(Input::get('security_pin') !=''){
      $model->social_security_number	=	Input::get('security_pin');
    }
	try{
		$saved					        =	$model->save();
		return true;
    }
    catch(\Exception $e){
       // do task when error
       echo $e->getMessage();
    }
  }
  /**
  * Function for get all admin data.
  *
  * @param search,start,length,column_id,column_name(for ajax datatable pagination).
  *
  * @return response admindata on success otherwise false.
  */
  public static function GetAdmin($search="",$start,$length,$column_id,$column_order){
    $column_name = array('first_name','first_name','email','phone','status','status');
    if($search){
        $usersdata = UserAdminModel::where('role_id',1)->where(function ($query) use ($search) {
          })->where(function ($query) use ($search) {
          $query->where('users.first_name', 'LIKE', '%'.$search.'%')
              ->orWhere('users.email', 'LIKE', '%'.$search.'%')
              ->orWhere('users.phone', 'LIKE', '%'.$search.'%')
              ->orWhere('users.status', 'LIKE', '%'.$search.'%');
        })->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
    }else{
		if($column_order){
			$usersdata = UserAdminModel::where('role_id',1)->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
		}else{
			$usersdata = UserAdminModel::where('role_id',1)->orderBy('id','desc')->limit($length)->offset($start)->get();
		}   
    }
        if(empty($usersdata)){
          return false;
        }
        return $usersdata;
  }
}
