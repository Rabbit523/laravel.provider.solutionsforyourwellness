<?php

namespace App\Model\admin;
use Input,Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class AdminUser extends Authenticatable
{
    use Notifiable;
	protected $table 		= 	'users';
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
    * Function for save new user into database.
    *
    * @param filename(user image),verify_string.
    *
    * @return response true on success otherwise false.
    */
	public static function SaveUser($filename=null,$verify_string){
		$model					        =	new AdminUser;
		$model->first_name      =	Input::get('first_name');
		$model->last_name	      =	Input::get('last_name');
		$model->username		    =	Input::get('username');
		$model->email			      =	Input::get('email');
		$model->password		    =	Hash::make(Input::get('password'));
		$model->phone			      =	Input::get('phone');
		$model->verify_string	  = $verify_string;
		$model->image			      = $filename;
		$saved					        =	$model->save();
		if(!$saved){
			return false;
		}
		return true;
	}
  /**
  * Function for update single user.
  *
  * @param user id,filename(user image).
  *
  * @return response true on success otherwise false.
  */
  public static function UpdateUser($id,$filename){
		$model					        =	AdminUser::find($id);
		$model->first_name      =	Input::get('first_name');
		$model->last_name	      =	Input::get('last_name');
		$model->username		    =	Input::get('username');
		$model->email			      =	Input::get('email');
		$model->phone			      =	Input::get('phone');
		$model->image			      = $filename;
		$saved					        =	$model->save();
		if(!$saved){
			return false;
		}
		return true;
	}
  /**
  * Function for delete users
  *
  * @param single user id or multiple ids by checkbox.
  *
  * @return response true if delete success otherwise false.
  */
  public static function DeleteUser($ids){
    $delete = AdminUser::whereIn('id',$ids)->delete();
    if(!$delete){
      return false;
    }
    return true;
  }
  /**
  * Function for get all users data.
  *
  * @param search,start,length,column_id,column_name(for ajax datatable pagination).
  *
  * @return response usersdata on success otherwise false.
  */
  public static function GetUsers($search="",$start,$length,$column_id,$column_order){
    $column_name = array('first_name','last_name','username','email','phone','status');
    if($search){
        $usersdata = AdminUser::where('user_role_id',0)->where(function ($query) use ($search) {
          })->where(function ($query) use ($search) {
          $query->where('users.first_name', 'LIKE', '%'.$search.'%')
              ->orWhere('users.last_name', 'LIKE', '%'.$search.'%')
              ->orWhere('users.username', 'LIKE', '%'.$search.'%')
              ->orWhere('users.email', 'LIKE', '%'.$search.'%')
              ->orWhere('users.phone', 'LIKE', '%'.$search.'%')
              ->orWhere('users.status', 'LIKE', '%'.$search.'%');
        })->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get()->toArray();
    }else{
          $usersdata = AdminUser::where('user_role_id',0)->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get()->toArray();
    }
        if(empty($usersdata)){
          return false;
        }
        return $usersdata;
  }
  /**
  * Function for count all users records.
  *
  * @param null.
  *
  * @return response numbers of users record.
  */
  public static function CountUsers(){
    $count = AdminUser::where('user_role_id','=',0)->count();
    return $count;
  }
  /**
  * Function for get single user data.
  *
  * @param user id.
  *
  * @return response userdata on success otherwise false.
  */
  public static function GetById($id){
     $user = AdminUser::where('id',$id)->first();
     if(empty($user)){
       return false;
     }
     return $user;
  }
}
