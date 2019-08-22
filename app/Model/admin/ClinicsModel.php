<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;
use DateTime,DateTimeZone;

class ClinicsModel extends Authenticatable
{
    use Notifiable;
	protected $table = 'clinics';
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
  	public static function SaveClinic(){
	  $global_data = DB::table('users')->where('id',Auth::user()->id)->orderBy('id','ASC')->first();
		if(!empty(Input::get('timezone'))){
			$timezone = Input::get('timezone');
		}else{
			$timezone = $global_data->timezone;
		}
	  $clinic_time_zone     	= $timezone;

      $clinic_time    		= 	Input::get('time');
      $clinicDate 	  		= 	date('Y-m-d',strtotime(Input::get('date')));
      $clinic_time_data 	= 	new DateTime($clinicDate.' '.$clinic_time, new DateTimeZone($clinic_time_zone));
      $clinic_time_data->setTimezone(new DateTimeZone('GMT'));
      $clinic_date_time 		= $clinic_time_data->format('Y-m-d H:i:s');
      $clinicDate 			= 	$clinic_time_data->format('Y-m-d');
      $clinicTime 			= 	$clinic_time_data->format('H:i');

      $time = strtotime($clinic_date_time);
      if(Input::get('preptime') == null){
        $default_prep_time 	= 	DB::table('admin_settings')->select('default_prep_time')->where('id',20)->first();
        $default_preptime   = 	'-'.$default_prep_time->default_prep_time.' minutes';
        $prep_time  		= 	date("H:i", strtotime($default_preptime, $time));
      }
      else{
        $default_preptime   = 	'-'.Input::get('preptime').' minutes';
        $prep_time  		=	date("H:i", strtotime($default_preptime, $time));
      }
  		$model					         =	new ClinicsModel;
      if(Input::get('manualprovider') && Input::get('manualprovider') != null){
        $model->provider_id      		 =	implode(',', Input::get('manualprovider'));
		$model->manual_provider      	 =	implode(',', Input::get('manualprovider'));
      }
      elseif(Input::get('providers') && Input::get('providers') != null){
        $model->provider_id      		=	implode(',', Input::get('providers'));
		$model->manual_provider      	=	null;
      }
      else{
        $model->provider_id      =	null;
      }

    $estimated_duration_time       = 	'+'.Input::get('estimated_duration').' minutes';
    $clinic_end_time  			   = 	date("H:i", strtotime($estimated_duration_time, $time));
	$model->admin_id	           =	Auth::user()->id;
    $model->name	               =	Input::get('name');
	$model->phone	               =	Input::get('phone');
	$model->location_name		   =	Input::get('location');
	$model->latitude			   =	Input::get('lat');
	$model->longitude		       =	Input::get('lng');
	$model->time			       =	$clinicTime;
	$model->end_time			   =	$clinic_end_time;
	$model->prep_time			   =	$prep_time;
	$model->date			       =	$clinicDate;
	$model->estimated_duration	   =	Input::get('estimated_duration');
	$model->service_provider	   =	Input::get('service_provider');
	$model->default_unfilled_time  =	Input::get('unfilled_time');
	$model->personnel			   =	Input::get('personnel');
	$model->timezone               =	$clinic_time_zone;
	$model->create_timestamp       =  	time();
	$saved					       =	$model->save();
    $last_insert_id                = 	$model->id;
      if($last_insert_id != null && Input::get('manualprovider') != null){
		$manual_providers =  Input::get('manualprovider');
		foreach($manual_providers as $manual){
			$check_exist = DB::table('clinic_status')->where('clinic_id', '=', $last_insert_id)->where('provider_id',$manual)->first();
			$check_rules = DB::table('rules')->where('clinic_id', '=', $last_insert_id)->where('provider_id',$manual)->first();
			  if ($check_exist==null){
				DB::table('clinic_status')->insert(
									['clinic_id' 		=> $last_insert_id,
									'provider_id' 		=> $manual,
									'status' 			=> 1,
									'create_timestamp' 	=> date('Y-m-d H:i:s')
									]
								);
			  } else {
				DB::table('clinic_status')->where('clinic_id',$last_insert_id)->update(
									['provider_id' => $manual]
								);
			  }
			if(count($manual_providers)<=1){
				if($check_rules == null ){
				DB::table('rules')->insert(
									['clinic_id' 	=> $last_insert_id,
									'provider_id' 	=> $manual,
									'type' 			=> 'primary',
									'status' 		=> 'Sent',
									'created_at' 	=> date('Y-m-d H:i:s'),
									'updated_at' 	=> date('Y-m-d H:i:s')]
								);
				}else{
					DB::table('rules')->where('clinic_id',$last_insert_id)->update(
										['provider_id' => $manual,
										'created_at' => date('Y-m-d H:i:s'),
										'updated_at' => date('Y-m-d H:i:s')]
									);
				}
			}
		}
      }
  		if($saved){
  			return $model;
  		}
  		return false;
  	}
    /**
    * Function for update single clinic.
    *
    * @param user id
    *
    * @return response true on success otherwise false.
    */
    public static function UpdateClinic($id){
		$old_manual_provider    =	ClinicsModel::where('id',$id)->get()->first();
		$old_provider			=   explode(',',$old_manual_provider->manual_provider);
		
		if((Input::get('manual_providers')) && (Input::get('manual_providers') != null) && !in_array(Input::get('manual_providers'),$old_provider)){
			$current_provider       =   implode(',',Input::get('manual_providers'));
			foreach($old_provider as $old){
				// delete old provider from clinic //
				$delete = DB::table('clinic_status')->where('clinic_id', '=', $id)->where('provider_id',$old)->delete();
			}
			foreach(Input::get('manual_providers') as $manual){
				// insert new provider or asign clinic to new provider //
				$check_exist = DB::table('clinic_status')->where('clinic_id', '=', $id)->where('provider_id',$manual)->first();
				 if ($check_exist==null){
					DB::table('clinic_status')->insert(
											['clinic_id' => $id, 
											'provider_id' => $manual,
											 'status' => 1,
											 'create_timestamp' => date('Y-m-d H:i:s')]
										);
				  }
			}
			
		}
		else if((Input::get('manual_providers')) && (Input::get('manual_providers') == null)){
			foreach($old_provider as $old){
				$check_existt = DB::table('clinic_status')->where('clinic_id', '=', $id)->where('provider_id',$old)->first();
				if ($check_existt!=null){
					 DB::table('clinic_status')->where('clinic_id',$id)->where('provider_id',$old)->delete();
					 DB::table('rules')->where('clinic_id',$id)->where('provider_id',$old)->delete();
				  }
			}
		}
      $global_data = DB::table('users')->where('id',Auth::user()->id)->orderBy('id','ASC')->first();
		if(!empty(Input::get('timezone'))){
			$timezone = Input::get('timezone');
		}else{
			$timezone = $global_data->timezone;
		}
	  $clinic_time_zone     	= $timezone;

      $clinic_time    = Input::get('time');
      $clinicDate = date('Y-m-d',strtotime(Input::get('date')));
      $clinic_time_data = new DateTime($clinicDate.' '.$clinic_time, new DateTimeZone($clinic_time_zone));
      $clinic_time_data->setTimezone(new DateTimeZone('GMT'));
      $clinic_date_time 		= $clinic_time_data->format('Y-m-d H:i:s');
      $clinicDate = $clinic_time_data->format('Y-m-d');
      $clinicTime = $clinic_time_data->format('H:i');
	  
	  if(Input::get('manual_providers') && Input::get('manual_providers') != null){
        $providers_id      		 =	implode(',',Input::get('manual_providers'));
		//$model->manual_provider  =	implode(',',Input::get('manual_providers'));
      }
      elseif(Input::get('providers') && Input::get('providers') != null){
        $providers_id       =	implode(',', Input::get('providers'));
      }
      else{
        $providers_id       =	null;
      }

      $time = strtotime($clinic_date_time);

      if(Input::get('preptime') == null){
        $default_prep_time = $result =  DB::table('admin_settings')->select('default_prep_time')->where('id',20)->first();
        $default_preptime   = '-'.$default_prep_time->default_prep_time.' minutes';
        $prep_time  = date("H:i", strtotime($default_preptime, $time));
      }
      else{
        $default_preptime   = '-'.Input::get('preptime').' minutes';
        $prep_time  = date("H:i", strtotime($default_preptime, $time));
      }
  		$model					             =	ClinicsModel::find($id);

      $estimated_duration_time      = 	'+'.Input::get('estimated_duration').' minutes';
      $clinic_end_time  			= date("H:i", strtotime($estimated_duration_time, $time));

      $model->name	              		=	Input::get('name');
	  $model->provider_id	            =	$providers_id;
	  if(Input::get('manual_providers') != null){
		  $model->manual_provider	        =	$providers_id;
	  }else{
		  $model->manual_provider	        =	null;
	  } 
	  if(!empty(Input::get('manual_providers')) && Input::get('manual_providers') != null){
		  foreach(Input::get('manual_providers') as $manual){
				$check_rules = DB::table('rules')->where('clinic_id', '=', $id)->first();
				if(count(Input::get('manual_providers'))<=1){
					if($check_rules == null ){
						DB::table('rules')->insert(
											['clinic_id' => $id,
											 'provider_id' => $manual,
											 'type' => 'primary',
											 'status' => 'Sent',
											 'created_at' => date('Y-m-d H:i:s'),
											 'updated_at' => date('Y-m-d H:i:s')]
										);
					}else{
							DB::table('rules')->where('clinic_id',$id)->update(
												['provider_id' => $manual,
												'created_at' => date('Y-m-d H:i:s'),
												'updated_at' => date('Y-m-d H:i:s')]
											);
					}
				}
			}
	  }
	  
  	  $model->phone	               		=	Input::get('phone');
  	  $model->time			           	=	$clinicTime;
      $model->end_time			       	=	$clinic_end_time;
      $model->prep_time			       	=	$prep_time;
      $model->date			           	=	$clinicDate;
      $model->estimated_duration	 	=	Input::get('estimated_duration');
	  
      $model->personnel			       	=	Input::get('personnel');
      $model->service_provider		 	=	Input::get('service_provider');
      $model->location_name		     	=	Input::get('location');
  	  $model->latitude			       	=	Input::get('lat');
  	  $model->longitude		         	=	Input::get('lng');
      $model->default_unfilled_time		=	Input::get('unfilled_time');
      $model->timezone					=	$clinic_time_zone;
  	  $saved					             =	$model->save();
      // if(Input::get('manualprovider') != null){
      //   $check_exist = DB::table('clinic_status')->where('clinic_id', '=', $id)->first();
      //     if ($check_exist==null){
      //       DB::table('clinic_status')->insert(
      //                           ['clinic_id' => $id, 'provider_id' => Input::get('manualprovider'), 'status' => 1, 'create_timestamp' => date('Y-m-d H:i:s')]
      //                       );
      //     } else {
      //       DB::table('clinic_status')->where('clinic_id',$id)->update(
      //                           ['provider_id' => Input::get('manualprovider')]
      //                       );
      //     }
      //
      // }
  		if(!$saved){
  			return false;
  		}
  		return true;
  	}

