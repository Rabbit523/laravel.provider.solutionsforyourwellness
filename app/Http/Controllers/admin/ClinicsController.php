<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\ClinicsModel;
use App\Model\admin\GeoLocationModel;
use App\Model\admin\ClinicsStatusModel;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use App\Model\admin\ProvidersModel;
use App\Model\admin\ClockInAway;
use App\Model\Notifications;
use App\Model\admin\Rules;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast;
use DateTime,DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ClinicsController extends BaseController
{
    public function index(){
      $clinics = ClinicsModel::orderBy('id','desc')->get();
      $providers = DB::table('users')->where('role_id',0)->orderBy('id','desc')->get();
      return  View::make('admin.clinics.index',compact('clinics','providers'));
    }
    // function to display clinic data in databale using ajax
      /**
     * Function for get clinics data use in ajax datatable pagination.
     *
     * @param null
     *
     * @return clinic ajax list page.
     */
    public function ajaxloadclinic(){
       $columns 			= 	Input::get('columns');
       $length				= 	Input::get('length');
       $start				= 	Input::get('start');
       $search				= 	Input::get('search');
       $totaldata 			= 	ClinicsModel::count();
       $total_filtered_data	=	$totaldata;
       $order				=	Input::get('order');
       $column_id			=	$order[0]['column'];
       $column_order		=	$order[0]['dir'];
       $searchdata          = 	$columns[1]['search']['value'];
       $search =   ltrim($searchdata, ',');
       $search_ids = explode(",", $search);
       if($search){
           if(in_array('all',$search_ids)){
             $totaldata 				= 	ClinicsModel::count();
             $total_filtered_data	  	=		$totaldata;
           }
           else{
				$totaldata 			= 	ClinicsModel::whereIn('provider_id',$search_ids)->count();
             $total_filtered_data	=	$totaldata;
           }
         $resultdata				=	ClinicsModel::Get_Filtered_Clinics($search,$start,$length,$column_id,$column_order);
       }
       else{
             $totaldata 			= 	ClinicsModel::count();
            $total_filtered_data	=	$totaldata;
			$resultdata				=	ClinicsModel::Get_Filtered_Clinics($search="",$start,$length,$column_id,$column_order);
       }
      //  if($search != null){
      //    $resultdata		=		ClinicsModel::GetClinics($search,$start,$length,$column_id,$column_order);
      //  }else{
      //    $resultdata			=	ClinicsModel::GetClinics($search="",$start,$length,$column_id,$column_order);
      //  }
       $table_data		=	array();
       return view('admin.clinics.indexajax', ['clinicdetails' => $resultdata,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
     }
    public function add(){
		 Input::replace($this->arrayStripTags(Input::all()));
			if(Input::isMethod('post')){
				$rules = array(
					'name'           	=> 'required',
					'phone'          	=> 'required',
					'date'             	=> 'required',
					'time'             	=> 'required',
					'estimated_duration'=> 'required',
					'personnel'         => 'required',
					'location'          => 'required',
				);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()){
					$messages = $validator->messages();
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
						$clinic_date		=	date('Y-m-d',strtotime(Input::get('date')));
						$clinic_time		=	date('h:i:s',strtotime(Input::get('time')));
						$clinic_location	=	Input::get('location');
						$model 				= 	ClinicsModel::SaveClinic();  //calls function for create clinic.
						// push notification to users starts //
						$last_id 	= $model->id;
						$providers  = explode(',',$model->provider_id);
						if(!empty($providers[0])){
							foreach($providers as $provider){
							$provider_data 	= DB::table('users')->where('id',$provider)->get()->first();
							$device_id_data = DB::table('api_tokens')->where('user_id',$provider)->get()->first();
							if(!empty($device_id_data)){
								$user_device_id	= $device_id_data->device_id;
							if($provider_data->push_notification == 1){
							$notification_message = ["notification" => [
																          	"body" 	=> "New preffered clinic available!",
																          	"title" => "Preffered clinic available"
																       	],
																	"notification_data" => [
																    "clinic_id" 		=> $last_id,
																    "title" 			=> isset($model->name)?$model->name:'',
																    "message" 		=> isset($model->location_name)?$model->location_name:'',
																    "type" 	  			=> 'preffered',
																],
													];
							$PushNotification 	=   $this->notification_api($user_device_id,$notification_message);
								if($PushNotification){
									$new_model        	= new Notifications;
									$new_model->device_id  	=	$user_device_id;
									$new_model->message		=	json_encode($notification_message);
									$new_model->user_id		=	$provider;
									$new_model->required_id	=	$last_id;
									$new_model->type		=	'preffered';
									$new_model->status		=	'sent';
									$saved					=	$new_model->save();
								}
							}
							}
							}
						}					
						// push notification to user end //
						if(Input::get('manualprovider')){
							$manual_provider		=	Input::get('manualprovider');
							$user_data    			= 	ProvidersModel::where('id',$manual_provider)->first();
							if(!empty($user_data)){
								if($user_data->email_notification == 1){
									$user_time_zone_value 	= 	$user_data->timezone;
									$clinic_date_time		= 	new DateTime($clinic_date.' '.$clinic_time, new DateTimeZone('GMT'));
									$clinic_date_time->setTimezone(new DateTimeZone($user_time_zone_value));
									$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
									$date         			= 	$clinic_date_time->format('Y-m-d');
									$time         			= 	$clinic_date_time->format('H:i');
									$user_name     			=   $user_data->first_name ." ". $user_data->last_name;
									$subject_array			=	array($user_name);
									$replace_array 			=   array($user_name,$date,$time,$clinic_location);
									$email_send    			=   $this->mail_send('assign_clinic_to_user',$user_data->email,$user_name,$subject_array,$replace_array);
								}
							}
						}
            // Clinic Confirmation Email
           $provider_ids = explode(",", $model->provider_id);
		   if(!empty($provider_ids[0])){
			   foreach ($provider_ids as $key => $value) {
            $user_data    			= 	ProvidersModel::where('id',$value)->first();
			$user_time_zone_value 	= 	$user_data->timezone;
			$clinic_date_time		= 	new DateTime($clinic_date.' '.$clinic_time, new DateTimeZone('GMT'));
			$clinic_date_time->setTimezone(new DateTimeZone($user_time_zone_value));
			$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
			$date         			= 	$clinic_date_time->format('Y-m-d');
			$time         			= 	$clinic_date_time->format('H:i');
              if($user_data->email_notification == 1){
                 $tookan        =   $this->generate_random_string(20);
				 $type1         =   $this->encrypt('accept');
                 $type2         =   $this->encrypt('reject');
                 $accept_url    =   URL::route('clinic_confirmation',$tookan).'?type='.$type1;
                 $reject_url    =   URL::route('clinic_confirmation',$tookan).'?type='.$type2;
                 $user_name     =   $user_data->first_name ." ". $user_data->last_name;
                 $subject_array			=	array($user_name);
                 $replace_array =   array($user_name,$model->name,$clinic_location,$date,$time,$accept_url,$reject_url);
                 $email_send    =   $this->mail_send('available_clinic_with_accept_reject_link',$user_data->email,$user_name,$subject_array,$replace_array);
                 if($email_send){
					$clinic_id = $model->id;
					$userId    = $user_data->id;
					$token     = $tookan;
					DB::table('accept_reject_tokens')->insert(
						 array(
								'provider_id'   =>   $userId, 
								'clinic_id'   	=>   $clinic_id,
								'token'   		=>   $token,
						)
					);
                 }          
              }
           }
		   }
           
           Toast::success('Clinic successfully added');
						return redirect()->route('clinics');
			}
		}else{
			$timezones = DB::table('timezone')->orderBy('timezone_name','ASC')->get();
			$providers = DB::table('users')->where('role_id',0)->orderBy('id','desc')->get();
			return View::make('admin.clinics.add',compact('providers','timezones'));
		}
	 }
   /**
	* Function to edit clinic
	*
	* @param user id (default null )
	*
	* @return providers list page.
	*/
	public function edit($id=0){
		$CheckClinicStatus = ClinicsStatusModel::CheckClinicStatus($id);
		 if(Input::isMethod('post')){
				$rules = array(
				'name'               => 'required',
				'phone'              => 'required',
				'date'               => 'required',
				'time'               => 'required',
				'estimated_duration' => 'required',
				'personnel'          => 'required',
		  //  'unfilled_time'      => 'numeric',
				);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			  } else {
								$UpdateClinic = ClinicsModel::UpdateClinic($id);    // calls function for update provider data.
									if($UpdateClinic){
					  $clinic = ClinicsModel::where('id',$id)->first();
					  $exp = explode(",", $clinic->provider_id);
						  foreach ($exp as $key => $value) {
							$notification = Notifications::where('type','update_clinic')->where('user_id',$value)->where('required_id',$id)->first();
							if(empty($notification)){
							  $model = new Notifications();
							  $model->user_id = $value;
							  $model->required_id = $id;
							  $model->type = 'update_clinic';
							  $model->status = 'not_sent';
							  $model->save();
							}
						  }
										 Toast::success('Clinic successfully updated');
										return redirect()->route('clinics');
									}else{
										 Toast::error('Technical error');
										return redirect()->route('clinics');
									}
						}
		   }else{
				$timezones = DB::table('timezone')->orderBy('timezone_name','ASC')->get();
				$providers = DB::table('users')->where('role_id',0)->orderBy('id','desc')->get();
				$clinic = ClinicsModel::where('id',$id)->first();
				if($CheckClinicStatus == 0){
					$provider_data = DB::table('providers_timesheet')
					->join('users','users.id','providers_timesheet.provider_id')
					->join('clinics','clinics.id','providers_timesheet.clinic_id')
					->select('users.first_name','users.last_name','clinics.*','providers_timesheet.*')
					->where('providers_timesheet.clinic_id',$id)
					->get()->toArray();
					return view('admin.clinics.edit', compact('clinic','providers','timezones','CheckClinicStatus','provider_data'));
				}else{
				return view('admin.clinics.edit', compact('clinic','providers','timezones','CheckClinicStatus'));
				}
		   }
	}
	/**
			* Function for delete clinic
			*
			* @param null
			*
			* @return view page.
			*/
	public function delete($id = ''){
				if($id){
					// Delete row 
					ClinicsModel::where('id',$id)->delete();
					// Delete row from clinic status
					$if_clinic_status = DB::table('clinic_status')->where('clinic_id',$id)->get();
					if(isset($if_clinic_status) && !empty($if_clinic_status)){
						DB::table('clinic_status')->where('clinic_id',$id)->delete();
					}
					// Delete row from providers_timesheet
					$timesheet_record = DB::table('providers_timesheet')->where('clinic_id',$id)->get();
					if(isset($timesheet_record) && !empty($timesheet_record)){
						DB::table('providers_timesheet')->where('clinic_id',$id)->delete();
					}
					// Delete row from rule records
					$rule_record = DB::table('rules')->where('clinic_id',$id)->get();
					if(isset($rule_record) && !empty($rule_record)){
						DB::table('rules')->where('clinic_id',$id)->delete();
					}
					// Delete row from geo location records
					$geo_record = DB::table('geolocation')->where('clinic_id',$id)->get();
					if(isset($geo_record) && !empty($geo_record)){
						DB::table('geolocation')->where('clinic_id',$id)->delete();
					}
					Toast::success('Clinic successfully deleted');
					return Redirect::back();
				}
				else{
					$checkboxdata = Input::get('chk_ids');
					ClinicsModel::whereIn('id', $checkboxdata)->delete();
					$if_clinic_status = DB::table('clinic_status')->whereIn('clinic_id',$checkboxdata)->get();
					if(isset($if_clinic_status) && !empty($if_clinic_status)){
						DB::table('clinic_status')->whereIn('clinic_id',$checkboxdata)->delete();
					}
					$timesheet_record = DB::table('providers_timesheet')->whereIn('clinic_id',$checkboxdata)->get();
					if(isset($timesheet_record) && !empty($timesheet_record)){
						DB::table('providers_timesheet')->whereIn('clinic_id',$checkboxdata)->delete();
					}
					// Delete row from rule records
					$rule_record = DB::table('rules')->whereIn('clinic_id',$checkboxdata)->get();
					if(isset($rule_record) && !empty($rule_record)){
						DB::table('rules')->whereIn('clinic_id',$checkboxdata)->delete();
					}
					// Delete row from geo location records
					$geo_record = DB::table('geolocation')->whereIn('clinic_id',$checkboxdata)->get();
					if(isset($geo_record) && !empty($geo_record)){
						DB::table('geolocation')->whereIn('clinic_id',$checkboxdata)->delete();
					}
					Toast::success('Clinics successfully deleted');
					return Redirect::back();
				   }
			}
    public function clinic_calender_view(){
        $providers = DB::table('users')->where('role_id',0)->get();
        if(Input::isMethod('post')){
          $search_ids = Input::get('SelectedProvider');
          $calender_views= ClinicsStatusModel::whereIn('provider_id',$search_ids)->get()->toArray();
          //prd($calender_views);
          foreach($calender_views as $calender_view){
            $clinic_id = $calender_view['clinic_id'];
            $clinic_details = ClinicsModel::where('id',$clinic_id)->get()->toArray();
            //prd($clinic_details[0]['date'].' '.$clinic_details[0]['time']);
            $clinic_status_check = ClinicsStatusModel::where('clinic_id',$clinic_id)->get();
           if($clinic_details[0]['date'].' '.$clinic_details[0]['time'] < date('Y-m-d h:i:s')){
              $my_color['color'] = 'grey';
                $my_color['clinic_status'] = 'Past Clinic';
            }
           else if(count($clinic_status_check) > 0){
              $my_color['color'] = '#4682B4';
              $my_color['clinic_status'] = 'Filled Clinic';
            }
            else{
              $my_color['color'] = '#6495ED';
               $my_color['clinic_status'] = 'Unfilled Clinic';
            }
             $all_task = array_merge($calender_view,$my_color);
             $tasks[] = $all_task;
          }
        }
        else{
          $calender_views= ClinicsModel::get()->toArray();
          foreach($calender_views as $calender_view){
            $clinic_id = $calender_view['id'];
            $clinic_details = DB::table('clinics')->where('id',$clinic_id)->get()->toArray();
            $clinic_status_check = DB::table('clinic_status')->where('clinic_id',$clinic_id)->get();
           if($clinic_details[0]->date.' '.$clinic_details[0]->time < date('Y-m-d h:i:s')){
              $my_color['color'] = 'grey';
                $my_color['clinic_status'] = 'Past Clinic';
            }
           else if(count($clinic_status_check) > 0){
              $my_color['color'] = '#4682B4';
              $my_color['clinic_status'] = 'Filled Clinic';
            }
            else{
              $my_color['color'] = '#6495ED';
               $my_color['clinic_status'] = 'Unfilled Clinic';
            }
            $all_task = array_merge($calender_view,$my_color);
            $tasks[] = $all_task;
          }
        }
          return view('admin.clinics.clinic-calender',compact('tasks','providers'));
      }
    public function AsignRule($id = ''){
        Input::replace($this->arrayStripTags(Input::all()));
        if(Input::isMethod('post')){
          foreach (Input::get('provider_id') as $key => $val) {
              $rule_type = Input::get('rule_type_'.$key);
              if($rule_type == 0){
                $type = 'primary';
              }else if($rule_type == 1){
                $type = 'medtech';
              }else{
                $type = 'others';
              }
              $model = new Rules();
              $model->clinic_id = Input::get('clinic_id');
              $model->provider_id = $val;
              $model->type = $type;
              $model->status = 'sent';
              $data =   $model->save();
          }
            Toast::success('Rules asigned');
            return redirect()->route('clinics');
   		}else{
           $clinic_records = DB::table('clinic_status')->join('users','users.id','=', 'clinic_status.provider_id')
           ->where('clinic_status.clinic_id',$id)->where('clinic_status.status',1)->get()->toArray();
   				return View::make('admin.clinics.asign-rule',compact('clinic_records'));
   			 }
			}
    public function clinic_confirm($tookan=null){
		$clinic_data = DB::table('accept_reject_tokens')->where('token',$tookan)->first();
        if(!empty($clinic_data)){
            $type = $this->decrypt(Input::get('type'));
            if($type == 'accept'){
				// checking clinic already accepted or not_sent
				$response	=	$this->ClinicAlreadyAcceptedOrRejected($clinic_data->clinic_id,$clinic_data->provider_id,'accept');
				if($response == 0){
					$model = DB::table('accept_reject_tokens')->where('clinic_id',$clinic_data->clinic_id)->where('provider_id',$clinic_data->provider_id)->update(array('token'=>null));
					// email sending process for confirm clinic starts
					$clinic					=	ClinicsModel::GetClinicById($clinic_data->clinic_id);
					$user					=	ProvidersModel::GetUserById($clinic_data->provider_id);
					if($user->email_notification == 1){
						$user_time_zone_value 	= 	$user->timezone;
						$clinic_date_time		= 	new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
						$clinic_date_time->setTimezone(new DateTimeZone($user_time_zone_value));
						$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
						$date         			= 	$clinic_date_time->format('Y-m-d');
						$time         			= 	$clinic_date_time->format('H:i');
						$subject_array			=	array($user->first_name.' '.$user->last_name);
						$replace_array 			=   array($user->first_name.' '.$user->last_name,$clinic->location_name,$date,$time);
						$email_send    			=   $this->mail_send('confirmation_clinic_email',$user->email,$user->first_name.' '.$user->last_name,$subject_array,$replace_array);	
						// email sending process for confirm clinic ends
					}
					$this->AcceptRejectClinic($clinic_data->id,$clinic_data->provider_id,1);
					Toast::success(trans('Clinic successfully accepted!'));
					return  Redirect::route('admindashboard');
				}else{
					Toast::error(trans('You have already accepted clinic!'));
					return  Redirect::route('admindashboard');
				}
                
            }elseif($type == 'reject'){
                $model = ClinicsModel::find($clinic_data->id);
                $model->tookan = null;
                $model->save();
				// checking clinic already rejected or not_sent
				$response	=	$this->ClinicAlreadyAcceptedOrRejected($clinic_data->clinic_id,$clinic_data->provider_id,'raject');
				if($response == 0){
					$this->AcceptRejectClinic($clinic_data->clinic_id,$clinic_data->provider_id,0);
					Toast::success(trans('Clinic successfully rejected!'));
					return  Redirect::route('admindashboard');
				}else{
					Toast::error(trans('You have already rejected clinic!'));
					return  Redirect::route('admindashboard');
				}
            }else{
              Toast::error(trans('You are using wrong link!'));
              return  Redirect::route('admindashboard');
            }
        }else{
          Toast::error(trans('You are using wrong link!'));
           return Redirect::route('admindashboard');
        }
      }
	/**
	* Function check clinic already accepted or not
	*
	* @param clinic Id,Provider Id,Key
	*
	* @return 1 on success otherwise 0.
	*/  
	public function ClinicAlreadyAcceptedOrRejected($clinic_id,$provider_id,$key){
		if($key == 'accept'){
			$status = 1;
		}else{
			$status = 0;
		}
		$count = ClinicsStatusModel::where('clinic_id',$clinic_id)
				->where('provider_id',$provider_id)
				->where('status',$status)
				->count();
		if($count>0){
			return 1;
		}else{
			return 0;
		}		
	}
	public function AcceptRejectClinic($clinic_id,$provider_id,$status){
		$status_model 					= 	new ClinicsStatusModel();
		$status_model->clinic_id 		= 	$clinic_id;
		$status_model->provider_id 		= 	$provider_id;
		$status_model->create_timestamp = 	date('Y-m-d H:i:s');
		$status_model->status 			= 	$status;
		$status_model->save();
	}
	public function check_asign_status(){
		$provider_id = Input::get('provider_id');
		$clinic_id = Input::get('clinic_id');
		$status_model 	= ClinicsStatusModel::whereIn('provider_id',$provider_id)->where('clinic_id',$clinic_id)->whereNull('clock_in')->count();
		if($status_model > 0){
			echo 1;
		}else{
			echo 0;
		}
	}
	/**
    * Function for show all clock in away providers list.
    *
    * @param null.
    *
    * @return cities list page.
    */	
	public function clock_in_away(){
		
			$clock_in_away = DB::table('clock_in_away')->orderBy('id','desc')->get();
			return  View::make('admin.clinics.clock_in_away',compact('clock_in_away'));
	}
	// function to display user data with away clock in in databale using ajax
    /**
   * Function for get users data use in ajax datatable pagination.
   *
   * @param null
   *
   * @return users ajax list page.
   */
    public function ajaxloadclock_in_away(){
      $length					= 	Input::get('length');
      $start					= 	Input::get('start');
      $search					= 	Input::get('search');
      $totaldata 				= 	ClockInAway::count();
      $total_filtered_data	  	=	$totaldata;
      $search					=	$search['value'];
      $order					=	Input::get('order');
      $column_id				=	$order[0]['column'];
      $column_order				=	$order[0]['dir'];
      if($search != null){
        $userdata				=	ClockInAway::GetData($search,$start,$length,$column_id,$column_order);
      }else{
        $userdata				=	ClockInAway::GetData($search="",$start,$length,$column_id,$column_order);
      }
      $table_data		=	array();
      return view('admin.clinics.clock_in_away_ajax', ['clinicdetails' => $userdata,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
    }
	/**
	* Function for delete clock in away records
	*
	* @param null
	*
	* @return view page.
	*/
	public function delete_clockin($id = ''){
		if($id){
			// Delete row
			ClockInAway::where('id',$id)->delete();
			Toast::success('Record successfully deleted');
			return Redirect::back();
		}
		else{
			$checkboxdata = Input::get('chk_ids');
			ClockInAway::whereIn('id', $checkboxdata)->delete();
			Toast::success('Record successfully deleted');
			return Redirect::back();
		   }
	}
	/**
	* Function to view clinic on clinic
	*
	* @param user id (default null )
	*
	* @return map list page.
	*/
	public function clinic_on_map($id=0,$userId){
		//$timezones = DB::table('timezone')->orderBy('timezone_name','ASC')->get();
		$provider = DB::table('users')->where('id',$userId)->first();
		$clinic	=	DB::table('clinic_status')->join('clinics','clinics.id','=','clinic_status.clinic_id')->where('clinic_status.clinic_id',$id)->where('clinic_status.provider_id',$userId)->first();
	
		if(!empty($clinic)){
			$providergeodata 	=	GeoLocationModel::GetProviderLocationByClinic($userId, $clinic->id, $clinic->clock_in, $clinic->clock_out, $clinic->latitude, $clinic->longitude);
			
			$red_time 			= GeoLocationModel::GetProviderRedTime($userId, $clinic->id, $clinic->clock_in, $clinic->clock_out, $clinic->latitude, $clinic->longitude);
			
			$red_yellow_time 	= GeoLocationModel::GetProviderRedYellowTime($userId, $clinic->id, $clinic->clock_in, $clinic->clock_out, $clinic->latitude, $clinic->longitude);
			
			$green_time 		= GeoLocationModel::GetProviderGreenTime($userId, $clinic->id, $clinic->clock_in, $clinic->clock_out, $clinic->latitude, $clinic->longitude);
		}
		return view('admin.clinics.clinic_on_map',compact('provider','clinic','providergeodata','red_time','red_yellow_time','green_time'));
	}
	/**
	* Function to get priovider geo location 
	*
	* @param user id (default null )
	*
	* @return 1 or 0
	*/
	public function get_provider_location(){
		$provider_id 		= Input::get('provider_id');
		$clinic_id 			= Input::get('clinic_id');
		$range_value 		= Input::get('range_value');
		
		$clinic_data = DB::table('clinic_status')->where('clinic_status.provider_id',$provider_id)->where('clinic_status.clinic_id',$clinic_id)->first();
		$user_details  		= DB::table('users')->where('id',$provider_id)->first();
		$clinic_details  	= DB::table('clinics')->where('id',$clinic_id)->first();
		$clinic_lat			= $clinic_details->latitude;
		$clinic_lng			= $clinic_details->longitude;
		
		$clock_in_time = $clinic_data->clock_in;
		$time_after_range  	= date('Y-m-d H:i:s',strtotime('+'.$range_value.' minutes',strtotime($clock_in_time)));
		$time_before_range  = date('Y-m-d H:i:s',strtotime('+ 1 minute 20 seconds',strtotime($time_after_range)));
		//prd($clock_in_time);
		$start_range_search = $time_after_range;
		$end_range_search	= $time_before_range;
		
		$clinic_start_data = DB::table('geolocation')->where('clinic_id',$clinic_id)->where('user_id',$provider_id)->first();
		if(isset($clinic_start_data) && $clinic_start_data != null){
			$user_start_lat = $clinic_start_data->latitude;
			$user_start_long = $clinic_start_data->longitude;
		}else{
			$user_start_lat = '';
			$user_start_long = '';
		}
		
		$search_user_geolocation = DB::table('geolocation')
									->where('geolocation.user_id',$provider_id)
									->where('geolocation.clinic_id',$clinic_id)
									->where('created_at','<=',$end_range_search)
									->where('created_at','>=',$start_range_search)
									->first();
									//print_r(DB::getQueryLog());
		$search_user_geolocation;
		if($search_user_geolocation){
			$search_lat_lng 			= $search_user_geolocation->latitude.'~'.$search_user_geolocation->longitude;
		}else{
			$search_lat_lng = $user_start_lat.'~'.$user_start_long;
			//$search_lat_lng = 0;
		}
		if($search_lat_lng){
			echo $search_lat_lng;
		}else{
			echo 0;
		}
	}
	public function CutManualTime(){
		 Input::replace($this->arrayStripTags(Input::all()));
			if(Input::isMethod('post')){
				$rules = array(
					'cut_time'   => 'required',
				);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()){
					$messages = $validator->messages();
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
					$provider_id 	= 	Input::get('provider_id');
					$clinic_id 		= 	Input::get('clinic_id');
					$clinic_details = DB::table('clinics')->join('clinic_status','clinics.id','clinic_status.clinic_id')->where('clinics.id',$clinic_id)->first();
					
					$clinic_lat 	= $clinic_details->latitude;
					$clinic_long 	= $clinic_details->longitude;
					$clock_in 		= $clinic_details->clock_in;
					$clock_out		= $clinic_details->clock_out;
					
					$green_miles_start 	= $this->GetAdminSettingsValue('green_miles_start');
					$green_miles_end 	= $this->GetAdminSettingsValue('green_miles_end');
					$yellow_miles_start = $this->GetAdminSettingsValue('yellow_miles_start');
					$yellow_miles_end 	= $this->GetAdminSettingsValue('yellow_miles_end');
					$red_miles_start 	= $this->GetAdminSettingsValue('red_miles_start');
					//prd(Input::all());
					if(Input::get('cut_time') == 'red'){
						$red_data = DB::select("select * from `geolocation` WHERE created_at BETWEEN '".$clock_in."' AND '".$clock_out."' AND clinic_id='".$clinic_id."' AND user_id='".$provider_id."' AND ( 6371 * acos( cos( radians(".$clinic_lat.") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$clinic_long.") ) + sin( radians(".$clinic_lat.") ) * sin( radians( `latitude` ) ) ) ) > ".$red_miles_start."");
						if(!empty($red_data)){
							$removable_time 	= 	count($red_data);
							$timesheet_data		=	DB::table('providers_timesheet')
													->select('clinic_spend_time','income','hourly_rate')
													->where('clinic_id',$clinic_id)
													->where('provider_id',$provider_id)
													->first();
							$old_spend_time     =	$timesheet_data->clinic_spend_time;
							$hourly_rate		= 	$timesheet_data->hourly_rate;
							$new_spend_time		=	$old_spend_time-$removable_time;
							$new_income			=   number_format(($new_spend_time/60)*$hourly_rate,2);
							$remove_status 		=	'red';
							$update_geo_records = 	DB::table('providers_timesheet')
													->where('clinic_id',$clinic_id)
													->where('provider_id',$provider_id)
													->update([
													'clinic_spend_time' => $new_spend_time,
													'income' 			=> $new_income,
													'removed_status' 	=> $remove_status,
													'removed_time' 		=> $removable_time,
													]);
						
						$red_geo = DB::select("Update `geolocation` SET status='0' WHERE created_at BETWEEN '".$clock_in."' AND '".$clock_out."' AND clinic_id='".$clinic_id."' AND user_id='".$provider_id."' AND ( 6371 * acos( cos( radians(".$clinic_lat.") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$clinic_long.") ) + sin( radians(".$clinic_lat.") ) * sin( radians( `latitude` ) ) ) ) > ".$red_miles_start."");
						Toast::success('Red time removed');
						}else{
							Toast::error('Red time not found');
							Redirect::back();
						}
						
						
					}elseif(Input::get('cut_time') == 'red_yellow'){
						$red_yellow_data = DB::select("select * from `geolocation` WHERE created_at BETWEEN '".$clock_in."' AND '".$clock_out."' AND clinic_id='".$clinic_id."' AND user_id='".$provider_id."' AND ( 6371 * acos( cos( radians(".$clinic_lat.") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$clinic_long.") ) + sin( radians(".$clinic_lat.") ) * sin( radians( `latitude` ) ) ) ) > ".$yellow_miles_start."");
						if(!empty($red_yellow_data)){
							$removable_time 	= 	count($red_yellow_data);
							$timesheet_data		=	DB::table('providers_timesheet')
													->select('clinic_spend_time','income','hourly_rate')
													->where('clinic_id',$clinic_id)
													->where('provider_id',$provider_id)
													->first();
							$old_spend_time     =	$timesheet_data->clinic_spend_time;
							$hourly_rate		= 	$timesheet_data->hourly_rate;
							$new_spend_time		=	$old_spend_time-$removable_time;
							$new_income			=   number_format(($new_spend_time/60)*$hourly_rate,2);
							$remove_status 		=	'red_yellow';
							$update_geo_records = 	DB::table('providers_timesheet')
													->where('clinic_id',$clinic_id)
													->where('provider_id',$provider_id)
													->update([
													'clinic_spend_time' => $new_spend_time,
													'income' 			=> $new_income,
													'removed_status' 	=> $remove_status,
													'removed_time' 		=> $removable_time,
													]);
						$red_yellow_geo = DB::select("Update `geolocation` SET status='0' WHERE created_at BETWEEN '".$clock_in."' AND '".$clock_out."' AND clinic_id='".$clinic_id."' AND user_id='".$provider_id."' AND ( 6371 * acos( cos( radians(".$clinic_lat.") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$clinic_long.") ) + sin( radians(".$clinic_lat.") ) * sin( radians( `latitude` ) ) ) ) > ".$yellow_miles_start."");
						Toast::success('Red & yellow time removed');
						} else{
							Toast::error('Red & yellow time not found');
							Redirect::back();
						}
					}
					elseif(Input::get('cut_time') == 'custom'){
						$cut_time = Input::get('cutmanualtime');
						if($cut_time != null){
							$removable_time 	= 	count($cut_time);
							$timesheet_data		=	DB::table('providers_timesheet')
													->select('clinic_spend_time','income','hourly_rate')
													->where('clinic_id',$clinic_id)
													->where('provider_id',$provider_id)
													->first();
							$old_spend_time     =	$timesheet_data->clinic_spend_time;
							$hourly_rate		= 	$timesheet_data->hourly_rate;
							$new_spend_time		=	$old_spend_time-$removable_time;
							$new_income			=   number_format(($new_spend_time/60)*$hourly_rate,2);
							$remove_status 		=	'custom';
							$update_geo_records = 	DB::table('providers_timesheet')
													->where('clinic_id',$clinic_id)
													->where('provider_id',$provider_id)
													->update([
													'clinic_spend_time' => $new_spend_time,
													'income' 			=> $new_income,
													'removed_status' 	=> $remove_status,
													'removed_time' 		=> $removable_time,
													]);
													
							$clinic_spend_time 	= $clinic_details->clinic_spend_time;
							$less_time 			= $clinic_spend_time-$cut_time;
							DB::select("Update `clinic_status` SET clinic_spend_time='".$less_time."' WHERE clinic_id='".$clinic_id."' AND provider_id='".$provider_id."'");
							Toast::success('Custom time removed');
						}else{
							Toast::error('Custom time not found');
							Redirect::back();
						}
						
					}
					return redirect()->route('clinic_on_map',array($clinic_id,$provider_id));					
				   
				}
		}else{
			$timezones = DB::table('timezone')->orderBy('timezone_name','ASC')->get();
			$providers = DB::table('users')->where('role_id',0)->orderBy('id','desc')->get();
			return View::make('admin.clinics.add',compact('providers','timezones'));
		}
	 }
}
