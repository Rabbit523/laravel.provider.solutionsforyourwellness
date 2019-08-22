<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Support\Facades\Hash;

class AdminItem extends Authenticatable
{
    use Notifiable;
	protected $table = 'deballage_products';
	public $timestamps = false;
	
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
	function AdminUpdateItem($data){
		$item					=	$this->find($data['item_id']);
		$item->user_id			=	isset($data['user_id'])?$data['user_id']:'0';
		$item->dealers_price	=	$data['dealers_price'];
		$item->retails_price	=	$data['retails_price'];
		$item->description		=	$data['description'];
		$item->category			=	$data['category'];
		$item->dealers_only		=	isset($data['dealers_only'])?$data['dealers_only']:'0';
		$item->save();
		if($data['user_id']){
			Media_model::where('item_id',$data['item_id'])->update(['user_id'=>$data['user_id']]);
		}
	}
}