    /**
    * Function for get all providers data.
    *
    * @param search,start,length,column_id,column_name(for ajax datatable pagination).
    *
    * @return response usersdata on success otherwise false.
    */
    public static function GetClinics($search="",$start,$length,$column_id,$column_order){
      $column_name = array('name','name','phone','location_name','time','date','phone');
      if($search){
          $usersdata = ClinicsModel::where(function ($query) use ($search) {
            })->where(function ($query) use ($search) {
            $query->where('clinics.location_name', 'LIKE', '%'.$search.'%')
                ->orWhere('clinics.time', 'LIKE', '%'.$search.'%')
                ->orWhere('clinics.name', 'LIKE', '%'.$search.'%')
                ->orWhere('clinics.date', 'LIKE', '%'.$search.'%')
                ->orWhere('clinics.phone', 'LIKE', '%'.$search.'%');
          })->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
      }else{
		  if($column_order != 'desc'){
			 $usersdata = ClinicsModel::orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)->get();
		  }else{
			 $usersdata = ClinicsModel::orderBy('id','desc')->limit($length)->offset($start)->get();
		  }
      }
          if(empty($usersdata)){
            return false;
          }
          return $usersdata;
    }
    public static function Get_Filtered_Clinics($search="",$start,$length,$column_id,$column_order){
      $column_name = array('clinics.id','clinics.name','clinics.phone','clinics.location_name','clinics.time','clinics.date');
      if($search){
        $search_ids = explode(",", $search);
        if(!in_array('all',$search_ids)){
				$resultdata = DB::table('clinics')
                    ->whereIn('provider_id', $search_ids)
                    ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                    ->get();					
          
        }
        else{
				$resultdata =  DB::table('clinics')
                    ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                    ->get();
        }
      }else{
      if($column_order){
            $resultdata = DB::table('clinics')
                    ->orderBy($column_name[$column_id],$column_order)->limit($length)->offset($start)
                    ->get();
      }else{
			$resultdata = DB::table('clinics')
                ->orderBy('certifications.user_id','desc')->limit($length)->offset($start)
                ->get();
      }
      }
          if(empty($resultdata)){
            return false;
          }
          return $resultdata;
    }
	/**
	* Function for get clinic data from clinic Id
	*
	* @param clinic Id
	*
	* @return Clinic data on success otherwise false.
	*/
	public static function GetClinicById($id){
		$result = ClinicsModel::where('id',$id)->first();
		return $result;
	}
}
