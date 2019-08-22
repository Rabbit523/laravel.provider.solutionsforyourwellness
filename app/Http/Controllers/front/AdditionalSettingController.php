<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\ProvidersModel;
use App\Model\admin\CertificationModel;
use App\Model\admin\AdditionalSettingModel;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdditionalSettingController extends BaseController
{
    public function settings(){
      if(Input::isMethod('post')){
   			$rules = array(
          //  'field_value'    	       => 'required',
   			);
   		$validator = Validator::make(Input::all(),$rules);
   		 if ($validator->fails()) {
   			$messages = $validator->messages();
   			return Redirect::back()->withErrors($validator)->withInput();
   		  } else {
   							$UpdateSettings = AdditionalSettingModel::UpdateSetting();
   								if($UpdateSettings){
   									 Toast::success('Settings successfully updated');
   									return redirect()->route('settings');
   								}else{
   									 Toast::error('Technical error');
   									return redirect()->route('settings');
   								}
   					}
   	   }else{
         $timezones = DB::table('timezone')->orderBy('timezone_name','ASC')->get();
          $admin_settings = DB::table('admin_settings')->where('id',20)->first();
          return  View::make('admin.additional_settings.index',compact('admin_settings','timezones'));
   	   }
    }


}
