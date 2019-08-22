<?php

namespace App\Http\Controllers\front;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\ClinicsModel;
use App\Model\ApiTokens;
use App\Model\AnnouncementModel;
use App\Model\TimesheetRecords;
use App\Model\AnnouncementStatusModel;
use App\Model\ClinicStatusModel;
use App\Model\Rules;
use App\Model\GeoLocationModel;
use App\Model\UpcomingSwipeModel;
use App\Model\OthersSwipeModel;
use App\Model\ProvidersModel;
use App\Model\User_model;
use Faker\Factory as Faker;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Image;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DateTime,DateTimeZone;

class ClinicsController extends BaseController
{
	 /**
     * function for get all .
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function GetClinics(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
			  'user_id' 	=> 'required',
			  //'device_id' => 'required',
			  'platform_type'=> 'required',
			  'latitude'  	=> 'required',
			  'longitude'  	=> 'required',
			  'type'  		=> 'required',
			  'type_value'  => 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		 }else{
			 
			$config_date 		= Config::get('date_format.date');
			$config_month 		= Config::get('date_format.month');
			$config_year 		= Config::get('date_format.year');
			$config_separator 	= Config::get('date_format.separator');
						
			 
			/* $input_data['user_id'] = '38';
			 $input_data['type'] = 'day';
			 $input_data['type_value'] = '20-09-2017';   */
				$user_id 		= 	$input_data['user_id'];
				$user			=	User_model::GetUserById($user_id);
				if(!empty($user)){
				/* $user_device_id         =       $input_data['device_id'];
					if(isset($user_device_id) && $user_device_id != null){
						$update_device_id		=       $this->UpdateDeviceId($user_device_id,$user_id);
					} */
				$all_clinics 	= 	array();
				if(isset($input_data['type']) && $input_data['type'] == 'upcoming' && $input_data['type_value'] == 'upcoming'){
					$distance  	= $this->GetAdminSettingsValue('max_distance');
					$sqlQuery = "SELECT *,
																( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) AS distance
														FROM clinics WHERE `date` >= curdate() AND
														(
														FIND_IN_SET(".$input_data['user_id'].", `provider_id`)  OR  ( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) <= $distance )
														ORDER BY distance ASC";
					$clinics_s = DB::select(DB::raw($sqlQuery));
					$clinics   = (array) $clinics_s;
					if(!empty($clinics)){
						foreach($clinics as $clinic){
							$clinic  = (array)$clinic;
							$clinic_time_gmt  		= $clinic['date'].' '.$clinic['time'];
							$current_time_gmt 		= date('Y-m-d H:i:s');
							$clinic_created_gmt 	= $clinic['created_at'];
							
							
							$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
							$user_time_zone 	  = User_model::select('timezone')->where('id',$input_data['user_id'])->get();
							$user_time_zone_value = $user_time_zone[0]['timezone'];
							$time->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_date_time 	  = $time->format('Y-m-d H:i');
							$clinic_time					=	$clinic_date_time;
							

							$current = new DateTime(date('Y-m-d h:i:s'));
							$current->setTimezone(new DateTimeZone($user_time_zone_value));
							$current_timestamp 				= strtotime($current->format('Y-m-d H:i'));
							$current_time             = 	$current->format('Y-m-d H:i:s');
							

							$clinic_creation = new DateTime($clinic['created_at'], new DateTimeZone('GMT'));
							$clinic_creation->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_created             = 	$clinic_creation->format('Y-m-d H:i:s');

							$preferred_wait_hour 	= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
							if($clinic['default_unfilled_time'] != null){
								$wait_time					=	'+'.$clinic['default_unfilled_time'].' hour';
							}
							else{
								$wait_time					=	'+'.$preferred_wait_hour.'hour';
							}
							$clinic_creation_time_with_wait_time	=	date('Y-m-d H:i:s',strtotime($wait_time,strtotime($clinic_created_gmt)));
							if(strtotime($current_time_gmt) < strtotime($clinic_time_gmt)){
							// checking clinic already accepted by someone or not.
								$count 	= 	ClinicStatusModel::
											where('clinic_id',$clinic['id'])
											->where('status',1)->count();
							if($count<$clinic['personnel']){
								//checking clinic accepted or rejected by this user.
								$check_decline_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',0)
																	->count();
								$check_accept_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',1)
																	->count();
																	
								if($check_decline_status==0 && $check_accept_status == 0){
									$clinic['clinic_name']	=	$clinic['name'];
									// clinic time according to user time zone starts //
									$Preptime 		= new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
									$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
									$prep_time 		= $Preptime->format('H:i');

									$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
									$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
									$clinic_end_time 		= $EndTime->format('H:i');

									$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
										if($user['time_format'] == 24){
											$format = 'H:i';
										}elseif($user['time_format'] == 12){
											$format = 'h:i a';
										}
										$clinic['time']					=	date($format, strtotime($clinic_date_time));
										$clinic['prep_time']		=	date($format, strtotime($prep_time));
										$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
									// clinic time according to user time zone ends //
									$clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
									if($current_time_gmt>$clinic_creation_time_with_wait_time){
										$clinic['type'] 				= 'Unfilled';
										$clinic['status_name'] 			= 'Unfilled clinic';
									}elseif($current_time_gmt < $clinic_time_gmt){
										if(in_array($input_data['user_id'],explode(',',$clinic['provider_id']))){
											$clinic['type'] 			= 	'preferd';
											$clinic['status_name']		= 	'Preferred available Clinics';
										}else{
											$clinic['type'] 			= 	'available_clinics';
											$clinic['status_name']		= 	'Available clinics';
										}
									}
									$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
									
									if(strtotime($clinic_time_gmt)> strtotime($current_time_gmt)){
										// if($clinic['primary_provider'] == $input_data['user_id'] || $clinic['medtech_provider'] == $input_data['user_id'] || $clinic['other_provider'] == $input_data['user_id']){
											$all_clinics[$clinic['date']][] = $clinic;
										}
										// if(strtotime($current_time)>strtotime($clinic_creation_time_with_wait_time)){
										// 	$all_clinics[$clinic['date']][] = $clinic;
										// }
								}
							}
							}
						}
					}
		  }else if(isset($input_data['type']) && $input_data['type'] == 'month'){
						$month_number 	= 	date('m', strtotime($input_data['type_value']));
						$month_name 		= 	date('F', strtotime($input_data['type_value']));
						$year 					= 	date('Y', strtotime($input_data['type_value']));

						$distance  	= $this->GetAdminSettingsValue('max_distance');
						$sqlQuery = "SELECT *,
																	( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) AS distance
															FROM clinics WHERE Year(`date`)=".$year." AND Month(`date`)=".$month_number." AND `date` >= curdate() AND
															(
														FIND_IN_SET(".$input_data['user_id'].", `provider_id`)  OR  ( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) <= $distance )
															ORDER BY distance ASC";
						$clinics_s = DB::select(DB::raw($sqlQuery));
						$clinics   = (array) $clinics_s;
				if(!empty($clinics)){
					foreach($clinics as $clinic){
							$clinic  = (array)$clinic;
							$clinic_time_gmt  		= $clinic['date'].' '.$clinic['time'];
							$current_time_gmt 		= date('Y-m-d H:i:s');
							$clinic_created_gmt 	= $clinic['created_at'];
							
							$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
							$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
							$user_time_zone_value = $user_time_zone[0]['timezone'];
							$time->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_date_time 		= $time->format('Y-m-d H:i:s');
							$clinic_time					=	$clinic_date_time;

							$current = new DateTime(date('Y-m-d h:i:s'));
							$current->setTimezone(new DateTimeZone($user_time_zone_value));
							$current_timestamp 				= strtotime($current->format('Y-m-d H:i'));
							$current_time             = 	$current->format('Y-m-d H:i:s');

							$clinic_creation = new DateTime($clinic['created_at'], new DateTimeZone('GMT'));
							$clinic_creation->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_created             = 	$clinic_creation->format('Y-m-d H:i:s');

							$preferred_wait_hour 	= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
							if($clinic['default_unfilled_time'] != null){
									$wait_time				=	'+'.$clinic['default_unfilled_time'].' hour';
							}
							else{
									$wait_time				=	'+'.$preferred_wait_hour.'hour';
							}
							$clinic_creation_time_with_wait_time	=	date('Y-m-d H:i:s',strtotime($wait_time,strtotime($clinic_created_gmt)));
						if(strtotime($current_time_gmt) < strtotime($clinic_time_gmt)){
							$count 						= 	ClinicStatusModel::
															where('clinic_id',$clinic['id'])
															->where('status',1)->count();
						if($count<$clinic['personnel']){
							//checking clinic accepted or rejected by this user.
								$check_decline_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',0)
																	->count();
								$check_accept_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',1)
																	->count();
								if($check_decline_status==0 && $check_accept_status == 0){

								$clinic['clinic_name']	=	$clinic['name'];
								// clinic time according to user time zone starts //
								$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
								$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
								$prep_time 		= $Preptime->format('H:i');

								$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
								$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_end_time 		= $EndTime->format('H:i');

								$clinic['date']		=	date('d-m-Y', strtotime($clinic_date_time));
								
								if($user['time_format'] == 24){
									$format = 'H:i';
								}elseif($user['time_format'] == 12){
									$format = 'h:i a';
								}
								$clinic['time']					=	date($format, strtotime($clinic_date_time));
								$clinic['prep_time']		=	date($format, strtotime($prep_time));
								$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
								// clinic time according to user time zone ends //
								$clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
								if($current_time_gmt>$clinic_creation_time_with_wait_time){
										$clinic['type'] 				= 'Unfilled';
										$clinic['status_name'] 	= 'Unfilled clinic';
								}else{
									if(in_array($input_data['user_id'],explode(',',$clinic['provider_id']))){
											$clinic['type'] 			= 	'preferd';
											$clinic['status_name']		= 	'Preferred available Clinics';
									}else{
										$clinic['type'] 			= 	'available_clinics';
										$clinic['status_name']		= 	'Available clinics';
									}
								}
								if(strtotime($clinic_time_gmt)> strtotime($current_time_gmt)){									
									$all_clinics[] = $clinic;
								}
							}
						}
						}
					}
				}
			}else if(isset($input_data['type']) && $input_data['type'] == 'day'){
					//$input_data['type_value'] = '25-09-2017';
					$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
					$user_time_zone_value 		= $user_time_zone[0]['timezone'];

					$start_time 				=	$input_data['type_value']." 12:00 AM";
					$end_time 					=	$input_data['type_value']." 11:59 PM";
					
					$user_time_zone 		=	new DateTimeZone($user_time_zone_value);
					$end_search_date  	= new DateTime($end_time, $user_time_zone);
					$start_search_date  = new DateTime($start_time, $user_time_zone);

					$start_search_date->setTimezone(new DateTimeZone('GMT'));
					$start_searchFinalDate = $start_search_date->format('Y-m-d H:i');

					$end_search_date  = new DateTime($end_time, $user_time_zone);

					$end_search_date->setTimezone(new DateTimeZone('GMT'));
					$end_searchFinalDate = $end_search_date->format('Y-m-d H:i');

					
					$date 			= 	date('Y-m-d', strtotime($input_data['type_value']));
					$month_name	=	strtolower(date("F", strtotime($date)));
					$search 		= 	$input_data['type_value'];

					$distance  	= $this->GetAdminSettingsValue('max_distance');
					$sqlQuery = "SELECT *,
																( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) AS distance
														FROM clinics WHERE (STR_TO_DATE(CONCAT(`date`, ' ' ,`time`), '%Y-%m-%d %H:%i') BETWEEN STR_TO_DATE('".$start_searchFinalDate."', '%Y-%m-%d %H:%i') AND STR_TO_DATE('".$end_searchFinalDate."', '%Y-%m-%d %H:%i')) AND (
														FIND_IN_SET(".$input_data['user_id'].", `provider_id`)  OR  ( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) <= $distance )
														ORDER BY distance ASC";
					$clinics_s = DB::select(DB::raw($sqlQuery));
					$clinics   = (array) $clinics_s;
						if(!empty($clinics)){
							foreach($clinics as $clinic){
								$clinic  = (array)$clinic;
								$clinic_time_gmt  		= $clinic['date'].' '.$clinic['time'];
								$current_time_gmt 		= date('Y-m-d H:i:s');
								$clinic_created_gmt 	= $clinic['created_at'];
									
								$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
								$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
								$user_time_zone_value = $user_time_zone[0]['timezone'];
								$time->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_date_time 		= $time->format('Y-m-d H:i:s');
								$clinic_time					=	$clinic_date_time;

								$current = new DateTime(date('Y-m-d h:i:s'));
								$current->setTimezone(new DateTimeZone($user_time_zone_value));
								$current_timestamp 				= strtotime($current->format('Y-m-d H:i'));
								$current_time             = 	$current->format('Y-m-d H:i:s');

								$clinic_creation = new DateTime($clinic['created_at'], new DateTimeZone('GMT'));
								$clinic_creation->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_created             = 	$clinic_creation->format('Y-m-d H:i:s');

								$preferred_wait_hour 	= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
								if($clinic['default_unfilled_time'] != null){
										$wait_time					=	'+'.$clinic['default_unfilled_time'].' hour';
								}
								else{
										$wait_time					=	'+'.$preferred_wait_hour.'hour';
								}
							$clinic_creation_time_with_wait_time	=	date('Y-m-d H:i:s',strtotime($wait_time,strtotime($clinic_created_gmt)));
							if(strtotime($current_time_gmt) < strtotime($clinic_time_gmt)){
								// checking clinic already accepted by someone or not.
								$count 							= 	ClinicStatusModel::
														where('clinic_id',$clinic['id'])
														->where('status',1)->count();
							if($count<$clinic['personnel']){
								//checking clinic accepted or rejected by this user.
								$check_decline_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',0)
																	->count();
								$check_accept_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',1)
																	->count();
								if($check_decline_status==0 && $check_accept_status == 0){

										$clinic['clinic_name']	=	$clinic['name'];
										// clinic time according to user time zone starts //
										$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
										$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
										$prep_time 		= $Preptime->format('H:i');

										$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
										$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
										$clinic_end_time 		= $EndTime->format('H:i');

										//$clinic['date']		=	date('d-m-Y', strtotime($clinic_date_time));
										$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic_date_time));
											if($user['time_format'] == 24){
												$format = 'H:i';
											}elseif($user['time_format'] == 12){
												$format = 'h:i a';
											}
											$clinic['time']					=	date($format, strtotime($clinic_date_time));
											$clinic['prep_time']		=	date($format, strtotime($prep_time));
											$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
										// clinic time according to user time zone ends //
										$clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
										if($current_time_gmt>$clinic_creation_time_with_wait_time){
										$clinic['type'] 				= 'Unfilled';
										$clinic['status_name'] 			= 'Unfilled clinic';
										}else{
											if(in_array($input_data['user_id'],explode(',',$clinic['provider_id']))){
											$clinic['type'] 			= 	'preferd';
											$clinic['status_name']		= 	'Preferred available Clinics';
											}else{
												$clinic['type'] 			= 	'available_clinics';
												$clinic['status_name']		= 	'Available clinics';
											}
										}
										if(strtotime($clinic_time_gmt)> strtotime($current_time_gmt)){
											$all_clinics[] = $clinic;
										}
									}
								}
								}
							}
						}
			  }else if(isset($input_data['type']) && $input_data['type'] == 'week'){
					$day_range 			= 	explode(',',$input_data['type_value']);
					$start_date			=	date('Y-m-d', strtotime($day_range[0]));
					$end_date			=	date('Y-m-d', strtotime($day_range[1]));

					$distance  	= $this->GetAdminSettingsValue('max_distance');
					$sqlQuery = "SELECT *,
																( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) AS distance
														FROM clinics WHERE (date BETWEEN '".$start_date."' AND '".$end_date."') AND
														(
														FIND_IN_SET(".$input_data['user_id'].", `provider_id`)  OR  ( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) <= $distance )
														ORDER BY distance ASC";
					$clinics_s = DB::select(DB::raw($sqlQuery));
					$clinics   = (array) $clinics_s;
					if(!empty($clinics)){
					foreach($clinics as $clinic){
							$clinic  = (array)$clinic;
							$clinic_time_gmt  		= $clinic['date'].' '.$clinic['time'];
							$current_time_gmt 		= date('Y-m-d H:i:s');
							$clinic_created_gmt 	= $clinic['created_at'];
							
							$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
							$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
							$user_time_zone_value = $user_time_zone[0]['timezone'];
							$time->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_date_time 		= $time->format('Y-m-d H:i:s');
							$clinic_time					=	$clinic_date_time;

							$current = new DateTime(date('Y-m-d h:i:s'));
							$current->setTimezone(new DateTimeZone($user_time_zone_value));
							$current_timestamp 				= strtotime($current->format('Y-m-d H:i'));
							$current_time             = 	$current->format('Y-m-d H:i:s');

							$clinic_creation = new DateTime($clinic['created_at'], new DateTimeZone('GMT'));
							$clinic_creation->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_created             = 	$clinic_creation->format('Y-m-d H:i:s');

								$preferred_wait_hour 	= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
								if($clinic['default_unfilled_time'] != null){
										$wait_time					=	'+'.$clinic['default_unfilled_time'].' hour';
								}
								else{
										$wait_time					=	'+'.$preferred_wait_hour.'hour';
								}
							$clinic_creation_time_with_wait_time	=	date('Y-m-d H:i:s',strtotime($wait_time,strtotime($clinic_created)));
						if(strtotime($current_time_gmt) < strtotime($clinic_time_gmt)){
						// checking clinic already accepted by someone or not.
								$count 	= 	ClinicStatusModel::
											where('clinic_id',$clinic['id'])
											->where('status',1)->count();
						if($count<$clinic['personnel']){
							//checking clinic accepted or rejected by this user.
								$check_decline_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',0)
																	->count();
								$check_accept_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',1)
																	->count();
								if($check_decline_status==0 && $check_accept_status == 0){
									$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
									// clinic time according to user time zone starts //
									$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
									$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
									$prep_time 		= $Preptime->format('H:i');

									$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
									$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
									$clinic_end_time 		= $EndTime->format('H:i');

										if($user['time_format'] == 24){
											$format = 'H:i';
										}elseif($user['time_format'] == 12){
											$format = 'h:i a';
										}
										$clinic['time']					=	date($format, strtotime($clinic_date_time));
										$clinic['prep_time']		=	date($format, strtotime($prep_time));
										$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
								$preferred_wait_hour 		= 	$this->GetAdminSettingsValue('preferred_wait_time');
								if($clinic['default_unfilled_time'] != null){
									$time_addition				=	'+'.$clinic['default_unfilled_time'].' hour';
								}
								else{
									$time_addition				=	'+'.$preferred_wait_hour.' hour';
								}
								$clinic_time					=	date('Y-m-d H:i:s',strtotime($clinic['date'].' '.$clinic['time']));
								// adding wait time or default unfilled time to clinic creation date.
								$clinic_creation_time_with_addition		=	date('Y-m-d H:i:s',strtotime($time_addition,strtotime($clinic_created_gmt)));

								$clinic['clinic_name']				=	$clinic['name'];
								// clinic time according to user time zone ends //
								$clinic['start_year']					=	date('Y', strtotime($clinic['date']));
								$clinic['start_month']				=	date('m', strtotime($clinic['date']));
								$clinic['start_day']					=	date('d', strtotime($clinic['date']));
								$clinic['end_year']						=	date('Y', strtotime($clinic['date']));
								$clinic['end_month']					=	date('m', strtotime($clinic['date']));
								$clinic['end_day']						=	date('d', strtotime($clinic['date']));
								$clinic['start_hour']					=	date('H', strtotime($clinic['time']));
								$clinic['start_minute']				=	date('i', strtotime($clinic['time']));
								$clinic['start_meridiem']			=	date('A', strtotime($clinic['time']));
								//get clinic end time from addition clinic duration into clinic time.
								$time_addition								=	'+'.$clinic['estimated_duration'].' minutes';

								$clinic_end_timestamp 				= 	strtotime($time_addition, strtotime($clinic['time']));
								$clinic['end_hour']						=	date('H', $clinic_end_timestamp);
								$clinic['end_minute']					=	date('i', $clinic_end_timestamp);
								$clinic['end_meridiem']				=	date('A', $clinic_end_timestamp);
								$clinic['duration']						=	number_format((($clinic['estimated_duration'])/60),2).' hour';
								$clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
								$clinic['mileage_required']		=	'';
								$clinic['drive_time_required']=	'';
								$clinic['mileage_status']			=	'';
								$clinic['drive_time_status']	=	'';
								$date 												= 	$clinic['date'];
								$time 												= 	$clinic['time'];
								$full_date 										= 	$time.' '.$date;
								if($current_time_gmt>$clinic_creation_time_with_addition){
									$clinic['type'] 				= 'Unfilled';
									$clinic['status_name'] 	= 'Unfilled clinic';
								}else{
									if(in_array($input_data['user_id'],explode(',',$clinic['provider_id']))){
											$clinic['type'] 			= 	'preferd';
											$clinic['status_name']		= 	'Preferred available Clinics';
									}else{
										$clinic['type'] 			= 	'available_clinics';
										$clinic['status_name']		= 	'Available clinics';
									}
								}
								if(strtotime($clinic_time_gmt)> strtotime($current_time_gmt)){
										$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
										$all_clinics[] = $clinic;
								}
							}
						}
						}
					}
				}
			  }else{
					$distance  	= $this->GetAdminSettingsValue('max_distance');
					$sqlQuery = "SELECT	*,
																( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) AS distance
														FROM clinics WHERE `date` >= curdate() AND
														(
														FIND_IN_SET(".$input_data['user_id'].", `provider_id`)  OR  ( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) <= $distance )
														ORDER BY distance ASC";
					$clinics_s = DB::select(DB::raw($sqlQuery));
					$clinics   = (array) $clinics_s;
				if(!empty($clinics)){
					foreach($clinics as $clinic){
						$clinic  = (array)$clinic;
						$clinic_time_gmt  		= $clinic['date'].' '.$clinic['time'];
						$current_time_gmt 		= date('Y-m-d H:i:s');
						$clinic_created_gmt 	= $clinic['created_at'];
						
						$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
						$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
						$user_time_zone_value = $user_time_zone[0]['timezone'];
						$time->setTimezone(new DateTimeZone($user_time_zone_value));
						$clinic_date_time 		= $time->format('Y-m-d H:i:s');
						$clinic_time					=	$clinic_date_time;

						$current = new DateTime(date('Y-m-d h:i:s'));
						$current->setTimezone(new DateTimeZone($user_time_zone_value));
						$current_timestamp 				= strtotime($current->format('Y-m-d H:i'));
						$current_time             = 	$current->format('Y-m-d H:i:s');

						$clinic_creation = new DateTime($clinic['created_at'], new DateTimeZone('GMT'));
						$clinic_creation->setTimezone(new DateTimeZone($user_time_zone_value));
						$clinic_created             = 	$clinic_creation->format('Y-m-d H:i:s');

								$preferred_wait_hour 	= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
								if($clinic['default_unfilled_time'] != null){
										$wait_time					=	'+'.$clinic['default_unfilled_time'].' hour';
								}
								else{
										$wait_time					=	'+'.$preferred_wait_hour.'hour';
								}
							$clinic_creation_time_with_wait_time	=	date('Y-m-d H:i:s',strtotime($wait_time,strtotime($clinic_created)));
						if(strtotime($current_time_gmt) < strtotime($clinic_time_gmt)){
						// checking clinic already accepted by someone or not.
								$count 	= 	ClinicStatusModel::
											where('clinic_id',$clinic['id'])
											->where('status',1)->count();
						if($count<$clinic['personnel']){
							//checking clinic accepted or rejected by this user.
								$check_decline_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',0)
																	->count();
								$check_accept_status 	= 	ClinicStatusModel::
																	where('clinic_id',$clinic['id'])
																	->where('provider_id',$input_data['user_id'])
																	->where('status',1)
																	->count();
								if($check_decline_status==0 && $check_accept_status == 0){
									// clinic time according to user time zone starts //
									$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
									$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
									$prep_time 		= $Preptime->format('H:i');

									$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
									$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
									$clinic_end_time 		= $EndTime->format('H:i');

									$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
										if($user['time_format'] == 24){
											$format = 'H:i';
										}elseif($user['time_format'] == 12){
											$format = 'h:i a';
										}
										$clinic['time']					=	date($format, strtotime($clinic_date_time));
										$clinic['prep_time']		=	date($format, strtotime($prep_time));
										$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
									// clinic time according to user time zone ends //
								$preferred_wait_hour 			= 	$this->GetAdminSettingsValue('preferred_wait_time');
								if($clinic['default_unfilled_time'] != null){
									$time_addition					=	'+'.$clinic['default_unfilled_time'].' hour';
								}
								else{
									$time_addition				=	'+'.$preferred_wait_hour.' hour';
								}
								$clinic_time						=	date('Y-m-d H:i:s',strtotime($clinic['date'].' '.$clinic['time']));
								// adding wait time or default unfilled time to clinic creation date.
								$clinic_creation_time_with_addition		=	date('Y-m-d H:i:s',strtotime($time_addition,strtotime($clinic_created_gmt)));

								$clinic['clinic_name']			=	$clinic['name'];

								$clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
								if($current_time_gmt>$clinic_creation_time_with_addition){
									$clinic['type'] 				= 'Unfilled';
									$clinic['status_name'] 	= 'Unfilled clinic';
								}else{
									if(in_array($input_data['user_id'],explode(',',$clinic['provider_id']))){
											$clinic['type'] 			= 	'preferd';
											$clinic['status_name']		= 	'Preferred available Clinics';
									}else{
										$clinic['type'] 			= 	'available_clinics';
										$clinic['status_name']		= 	'Available clinics';
									}
								}
								if(strtotime($clinic_time_gmt)> strtotime($current_time_gmt)){
											$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
											$all_clinics[$clinic['date']][] = $clinic;
									}
								}
							}
						}
						}
					}
			}
			$userimg = WEBSITE_UPLOADS_URL.'users/'.$user['image'];
				if(isset($user['image']) && $user['image'] != '' ){
					$user_image = $userimg;
				}else{
					$user_image = WEBSITE_UPLOADS_URL.'users/man.png';
				}
			if(!empty($all_clinics)){
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok.','clinics'=>$all_clinics,'image'=>$user_image)));
			}else{
				return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found in your location.')));
			}
		}else{
			return $this->encrypt(json_encode(array('status'=>'deleted','message'=>'User is deleted.')));
		}
		}
	}
	 /**
     * function for my clinics for calendar data.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function MyClinics(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 		 => 'required',
		  //'device_id' 	 => 'required',
		  'platform_type'  	 => 'required',
		  'latitude'  		 => 'required',
		  'longitude'  		 => 'required',
		  'type'  			 => 'required',
		  'type_value'  	 => 'required', 
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
				$config_date 		= Config::get('date_format.date');
				$config_month 		= Config::get('date_format.month');
				$config_year 		= Config::get('date_format.year');
				$config_separator 	= Config::get('date_format.separator');
			  
				/*  $input_data['user_id'] = 108;
				$input_data['type'] = 'week';
				$input_data['type_value'] = '29-09-2017,30-09-2017'; 
				$input_data['device_id'] = 'scascascascqwd56'; */
				$user_id 		= 	$input_data['user_id']; 
				$user				=	User_model::GetUserById($user_id);
				if(!empty($user)){
					$user_device_id         	=  $input_data['device_id'];
					if(isset($user_device_id) && $user_device_id != null){
						$update_device_id		=  $this->UpdateDeviceId($user_device_id,$user_id);
					}
					$all_clinics = array();
					$is_past_clinic  = 0;
					if(isset($input_data['type']) && $input_data['type'] == 'upcoming' && $input_data['type_value'] == 'upcoming'){
						$clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')->where('clinic_status.provider_id',$input_data['user_id'])
					      ->where('clinic_status.status',1)->orderBy('clinics.date','Desc')->get()->toArray();
					      if(!empty($clinics)){
					        foreach($clinics as $clinic){
							   // preptime key for offline clinic starts//
								$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
							   // preptime key for offline clinic ends here //
					          $is_past_clinic  = 0;
					          $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					          ->where('provider_id',$input_data['user_id'])
					          ->where('status',1)->count();
					          $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
							 // prd($mileage_info);
					          if(!empty($mileage_info)){
					            if($mileage_info[0]['mileage'] != "" && $mileage_info[0]['drive_time'] != ""){
					            $mileage 		= $mileage_info[0]['mileage'].' Miles';
					            $drive_time 	= $mileage_info[0]['drive_time'].' Minutes';
					            }
					          }
					          if($my_clinic_count>0){
					            $is_my_clinic = true;
					          }else{
					            $is_my_clinic = false;
					          }
										$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
										$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
										$user_time_zone_value = $user_time_zone[0]['timezone'];
										$time->setTimezone(new DateTimeZone($user_time_zone_value));
										$clinic_date_time 		= $time->format('Y-m-d H:i:s');
										$clinic_date_only 		= $time->format('Y-m-d');


										$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
										$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
										$prep_time 		= $Preptime->format('H:i');

										$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
										$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
										$clinic_end_time 		= $EndTime->format('H:i');

										$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
											if($user['time_format'] == 24){
												$format = 'H:i';
											}elseif($user['time_format'] == 12){
												$format = 'h:i a';
											}
											$clinic['time']					=	date($format, strtotime($clinic_date_time));
											$clinic['prep_time']		=	date($format, strtotime($prep_time));
											$clinic['end_time']			=	date($format, strtotime($clinic_end_time));

									  $clinic['timezone'] 			= 	$clinic['timezone'];
									  $clinic['contact_name'] 		= 	$clinic['name'];
									  $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
									  $date 								= 	$clinic['date'];
									  $time 								= 	$clinic['time'];
									  $full_date 						= 	$time.' '.$date;
									  $clinic_timestamp 		= 	strtotime($clinic_date_time);
									  //$current_timestamp 		=	time();
										$current = new DateTime(date('Y-m-d H:i:s'));
										$current->setTimezone(new DateTimeZone($user_time_zone_value));
										$current_timestamp 				= strtotime($current->format('Y-m-d H:i:s'));
										$current_date             		= 	$current->format('Y-m-d');

										$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
										$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
					
										//$setting_time 					=	$this->GetAdminSettingsValue('default_prep_time');
										//$before_timestamp 				=		($clinic_timestamp-($setting_time*60));
										$clinic_end_timestamp			=		strtotime($clinic['date'].' '.$clinic['end_time']);

										// primary, medtech, other data listing starts //
										$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
										$primary_name = $primary['first_name'].' '.$primary['last_name'];

										$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
										$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

										$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
										foreach($others as $other){
												$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
										}
										// primary, medtech, other data listing ends //
										$clinic['primary_name']			=	isset($primary_name)?$primary_name:array();
										$clinic['medtech_name']			=	isset($medtech_name)?$medtech_name:array();
										$clinic['other_name']			=	isset($other_names)?$other_names:array();
										$other_names					=	array();
							
							$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					          if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
								$clinic['clocked']						=	"";
					            $is_past_clinic 						= 	1;
					            $clinic['type'] 						= 	'Past';
					            $clinic['status_name'] 					= 	'Past clinic';
					            $clinic['clinic_name'] 					= 	$clinic['name'];
					            $clinic['name'] 						= 	$clinic['name'];
					            $clinic['duration'] 					= 	$clinic['estimated_duration'];
					            $clinic['mileage_required'] 			=	isset($mileage)?$mileage:'';
					            $clinic['drive_time_required'] 			= 	isset($drive_time)?$drive_time:'';
					            if(empty($mileage)){
					              $clinic['mileage_status'] 		=	0;
					            }else{
					              $clinic['mileage_status'] 		=	1;
					            }
					            if(empty($drive_time)){
					              $clinic['drive_time_status'] 	= 	0;
					            }else{
					              $clinic['drive_time_status'] 	= 	1;
					            }
					          }else{
								  if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
											if(!empty($clinic_status)){
												if($clinic_status->clock_in != null){
													$clinic['clocked']		=	0;
												}elseif($clinic_status->clock_in == null){
													$clinic['clocked']		=	1;
												}
												if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
													$clinic['clocked']			=	"";
													$is_past_clinic 			= 	1;
													$clinic['type'] 			= 	'Past';
													$clinic['status_name'] 		= 	'Past clinic';
												}else {
												$clinic['type'] 				= 	'clock_in';
												$clinic['status_name']			= 	'Clock in clinic';
												}
											}
										}else{
											$clinic['clocked']			=	"";
											$clinic['type'] 				= 	'MyClinics';
								$clinic['status_name'] 	= 	'My clinic';
										}
					            $clinic['name'] 				= 	$clinic['name'];
					            $clinic['duration'] 		= 	'';
					            $clinic['mileage_required'] 		=	"";
					            $clinic['drive_time_required'] 	= 	"";
					            $clinic['mileage_status'] 			=	1;
					            $clinic['drive_time_status'] 		= 	1;
					          }
					         if($is_my_clinic && $is_past_clinic != 1){
								 $clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
					           $all_clinics[] 	= $clinic;
					         }
					      }
					    }
					}else if(isset($input_data['type']) && $input_data['type'] == 'month'){
					    $month_number 				= 	date('m', strtotime($input_data['type_value']));
					    $month_start_date 			= 	$this->firstDay();
					    $month_end_date 			= 	$this->lastDay();
					    $start_date_timestamp 		= 	strtotime($month_start_date);
					    $first_date 				= 	strtotime("-7 day", $start_date_timestamp);
					    $my_query_start_date		= 	date('Y-m-d', $first_date);
					    $end_date_timestamp 		= 	strtotime($month_end_date);
					    $last_date 					= 	strtotime("+7 day", $end_date_timestamp);
					    $my_query_end_date			= 	date('Y-m-d', $last_date);
						
						$clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')->where('clinic_status.provider_id',$input_data['user_id'])
							->whereBetween('clinics.date',[$my_query_start_date,$my_query_end_date])
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();
							
					if(!empty($clinics)){
					  foreach($clinics as $clinic){
					  // preptime key for offline clinic starts//
						$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
					  // preptime key for offline clinic ends here //
					    $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					    ->where('provider_id',$input_data['user_id'])
					    ->where('status',1)->count();
					    $clinic_status 		=  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->where('provider_id',$input_data['user_id'])->get()->toArray();
					    if(!empty($clinic_status)){
					      if($clinic_status[0]['mileage'] != "" && $clinic_status[0]['drive_time'] != ""){
					        $mileage 			= $clinic_status[0]['mileage'].' Miles';
					        $drive_time 		= $clinic_status[0]['drive_time'].' Minutes';
					      }
					    }
					    if($my_clinic_count>0){
					      $is_my_clinic = true;
					    }else{
					      $is_my_clinic = false;
					    }
							// primary, medtech, other data listing starts //
							$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
							$primary_name = $primary['first_name'].' '.$primary['last_name'];

							$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
							$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

							$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
							foreach($others as $other){
									$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
							}
					      // primary, medtech, other data listing ends //
					    $clinic['primary_name']			=	isset($primary_name)?$primary_name:array();
					    $clinic['medtech_name']			=	isset($medtech_name)?$medtech_name:array();
					    $clinic['other_name']			=	isset($other_names)?$other_names:array();

						$clinic['timezone'] 			= 	$clinic['timezone'];
						$clinic['contact_name'] 		= 	$clinic['name'];
					    $other_names 					=	array();
					    $userdata 						=	User_model::GetUserById($input_data['user_id']);
					    $clinic['system_calender']		=	$userdata['system_calender'];

							$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
							$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
							$user_time_zone_value = $user_time_zone[0]['timezone'];
							$time->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_date_time 		= $time->format('Y-m-d H:i:s');
							$clinic_date_only 		= $time->format('Y-m-d');

							$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
							$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
							$prep_time 		= $Preptime->format('H:i');

							$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
							$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_end_time 		= $EndTime->format('H:i');

							$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
								if($user['time_format'] == 24){
									$format = 'H:i';
								}elseif($user['time_format'] == 12){
									$format = 'h:i a';
								}
								$clinic['time']				=	date($format, strtotime($clinic_date_time));
								$clinic['prep_time']		=	date($format, strtotime($prep_time));
								$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
					    $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					    $date 							= 	$clinic['date'];
					    $time 							= 	$clinic['time'];
					    $full_date 					= 	$date.' '.$time;
					    $clinic_timestamp 	= 	strtotime(date('Y-m-d H:i',strtotime($full_date)));

							$current = new DateTime(date('Y-m-d H:i:s'));
							$current->setTimezone(new DateTimeZone($user_time_zone_value));
							$current_timestamp 				= strtotime($current->format('Y-m-d H:i:s'));
							$current_date             = 	$current->format('Y-m-d');
					   // $current_timestamp 	=	strtotime(date("Y-m-d h:i:sa"));
						 $allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
						$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
						 $clinic_end_timestamp			=		strtotime($clinic['date'].' '.$clinic['end_time']);

						$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					    if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
						  $clinic['clocked']			=	"";
					      $clinic['type'] 					= 	'Past';
					      $clinic['status_name'] 			= 	'Past clinic';
					      $clinic['name'] 					= 	$clinic['name'];
					      $clinic['duration'] 				= 	$clinic['estimated_duration'];
					      $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
					      $clinic['drive_time_required']	= 	isset($drive_time)?$drive_time:'';
					      if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
					    }else{
							if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
								//if($current_timestamp>$before_timestamp && $clinic_end_timestamp>$current_timestamp){
									if(!empty($clinic_status)){
										if($clinic_status->clock_in != null){
											$clinic['clocked']		=	0;
										}elseif($clinic_status->clock_in == null){
											$clinic['clocked']		=	1;
										}
										if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
											$clinic['clocked']			=	"";
											$is_past_clinic 			= 	1;
											$clinic['type'] 			= 	'Past';
											$clinic['status_name'] 		= 	'Past clinic';
										}else {
										$clinic['type'] 				= 	'clock_in';
										$clinic['status_name']			= 	'Clock in clinic';
										}
									}
								}else{
									$clinic['clocked']			=	"";
									$clinic['type'] 			= 	'MyClinics';
									$clinic['status_name'] 		= 	'My clinic';
								}
					      $clinic['name'] 						= 	$clinic['name'];
					      $clinic['duration'] 					= 	'';
					      $clinic['mileage_required'] 			=	'';
					      $clinic['drive_time_required']		= 	'';
						  if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
					    }
					    if($is_my_clinic){
						  $all_clinics[] 	= $clinic;
					    }
					  }
					}
					}else if(isset($input_data['type']) && $input_data['type'] == 'day'){
						//$currentTime = date('H:i:s',time());
						// 30-08-2017

						$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
						$user_time_zone_value = $user_time_zone[0]['timezone'];

						$start_time 				=	$input_data['type_value']." 12:00 AM";
						$end_time 					=	$input_data['type_value']." 11:59 PM";
						$user_time_zone 		=	new DateTimeZone($user_time_zone_value);
						$end_search_date  	= new DateTime($end_time, $user_time_zone);
						$start_search_date  = new DateTime($start_time, $user_time_zone);

						$start_search_date->setTimezone(new DateTimeZone('GMT'));
						$start_searchFinalDate = $start_search_date->format('Y-m-d H:i');

						$end_search_date  = new DateTime($end_time, $user_time_zone);

						$end_search_date->setTimezone(new DateTimeZone('GMT'));
						$end_searchFinalDate = $end_search_date->format('Y-m-d H:i');

						$clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
							->where('clinic_status.provider_id',$input_data['user_id'])
							->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ' ,`time`), '%Y-%m-%d %H:%i') BETWEEN STR_TO_DATE('".$start_searchFinalDate."', '%Y-%m-%d %H:%i') AND STR_TO_DATE('".$end_searchFinalDate."', '%Y-%m-%d %H:%i')")
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();					

							//prd($clinics);
							if(!empty($clinics)){
					      foreach($clinics as $clinic){
							 // preptime key for offline clinic starts//
								$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
							  // preptime key for offline clinic ends here //
					        $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					        ->where('provider_id',$input_data['user_id'])
					        ->where('status',1)->count();
							$clinic_status 		=  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->where('provider_id',$input_data['user_id'])->get()->toArray();
							if(!empty($clinic_status)){
							  if($clinic_status[0]['mileage'] != "" && $clinic_status[0]['drive_time'] != ""){
								$mileage 			= $clinic_status[0]['mileage'].' Miles';
								$drive_time 		= $clinic_status[0]['drive_time'].' Minutes';
							  }
							}
					        /* $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
					        if(!empty($mileage_info)){
					            if($mileage_info[0]['mileage'] != "" && $mileage_info[0]['drive_time'] != ""){
					            $mileage 			= $mileage_info[0]['mileage'];
					            $drive_time 		= $mileage_info[0]['drive_time'];
					            }
					        } */
					        if($my_clinic_count>0){
					          $is_my_clinic 	= true;
					        }else{
					          $is_my_clinic 	= false;
					        }
							$clinic['timezone'] 			= 	$clinic['timezone'];
							$clinic['contact_name'] = 	$clinic['name'];
							$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));

							$time->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_date_time 		= $time->format('Y-m-d H:i:s');
							$clinic_date_only 		= $time->format('Y-m-d');

							$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
							$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
							$prep_time 		= $Preptime->format('H:i');

							$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));

							$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_end_time 		= $EndTime->format('H:i');

							$clinic['date']					=	$time->format('d-m-Y');
								if($user['time_format'] == 24){
									$format = 'H:i';
								}elseif($user['time_format'] == 12){
									$format = 'h:i a';
								}
								$clinic['time']				=	date($format, strtotime($clinic_date_time));
								$clinic['prep_time']		=	date($format, strtotime($prep_time));
								$clinic['end_time']			=	date($format, strtotime($clinic_end_time));

					        $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					        $date 							= 	$clinic['date'];
					        $time 							= 	$clinic['time'];
					        $full_date 					= 	$date.' '.$time;
					        $clinic_timestamp 	= 	strtotime($full_date);
									$current = new DateTime(date('Y-m-d H:i:s'));
									$current->setTimezone(new DateTimeZone($user_time_zone_value));
									$current_timestamp 		  = strtotime($current->format('Y-m-d H:i:s'));
									$current_date             = $current->format('Y-m-d');
									$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
									$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
									$clinic_end_timestamp	  =	strtotime($clinic['date'].' '.$clinic['end_time']);

									// primary, medtech, other data listing starts //
									$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
									$primary_name = $primary['first_name'].' '.$primary['last_name'];

									$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
									$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

									$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
									foreach($others as $other){
											$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
									}
									// primary, medtech, other data listing ends //
									$clinic['primary_name']			=	isset($primary_name)?$primary_name:array();
									$clinic['medtech_name']			=	isset($medtech_name)?$medtech_name:array();
									$clinic['other_name']			=	isset($other_names)?$other_names:array();
									$other_names					=	array();
					        //$current_timestamp 	=	time();
					        $clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					        if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
										$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
										if(!empty($clinic_status)){
											if($clinic_status->clock_in == null || $clinic_status->clock_out == null){
												$clinic['mileage_status'] 		=	1;
												$clinic['drive_time_status'] 	= 	1;
											}else{
												if($clinic_status->mileage == 0){
									              $clinic['mileage_status'] 		=	0;
									            }else{
									              $clinic['mileage_status'] 		=	1;
									            }
									            if($clinic_status->drive_time == 0){
									              $clinic['drive_time_status'] 	= 	0;
									            }else{
									              $clinic['drive_time_status'] 	= 	1;
									            }
											}
										}
										
							  $clinic['clocked']				=	"";
					          $clinic['type'] 					= 	'Past';
					          $clinic['status_name'] 			= 	'Past clinic';
					          $clinic['name'] 					= 	$clinic['name'];
					          $clinic['duration'] 				= 	$clinic['estimated_duration'];
					          $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
					          $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';

					        }else{
								if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
									if(!empty($clinic_status)){
										if($clinic_status->clock_in != null){
											$clinic['clocked']		=	0;
										}elseif($clinic_status->clock_in == null){
											$clinic['clocked']		=	1;
										}
										if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
											$clinic['clocked']			=	"";
											$is_past_clinic 			= 	1;
											$clinic['type'] 			= 	'Past';
											$clinic['status_name'] 		= 	'Past clinic';
										}else {
										$clinic['type'] 				= 	'clock_in';
										$clinic['status_name']			= 	'Clock in clinic';
										}
									}

								}else{
									$clinic['clocked']			=	"";
									$clinic['type'] 			= 	'MyClinics';
									$clinic['status_name'] 		= 	'My clinic';
								}
					          $clinic['name'] 							= $clinic['name'];
					          $clinic['duration'] 						= '';
					          $clinic['mileage_required'] 		= '';
					          $clinic['drive_time_required']	= '';
					          //$clinic['mileage_status'] 			=	1;
					          //$clinic['drive_time_status'] 		= 	1;
							  if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
							 
					        }
					        if($is_my_clinic){
							  $clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
					          $all_clinics[] 	= $clinic;
					        }

					      }
					  }
					}else if(isset($input_data['type']) && $input_data['type'] == 'week'){
					  $day_range 		= explode(',',$input_data['type_value']);
					  $start_date		=	date('Y-m-d', strtotime($day_range[0]));
					  $end_date			=	date('Y-m-d', strtotime($day_range[1]));
					  $clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
							->where('clinic_status.provider_id',$input_data['user_id'])
							->whereBetween('date',[$start_date,$end_date])
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();	
					  if(!empty($clinics)){
					    foreach($clinics as $clinic){
						// preptime key for offline clinic starts//
							$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
						// preptime key for offline clinic ends here //
					      $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					      ->where('provider_id',$input_data['user_id'])
					      ->where('status',1)->count();
					      $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
					      $clinic_status 		=  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->where('provider_id',$input_data['user_id'])->get()->toArray();
							if(!empty($clinic_status)){
							  if($clinic_status[0]['mileage'] != "" && $clinic_status[0]['drive_time'] != ""){
								$mileage 			= $clinic_status[0]['mileage'].' Miles';
								$drive_time 		= $clinic_status[0]['drive_time'].' Minutes';
							  }
							}
					      if($my_clinic_count>0){
					        $is_my_clinic = true;
					      }else{
					        $is_my_clinic = false;
					      }
								// primary, medtech, other data listing starts //
								$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
								$primary_name = $primary['first_name'].' '.$primary['last_name'];

								$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
								$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

								$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
								foreach($others as $other){
										$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
								}
					      // primary, medtech, other data listing ends //
					      $clinic['primary_name']		=	isset($primary_name)?$primary_name:array();
					      $clinic['medtech_name']		=	isset($medtech_name)?$medtech_name:array();
					      $clinic['other_name']			=	isset($other_names)?$other_names:array();
					      $other_names 					=	array();
					      $userdata 					=	User_model::GetUserById($input_data['user_id']);
								$clinic['timezone'] 			= 	$clinic['timezone'];
								$clinic['contact_name'] = 	$clinic['name'];
								$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
								$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
								$user_time_zone_value = $user_time_zone[0]['timezone'];
								$time->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_date_time 		= $time->format('Y-m-d H:i:s');
								$clinic_date_only 		= $time->format('Y-m-d');

								$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
								$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
								$prep_time 		= $Preptime->format('H:i');

								$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
								$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_end_time 		= $EndTime->format('H:i');

								$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
									if($user['time_format'] == 24){
										$format = 'H:i';
									}elseif($user['time_format'] == 12){
										$format = 'h:i a';
									}
									$clinic['time']				=	date($format, strtotime($clinic_date_time));
									$clinic['prep_time']		=	date($format, strtotime($prep_time));
									$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
								// clinic time according to user time zone ends //
					      $clinic['system_calender']		=	$userdata['system_calender'];
					      $clinic['start_year']				=	date('Y', strtotime($clinic['date']));
					      $clinic['start_month']			=	date('m', strtotime($clinic['date']));
					      $clinic['start_day']				=	date('d', strtotime($clinic['date']));
					      $clinic['end_year']				=	date('Y', strtotime($clinic['date']));
					      $clinic['end_month']				=	date('m', strtotime($clinic['date']));
					      $clinic['end_day']				=	date('d', strtotime($clinic['date']));
					      $clinic['start_hour']				=	date('H', strtotime($clinic['time']));
					      $clinic['start_minute']			=	date('i', strtotime($clinic['time']));
					      //get clinic end time from addition clinic duration into clinic time.
					      $time_addition					=	'+'.$clinic['estimated_duration'].' minutes';
					      //$clinic['date']					=	date('d-m-Y', strtotime($clinic['date']));
					      //$clinic['time']					=	date('H:i', strtotime($clinic['time']));
					      $clinic_end_timestamp 			= 	strtotime($time_addition, strtotime($clinic['time']));
					      $clinic['end_hour']				=	date('H', $clinic_end_timestamp);
					      $clinic['end_minute']			=	date('i', $clinic_end_timestamp);
					      //$clinic['prep_time']			=	date('h:i a', strtotime($clinic['prep_time']));

					      $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					      $date 							= 	$clinic['date'];
					      $time 							= 	$clinic['time'];
					      $full_date 						= 	$date.' '.$time;
					      $clinic_timestamp 				= 	strtotime($full_date);
								$current = new DateTime(date('Y-m-d H:i:s'));
								$current->setTimezone(new DateTimeZone($user_time_zone_value));
								$current_timestamp 				= strtotime($current->format('Y-m-d H:i:s'));
								$current_date             		= 	$current->format('Y-m-d');
								$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
								$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
								$clinic_end_timestamp			=		strtotime($clinic['date'].' '.$clinic['end_time']);

							$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					        if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
							$clinic['clocked']				=	"";
					        $clinic['type'] 				= 	'Past';
					        $clinic['status_name'] 			= 	'Past clinic';
					        $clinic['name'] 				= 	$clinic['name'];
					        $clinic['duration'] 			= 	$clinic['estimated_duration'];
					        $clinic['mileage_required'] 	=	isset($mileage)?$mileage:'';
					        $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
					        if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
					      }else{
								if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
									if(!empty($clinic_status)){
										if($clinic_status->clock_in != null){
											$clinic['clocked']		=	0;
										}elseif($clinic_status->clock_in == null){
											$clinic['clocked']		=	1;
										}
										if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
											$clinic['clocked']			=	"";
											$is_past_clinic 			= 	1;
											$clinic['type'] 			= 	'Past';
											$clinic['status_name'] 		= 	'Past clinic';
										}else {
										$clinic['type'] 				= 	'clock_in';
										$clinic['status_name']			= 	'Clock in clinic';
										}
									}
								}else{
									$clinic['clocked']			=	"";
									$clinic['type'] 			= 	'MyClinics';
									$clinic['status_name'] 		= 	'My clinic';
								}
					        $clinic['name'] 				= 	$clinic['name'];
					        $clinic['duration'] 			= 	'';
					        $clinic['mileage_required'] 	=	'';
					        $clinic['drive_time_required'] 	= 	'';
					        if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }

					      }
					      if($is_my_clinic){
							$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
					        $all_clinics[] 	= $clinic;
					      }
					    }
					  }
					}
					elseif(isset($input_data['type']) && $input_data['type'] == 'offline' && $input_data['type'] == 'offline'){
					  $clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
							->where('clinic_status.provider_id',$input_data['user_id'])
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();	
					  if(!empty($clinics)){
					    foreach($clinics as $clinic){
						// preptime key for offline clinic starts//
						$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
						// preptime key for offline clinic ends here //
						
					      $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					      ->where('provider_id',$input_data['user_id'])
					      ->where('status',1)->count();
					      $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
					      $clinic_status 		=  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->where('provider_id',$input_data['user_id'])->get()->toArray();
							if(!empty($clinic_status)){
							  if($clinic_status[0]['mileage'] != "" && $clinic_status[0]['drive_time'] != ""){
								$mileage 			= $clinic_status[0]['mileage'].' Miles';
								$drive_time 		= $clinic_status[0]['drive_time'].' Minutes';
							  }
							}
					      if($my_clinic_count>0){
					        $is_my_clinic = true;
					      }else{
					        $is_my_clinic = false;
					      }
								// primary, medtech, other data listing starts //
								$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
								$primary_name = $primary['first_name'].' '.$primary['last_name'];

								$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
								$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

								$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
								foreach($others as $other){
										$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
								}
					      // primary, medtech, other data listing ends //
					      $clinic['primary_name']		=	isset($primary_name)?$primary_name:array();
					      $clinic['medtech_name']		=	isset($medtech_name)?$medtech_name:array();
					      $clinic['other_name']			=	isset($other_names)?$other_names:array();
					      $other_names 					=	array();
					      $userdata 					=	User_model::GetUserById($input_data['user_id']);
								$clinic['timezone'] 			= 	$clinic['timezone'];
								$clinic['contact_name'] = 	$clinic['name'];
								$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
								$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
								$user_time_zone_value = $user_time_zone[0]['timezone'];
								$time->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_date_time 		= $time->format('Y-m-d H:i:s');
								$clinic_date_only 		= $time->format('Y-m-d');

								$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
								$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
								$prep_time 		= $Preptime->format('H:i');

								$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
								$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_end_time 		= $EndTime->format('H:i');

								$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
									if($user['time_format'] == 24){
										$format = 'H:i';
									}elseif($user['time_format'] == 12){
										$format = 'h:i a';
									}
									$clinic['time']				=	date($format, strtotime($clinic_date_time));
									$clinic['prep_time']		=	date($format, strtotime($prep_time));
									$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
								// clinic time according to user time zone ends //
					      $clinic['system_calender']		=	$userdata['system_calender'];
					      $clinic['start_year']				=	date('Y', strtotime($clinic['date']));
					      $clinic['start_month']			=	date('m', strtotime($clinic['date']));
					      $clinic['start_day']				=	date('d', strtotime($clinic['date']));
					      $clinic['end_year']				=	date('Y', strtotime($clinic['date']));
					      $clinic['end_month']				=	date('m', strtotime($clinic['date']));
					      $clinic['end_day']				=	date('d', strtotime($clinic['date']));
					      $clinic['start_hour']				=	date('H', strtotime($clinic['time']));
					      $clinic['start_minute']			=	date('i', strtotime($clinic['time']));
					      //get clinic end time from addition clinic duration into clinic time.
					      $time_addition					=	'+'.$clinic['estimated_duration'].' minutes';
					      //$clinic['date']					=	date('d-m-Y', strtotime($clinic['date']));
					      //$clinic['time']					=	date('H:i', strtotime($clinic['time']));
					      $clinic_end_timestamp 			= 	strtotime($time_addition, strtotime($clinic['time']));
					      $clinic['end_hour']				=	date('H', $clinic_end_timestamp);
					      $clinic['end_minute']			=	date('i', $clinic_end_timestamp);
					      //$clinic['prep_time']			=	date('h:i a', strtotime($clinic['prep_time']));

					      $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					      $date 							= 	$clinic['date'];
					      $time 							= 	$clinic['time'];
					      $full_date 						= 	$date.' '.$time;
					      $clinic_timestamp 				= 	strtotime($full_date);
							$current = new DateTime(date('Y-m-d H:i:s'));
							$current->setTimezone(new DateTimeZone($user_time_zone_value));
							$current_timestamp 				= strtotime($current->format('Y-m-d H:i:s'));
							$current_date             		= 	$current->format('Y-m-d');
							$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
							$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
							$clinic_end_timestamp			=		strtotime($clinic['date'].' '.$clinic['end_time']);

							$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					        if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
							$clinic['clocked']				=	"";
					        $clinic['type'] 				= 	'Past';
					        $clinic['status_name'] 			= 	'Past clinic';
					        $clinic['name'] 				= 	$clinic['name'];
					        $clinic['duration'] 			= 	$clinic['estimated_duration'];
					        $clinic['mileage_required'] 	=	isset($mileage)?$mileage:'';
					        $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
					        if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
					      }else{
								if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
									if(!empty($clinic_status)){
										if($clinic_status->clock_in != null){
											$clinic['clocked']		=	0;
										}elseif($clinic_status->clock_in == null){
											$clinic['clocked']		=	1;
										}
										if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
											$clinic['clocked']			=	"";
											$is_past_clinic 			= 	1;
											$clinic['type'] 			= 	'Past';
											$clinic['status_name'] 		= 	'Past clinic';
										}else {
										$clinic['type'] 				= 	'clock_in';
										$clinic['status_name']			= 	'Clock in clinic';
										}
									}
								}else{
									$clinic['clocked']			=	"";
									$clinic['type'] 			= 	'MyClinics';
									$clinic['status_name'] 		= 	'My clinic';
								}
					        $clinic['name'] 				= 	$clinic['name'];
					        $clinic['duration'] 			= 	'';
					        $clinic['mileage_required'] 	=	'';
					        $clinic['drive_time_required'] 	= 	'';
					        if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }

					      }
					      if($is_my_clinic){
							$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
					        $all_clinics[] 	= $clinic;
					      }
					    }
					  }
					}
					else{
						$clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')->where('clinic_status.provider_id',$input_data['user_id'])
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();
					if(!empty($clinics)){
					  foreach($clinics as $clinic){
					  // preptime key for offline clinic starts//
						$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
					  // preptime key for offline clinic ends here //
					    $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					    ->where('provider_id',$input_data['user_id'])
					    ->where('status',1)->count();
					    $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
					    if(!empty($mileage_info)){
					            if($mileage_info[0]['mileage'] != "" && $mileage_info[0]['drive_time'] != ""){
					              $mileage 		= $mileage_info[0]['mileage'].' Miles';
					              $drive_time 	= $mileage_info[0]['drive_time'].' Minutes';
					            }
					    }
					    if($my_clinic_count>0){
					      $is_my_clinic = true;
					    }else{
					      $is_my_clinic = false;
					    }
						$clinic['timezone'] 			= 	$clinic['timezone'];
						$clinic['contact_name'] = 	$clinic['name'];

						$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
						$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
						$user_time_zone_value = $user_time_zone[0]['timezone'];
						$time->setTimezone(new DateTimeZone($user_time_zone_value));
						$clinic_date_time 		= $time->format('Y-m-d H:i:s');
						$clinic_date_only 		= $time->format('Y-m-d');

						$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
						$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
						$prep_time 		= $Preptime->format('H:i');

						$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
						$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
						$clinic_end_time 		= $EndTime->format('H:i');

						$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
							if($user['time_format'] == 24){
								$format = 'H:i';
							}elseif($user['time_format'] == 12){
								$format = 'h:i a';
							}
							$clinic['time']				=	date($format, strtotime($clinic_date_time));
							$clinic['prep_time']		=	date($format, strtotime($prep_time));
							$clinic['end_time']			=	date($format, strtotime($clinic_end_time));

					    $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					    $date 							= 	$clinic['date'];
					    $time 							= 	$clinic['time'];
					    $full_date 						= 	$time.' '.$date;
					    $clinic_timestamp 				= 	strtotime($full_date);
						$current = new DateTime(date('Y-m-d H:i:s'));
						$current->setTimezone(new DateTimeZone($user_time_zone_value));
						$current_timestamp 				= strtotime($current->format('Y-m-d H:i:s'));
						$current_date             		= 	$current->format('Y-m-d');
						$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
						$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
						$clinic_end_timestamp			=		strtotime($clinic['date'].' '.$clinic['end_time']);

						// primary, medtech, other data listing starts //
						$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
						$primary_name = $primary['first_name'].' '.$primary['last_name'];

						$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
						$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

						$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
							foreach($others as $other){
									$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
							}
							// primary, medtech, other data listing ends //
							$clinic['primary_name']			=	isset($primary_name)?$primary_name:array();
							$clinic['medtech_name']			=	isset($medtech_name)?$medtech_name:array();
							$clinic['other_name']			=	isset($other_names)?$other_names:array();
							$other_names 					=	array();

					    $clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					    if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
						  $clinic['clocked']				=	"";
					      $clinic['type'] 					= 	'Past';
					      $clinic['status_name'] 			= 	'Past clinic';
					      $clinic['name'] 					= 	$clinic['name'];
					      $clinic['duration'] 				= 	$clinic['estimated_duration'];
					      $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
					      $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
					      if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
					    }else{
							if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
								if(!empty($clinic_status)){
									if($clinic_status->clock_in != null){
										$clinic['clocked']		=	0;
									}elseif($clinic_status->clock_in == null){
										$clinic['clocked']		=	1;
									}
									if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
										$clinic['clocked']			=	"";
										$is_past_clinic 			= 	1;
										$clinic['type'] 			= 	'Past';
										$clinic['status_name'] 		= 	'Past clinic';
									}else {
									$clinic['type'] 				= 	'clock_in';
									$clinic['status_name']			= 	'Clock in clinic';
									}
								}
							}else{
								$clinic['clocked']			=	"";
								$clinic['type'] 			= 	'MyClinics';
								$clinic['status_name'] 		= 	'My clinic';
							}
					      $clinic['name'] 						= 	$clinic['name'];
					      $clinic['duration'] 					= 	'';
					      $clinic['mileage_required'] 			=		'';
					      $clinic['drive_time_required'] 		= 	'';
					      if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
						  }else{
							 $clinic['mileage_status'] 		=	1;
							 $clinic['drive_time_status'] 		= 	1;
							 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
							 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
						  }
					    }
					   if($is_my_clinic){
						 $clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
					     $all_clinics[] 	= $clinic;
					   }
					}
					}
					}//prd($all_clinics);
					$userimg = WEBSITE_UPLOADS_URL.'users/'.$user['image'];
					if(isset($user['image']) && $user['image'] != '' ){
						$user_image = $userimg;
					}else{
						$user_image = WEBSITE_UPLOADS_URL.'users/man.png';
					}
					if(!empty($all_clinics)){
					return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok.','clinics'=>$all_clinics,'image'=>$user_image)));
					}else{
					return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found for given criteria.')));
					}
					}
				else{
					return $this->encrypt(json_encode(array('deleted'=>'success','message'=>'User is deleted.')));
				}
			}
	}
	 /**
     * function for update clock in time.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function UpdateClockInTime(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 		=> 'required',
		  //'device_id'  	=> 'required',
		  'platform_type' 	=> 'required',
		  'latitude'  		=> 'required',
		  'longitude'  		=> 'required',
		  'clinic_id'  		=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
			$update 	 = ClinicStatusModel::UpdateClockIn($input_data['clinic_id'],$input_data['user_id']);
			// saving user lat long geo location at clock_out time //
				/* $save_record = GeoLocationModel::insert(['user_id' 	=> $input_data['user_id'],
														'clinic_id' => $input_data['clinic_id'],
														'latitude' 	=> $input_data['latitude'],
														'longitude' => $input_data['longitude'],
														'created_at' => date('Y-m-d H:i:s'),
														]); */
			// process starts of saving record if user is not on clinic location //
			$clinic_data = ClinicsModel::where('id',$input_data['clinic_id'])->first();
			$clinic_latitude 	= $clinic_data->latitude;
			$clinic_longitude 	= $clinic_data->longitude;
			$user_latitude   	=  $input_data['latitude'];
			$user_longitude   	=  $input_data['longitude'];
			$drive_time_data	= 	$this->GetDrivingTime($clinic_latitude,$user_latitude,$clinic_longitude,$user_longitude);
				if($drive_time_data > 3){
					$save_record = DB::table('clock_in_away')->insert([
															'device_id'	=>	$input_data['device_id'],
															'user_id'	=>	$input_data['user_id'],
															'clinic_id'	=>	$input_data['clinic_id'],
															'latitude'	=>	$input_data['latitude'],
															'longitude'	=>	$input_data['longitude'],
															]);
				}
			// process end of saving record if user is not on clinic location //
			if($update){
				$time_period = 15000; // milisecond to save user geo location
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Succesfully clocked in.','data'=>$time_period)));
			}else{
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Technical error.')));
			}
		  }
	 }
	 /**
     * function for swipe unfilled clinics from homefeeds
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function SwipeUpcomingClinics(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 			=> 'required',
		  //'device_id'  		=> 'required',
		  'platform_type' => 'required',
		  'latitude'  		=> 'required',
		  'longitude'  		=> 'required',
		  'clinic_id'  		=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
			$update = UpcomingSwipeModel::SwipeClinic($input_data['user_id'],$input_data['clinic_id']);
			if($update){
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Successfully swiped.')));
			}else{
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Technical error.')));
			}
		  }
	 }
	 /**
     * function for swipe other available clinics from homefeeds
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function SwipeOtherClinics(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 			=> 'required',
		  //'device_id'  		=> 'required',
		  'platform_type' => 'required',
		  'latitude'  		=> 'required',
		  'longitude'  		=> 'required',
		  'clinic_id'  		=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
			$update = OthersSwipeModel::SwipeClinic($input_data['user_id'],$input_data['clinic_id']);
			if($update){
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Successfully swiped.')));
			}else{
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Technical error.')));
			}
		  }
	 }
	 /**
     * function for get user data(lat, long).
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function UpdateUserLatLong(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 			=> 'required',
		  //'device_id'  		=> 'required',
		  'platform_type' => 'required',
		  'latitude'  		=> 'required',
		  'longitude'  		=> 'required',
		  'clinic_id'  		=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
				$user_id					=	$input_data['user_id'];
				$clinic_id					=	$input_data['clinic_id'];
				$longitude					=	$input_data['longitude'];
				$latitude					=	$input_data['latitude'];
				$user			=	User_model::GetUserById($user_id);
				if(!empty($user)){
					$check_clinic_clock_out_status = ClinicStatusModel::where('provider_id',$user_id)->where('clinic_id',$clinic_id)->where('status',1)->whereNull('clock_out')->whereNotNull('clock_in')->get()->count();
						if($check_clinic_clock_out_status > 0){
							$update = GeoLocationModel::SaveLatLong($user_id,$clinic_id,$longitude,$latitude);
								if($update){
									return $this->encrypt(json_encode(array('status'=>'success','message'=>'Lat long saved.')));
								}else{
									return $this->encrypt(json_encode(array('status'=>'success','message'=>'Technical error.')));
								}
						}else{
							return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found.')));
						}
				}else{
					return $this->encrypt(json_encode(array('status'=>'error','message'=>'User not found.')));
				}
		  }
	 }
	 /**
     * function for update clock in time.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function UpdateClockOutTime(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 			=> 'required',
		  //'device_id'  		=> 'required',
		  'platform_type' => 'required',
		  'latitude'  		=> 'required',
		  'longitude'  		=> 'required',
		  'clinic_id'  		=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
			$update = ClinicStatusModel::UpdateClockOut($input_data['clinic_id'],$input_data['user_id']);
			if($update){
				// process to insert provider timesheet record starts here//
				$clinic_data = ClinicsModel::where('id',$input_data['clinic_id'])->first();
				$clinic_date_time 		= $clinic_data->date.' '.$clinic_data->time;
				
				$clinics_status_record = ClinicStatusModel::where('clinic_id',$input_data['clinic_id'])->where('provider_id',$input_data['user_id'])->first();

				$user_current_time 		= date('Y-m-d H:i:s');
				$clockin_user_time 		= $clinics_status_record->clock_in;
				
				$user_record = DB::table('users')->select('hourly_rate')->where('id',$input_data['user_id'])->first();
				// saving user lat long geo location at clock_out time //
				$save_record = GeoLocationModel::insert(['user_id' 	=> $input_data['user_id'],
														'clinic_id' => $input_data['clinic_id'],
														'latitude' 	=> $input_data['latitude'],
														'longitude' => $input_data['longitude'],
														'created_at' => date('Y-m-d H:i:s'),
														]);
				
				$count = TimesheetRecords::where('clinic_id',$input_data['clinic_id'])->where('provider_id',$input_data['user_id'])->count();
				if($count == 0){
					$spend_time					= round((strtotime($user_current_time)-strtotime($clockin_user_time))/60);
					$model			 			= new TimesheetRecords;
					$model->clinic_id   		= $input_data['clinic_id'];
					$model->provider_id   		= $input_data['user_id'];
					$model->clinic_date   		= $clinic_date_time;
					$model->clinic_location   	= $clinic_data->location_name;
					$model->clinic_latitude   	= $clinic_data->latitude;
					$model->clinic_longitude   	= $clinic_data->longitude;
					$model->clock_in   			= $clockin_user_time;
					$model->clock_out   		= $user_current_time;
					$model->clinic_spend_time  	= $spend_time;
					$model->mileage   			= null;
					$model->drive_time   		= null;
					$model->hourly_rate   		= $user_record->hourly_rate;
					$model->income   			= ($spend_time/60)*$user_record->hourly_rate;
					$save_model = $model->save();
				}

				// process to insert provider timesheet record starts here//
			
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Successfully clocked out.')));
			}else{
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Technical error.')));
			}
		  }
	 }

	public function AcceptOrRejectClinic(){
			$input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
			$rules = array(
			 'user_id' 			=> 'required',
			 'platform_type' 	=> 'required',
			 'latitude'  		=> 'required',
			 'longitude'  		=> 'required',
			 'clinic_id'  		=> 'required',
			 'status'  			=> 'required',
			 );
			 $validator = Validator::make($input_data,$rules);
			 if ($validator->fails()){
				$messages = $validator->messages();
				return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
			 }else{
				//$input_data['clinic_id'] = 109;
				 //$input_data['user_id'] = 38;
				 //$input_data['status'] = 1; 
				if(isset($input_data['status'])){
					$already_clinic_found 		= 	0;
					 // checking that on same time there is no other clinic is accepted by the same provider
					 $clinic_data 						= 	ClinicsModel::where('id',$input_data['clinic_id'])->first();
					 $provider_type						=	$this->GetProviderType($input_data['user_id'],$input_data['clinic_id']);
					 $to_be_accepted_latitude 			= 	$clinic_data['latitude'];
					 $to_be_accepted_longitude 			= 	$clinic_data['longitude'];
					 $to_be_accept_clinic_time 			= 	date('Y-m-d H:i:s',strtotime($clinic_data->date.' '.$clinic_data->time));
					 $to_be_accept_clinic_end_time 		= 	date('Y-m-d H:i:s',strtotime($clinic_data->date.' '.$clinic_data->end_time));
					 
					 
					 $same_date_clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
															->where('clinic_status.provider_id',$input_data['user_id'])
															->whereDate('clinics.date',$clinic_data->date)
															->where('clinics.status',1)
															->get()->toArray();
															
				if(!empty($same_date_clinics)){
					foreach($same_date_clinics as $clinic){
							 $clinic_latitude			=	$clinic['latitude'];
							 $clinic_longitude			=	$clinic['longitude'];
							 $already_clinic_found 		= 	0;
							 $clinicDate 				= 	$clinic['date'];
							 $clinicTime 				= 	$clinic['time'];
							 $clinicEndTime 			= 	$clinic['end_time'];
							 $clinic_start_time 		= 	date('Y-m-d H:i:s',strtotime($clinicDate.' '.$clinicTime));
							 $clinic_end_time			= 	date('Y-m-d H:i:s',strtotime($clinicDate.' '.$clinicEndTime));
							 $drive_time_data			= 	$this->GetDrivingTime($to_be_accepted_latitude,$clinic_latitude,$to_be_accepted_longitude,$clinic_longitude);
							 
							 $margin_time						= $this->GetAdminSettingsValue('accept_margin_time');
							 /* starts checking for clinic time confilt with forward time clinic. */
							 $drive_time_with_margin_time		= intval($drive_time_data+$margin_time);
							 $estimated_time_addition			= '+'.$drive_time_with_margin_time.' minutes';	
							 $clinic_date_time_with_estimation 	= strtotime($estimated_time_addition,strtotime($clinic_end_time));
							 $clinic_final_end_time				= date('Y-m-d H:i:s',$clinic_date_time_with_estimation);
							 /* ends checking for clinic time confilt with forward time clinic. */
							 
							 /* starts checking for clinic time confilt with backward time clinic. */
							 $reverce_drive_time_data	= $this->GetDrivingTime($clinic_latitude,$to_be_accepted_latitude,$clinic_longitude,$to_be_accepted_longitude);
							 $total_time_subtraction		= 	intval($reverce_drive_time_data+$margin_time);
							 $estimated_time_substraction	=	'-'.$total_time_subtraction.' minutes';
							 $clinic_sub_date_time_with_estimate = strtotime($estimated_time_substraction,strtotime($clinic_start_time));
							 $clinic_final_reverse_time 	= date('Y-m-d H:i:s',$clinic_sub_date_time_with_estimate);
							 /* ends checking for clinic time confilt with backward time clinic. */
							 
							 if($to_be_accept_clinic_time>$clinic_start_time){
								 $after = 1;
							 }else{
								 $after = 0;
							 }
							 if($to_be_accept_clinic_time<$clinic_final_end_time && $after == 1){
								 $already_clinic_found = 1;
								 $reject = ClinicStatusModel::AcceptRejectClinic($input_data['user_id'],$input_data['clinic_id'],0,$provider_type);
								 break;
							 }elseif($to_be_accept_clinic_end_time>$clinic_final_reverse_time && $after == 0){
								 $already_clinic_found = 1;
								 $reject = ClinicStatusModel::AcceptRejectClinic($input_data['user_id'],$input_data['clinic_id'],0,$provider_type);
								 break;
							 } 
					}
				}
				 if($already_clinic_found == 1 && $input_data['status'] == 1){
					 return $this->encrypt(json_encode(array('status'=>'error','message'=>'The clinic is in conflict with scheduled clinic')));
				 }else{
					 if($input_data['status'] == 1){
						 $update = ClinicStatusModel::AcceptRejectClinic($input_data['user_id'],$input_data['clinic_id'],1,$provider_type);
						 // if provider has 1 personnel than asign rules autumatically starts here //
						 $total_personnel = $clinic_data->personnel;
						 if($total_personnel == 1){
							 $check_rules = DB::table('rules')->where('clinic_id', '=', $input_data['clinic_id'])->first();
							  if($check_rules == null ){
								DB::table('rules')->insert(
													['clinic_id' => $input_data['clinic_id'], 'provider_id' => $input_data['user_id'], 'type' => 'primary', 'status' => 'Sent','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')]
												);
								}else{
									DB::table('rules')->where('clinic_id',$input_data['clinic_id'])->update(
														['provider_id' => $input_data['user_id'],'created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s')]
													);
								}
						 }
						 // if provider has 1 personnel than asign rules autumatically ends here //
							
						 // process to start send email to provider
							$user_data    			= 	DB::table('users')->where('id',$input_data['user_id'])->first();
							if(!empty($user_data)){
								if($user_data->email_notification == 1 && $user_data->disable_email_confirmation == 0 ){
									$clinic_data    =  DB::table('clinics')->where('id',$input_data['clinic_id'])->first();
									$clinic_location  = $clinic_data->location_name;
									$user_time_zone_value 	= 	$user_data->timezone;
									$clinic_date_time		= 	new DateTime($clinic_data->date.' '.$clinic_data->time, new DateTimeZone('GMT'));
									$clinic_date_time->setTimezone(new DateTimeZone($user_time_zone_value));
									$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
									$date         			= 	$clinic_date_time->format('Y-m-d');
									$time         			= 	$clinic_date_time->format('H:i');
									$user_name     			=   $user_data->first_name ." ". $user_data->last_name;
									$subject_replace		=	array($user_name);
									$replace_array 			=   array($user_name,$date,$time,$clinic_location);
									$email_send    			=   $this->mail_send('clinic_confirm',$user_data->email,$user_name,$subject_replace,$replace_array);
								}
							}
							
							//email send to admins
							
							$all_admins   = DB::table('users')->where('role_id',1)->get();
							foreach($all_admins as $admin){		
								$clinic_location  		= 	$clinic_data->location_name;
								$admin_time_zone_value 	= 	$admin->timezone;
								$clinic_date_time		= 	new DateTime($clinic_data->date.' '.$clinic_data->time, new DateTimeZone('GMT'));
								$clinic_date_time->setTimezone(new DateTimeZone($admin_time_zone_value));
								
								$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
								$date         			= 	$clinic_date_time->format('Y-m-d');
								$time         			= 	$clinic_date_time->format('H:i');
								$user_name     			=   $admin->first_name ." ". $admin->last_name;
								$subject_replace		=	array($user_name);
								$replace_array 			=   array($user_name,$date,$time,$clinic_location);
								if($admin->email_notification == 1){
									$this->mail_send('clinic_confirm',$admin->email,$user_name,$subject_replace,$replace_array);
								}
							}
							
							
						 // mail send ends
						 return $this->encrypt(json_encode(array('status'=>'success','message'=>'Clinic accepted')));
					 }elseif($input_data['status'] == 0){
						 $update = ClinicStatusModel::AcceptRejectClinic($input_data['user_id'],$input_data['clinic_id'],0,$provider_type);
						 return $this->encrypt(json_encode(array('status'=>'success','message'=>'Clinic rejected')));
					 }else{
						 return $this->encrypt(json_encode(array('status'=>'error','message'=>'Technical error.')));
					 }
				 }
			 }
		 }
	}
	/**
     * function for get provider type.
     *
     * @param user id,clinic id
     *
     * @return response data on success otherwise error.
     */
	public function GetProviderType($user_id,$clinic_id){
		$clinic = ClinicsModel::where('id',$clinic_id)->first();
		if($clinic->primary_provider == $user_id){
			return 'primary';
		}elseif($clinic->medtech_provider == $user_id){
			return 'medtech';
		}elseif($clinic->other_provider != null){
			$others = explode(',',$clinic->other_provider);
			if (in_array($user_id, $others)){
				return 'other';
			}else{
				return 'unfilled';
			}
		}elseif($clinic->provider_id != null){
			$preffred = explode(',',$clinic->provider_id);
			if (in_array($user_id, $preffred)){
				return 'preffred';
			}else{
				return 'unfilled';
			}

		}else{
			return 'unfilled';
		}
	}
	 /**
     * function for show homefeed information.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function HomeFeedsInformation(){
            
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
                
		 $rules = array(
		   'user_id' 		=> 'required',
		  //'device_id' 	=> 'required',
		  'platform_type' 	=> 'required',
		  'latitude'  		=> 'required',
		  'longitude'  		=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
				/* $input_data['user_id'] = '38';	
				$input_data['device_id'] = 'dsdsvsdvsvsdvdsvds'; */		
				$clinic_message	=	'';
				$user_id 		= 	$input_data['user_id'];
				$user			=	User_model::GetUserById($user_id);
				$distance  		= 	$this->GetAdminSettingsValue('max_distance');
				if(!empty($user)){
					$user_device_id         =       $input_data['device_id'];
					if(isset($user_device_id) && $user_device_id != null){
						$update_device_id		=       $this->UpdateDeviceId($user_device_id,$user_id);
					}
					$user_city              =       $user['city_name'];
					$latest_announcement 	=		AnnouncementModel::GetLatest($user_id,$user_city);
					//prd($latest_announcement);
					$sqlQuery = "SELECT *,
													( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) AS distance
														FROM clinics WHERE `date` >= curdate() AND
														(
														FIND_IN_SET(".$input_data['user_id'].", `provider_id`)  OR  ( 6371 * acos( cos( radians(".$user['latitude'].") ) * cos( radians( `latitude` ) ) * cos( radians( `longitude` ) - radians(".$user['longitude'].") ) + sin( radians(".$user['latitude'].") ) * sin( radians( `latitude` ) ) ) ) <= $distance )
														ORDER BY distance ASC";			
															
														
					$clinics_s 		= 	DB::select(DB::raw($sqlQuery));
					$clinics   		= 	(array) $clinics_s;
					//prd($clinics);
					$clinics_data	=	array();
			if(!empty($clinics)){
				// set clinic message when clinic found in given location
				foreach($clinics as $clinic){
					$clinic  			= 	(array)$clinic;
					$expired			=	false;
					$not_available		=	0;
					$clock_out 			= 	false;
				// checking clinic already accepted by someone or not.
					
					$check_desclined_status 		= DB::table('unfilled_rejected_clinics')->where('clinic_id',$clinic['id'])->where('provider_id',$input_data['user_id'])->where('decline',1)->get()->count();
					$check_desclined_status_others 	= DB::table('available_rejected_clinics')->where('clinic_id',$clinic['id'])->where('provider_id',$input_data['user_id'])->where('decline',1)->get()->count();
					
					if($check_desclined_status_others <= 0){
						// primary, medtech, other data listing starts //
						$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['id'])->where('rules.type','primary')->get()->first();
						$primary_name = $primary['first_name'].' '.$primary['last_name'];

						$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['id'])->where('rules.type','medtech')->get()->first();
						$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

						$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['id'])->where('rules.type','others')->get()->toArray();
						foreach($others as $other){
								$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
						}

						// primary, medtech, other data listing ends //
						$clinic['primary_name']			=	isset($primary_name)?$primary_name:array();
						$clinic['medtech_name']			=	isset($medtech_name)?$medtech_name:array();
						$clinic['other_name']			=	isset($other_names)?$other_names:array();
						$other_names					=	array();

					//show time according to clinic time and user time starts here //
					$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
					$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
					$user_time_zone_value 		= $user_time_zone[0]['timezone'];
					$time->setTimezone(new DateTimeZone($user_time_zone_value));
					$clinic_date_time 			= 	$time->format('Y-m-d H:i:s');



					$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
					$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
					$prep_time 				= 	$Preptime->format('H:i');

					$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
					$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
					$clinic_end_time 		= 	$EndTime->format('H:i');

					$current = new DateTime(date('Y-m-d H:i:s'));
					$current->setTimezone(new DateTimeZone($user_time_zone_value));
					$current_timestamp 		= 	strtotime($current->format('Y-m-d H:i:s'));
					$current_date			= 	$current->format('Y-m-d');
					

					$clinic['date']			=	date('d-m-Y', strtotime($clinic_date_time));
						if($user['time_format'] == 24){
							$format = 'H:i';
						}elseif($user['time_format'] == 12){
							$format = 'h:i a';
						}
						$clinic['time']				=	date($format, strtotime($clinic_date_time));
						$clinic['prep_time']		=	date($format, strtotime($prep_time));
						$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
						// show time according to clinic time and user time ends here //

					$clinic['timestamp']				=	strtotime($clinic['date'].' '.$clinic['time']);
					$clinic['estimated_duration']		=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					$availablity 						= 	ClinicStatusModel::CheckClinicAvailablity($clinic['id'],$input_data['user_id']);
					
					$check_rejected 					= 	ClinicStatusModel::CheckClinicRejected($clinic['id']);
						 
					$clocked_out						=	"";
					$expired_clinic						=	"";
					$setting_time 						=	$clinic['date'].' '.$prep_time;
					$allow_clockin_before_preptime		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
					$date 								= 	$clinic['date'];
					$time 								= 	$clinic['time'];

					$full_date 							= 	$time.' '.$date;
					$upcoming_limit 					=	ClinicStatusModel::Upcomig_clinic_limit($clinic['id'],$date,10);
					$clinic_timestamp 					= 	strtotime($full_date);
					$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
					$before_timestamp 					=	(strtotime($clinic['date'].' '.$prep_time))-$allow_clockin_before_time*60;
					$clinic_end_timestamp				=	strtotime($clinic['date'].' '.$clinic['end_time']);
					
					$count 	= 	ClinicStatusModel::
											where('clinic_id',$clinic['id'])
											->where('status',1)->count();
					
					if($clinic['default_unfilled_time'] !=  '' || $clinic['default_unfilled_time'] != null){
						$unfilled_clinic_time  = $clinic['default_unfilled_time'];
					}else{
						$unfilled_clinic_time  = $this->GetAdminSettingsValue('default_time_stay_in_feeds');
					}
					$unfilled_time = '+'.$unfilled_clinic_time.' hour';
					
					$clinic_date_time_with_unfilled_time = 	strtotime($unfilled_time,strtotime($clinic['created_at']));
					$Current_Time_GMT					 =   time();
					
					if($availablity == 1 && $Current_Time_GMT < $clinic_date_time_with_unfilled_time){
						
							if($current_timestamp>$clinic_timestamp || $check_rejected <= 0){
								// clinic is expired do not show it.
								$not_available = 1;
							}else{
								if(in_array($input_data['user_id'],explode(',',$clinic['provider_id']))){
									$clinic['type'] 			= 	'preferd';
									$clinic['status_name']		= 	'Preferred available Clinics';
									$clinic['sorting_key'] 		= 	3;
								}else{
									$clinic['type'] 			= 	'available_clinics';
									$clinic['status_name']		= 	'Available clinics';
									$clinic['sorting_key'] 		= 	4;
								}
								
							}
					}else{
						   $clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['id'],$input_data['user_id']);
						if(!empty($clinic_status)){
							if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
								$clinic['type'] 			= 	'clock_in';
								$clinic['status_name']		= 	'Clock in clinic';
								$clinic['sorting_key'] 		= 	1;
							}else if($current_timestamp<$clinic_timestamp){
									if($user['prep_time'] != null || $user['prep_time'] != ''){
										$get_user_preptime = '-'.$user['prep_time'].' minutes';
									}else{
										$get_user_preptime = '-'.$this->GetAdminSettingsValue('default_prep_time').' minutes';
									}
									$clinic_date_time_with_preptime	 = 	strtotime($get_user_preptime,strtotime($full_date));
									if($current_timestamp < $clinic_date_time_with_preptime || $check_desclined_status > 0){
										$not_available = 1;
									}else{
										$clinic['type'] 			= 	'upcoming';
										$clinic['status_name']		= 	'Upcoming clinic';
										$clinic['sorting_key'] 		= 	2;
									}
							}
							else if($current_timestamp>$clinic_timestamp){
								// clinic is expired do not show it.
								$not_available = 1;
							}
							if($clinic_status->clock_out == null){
								$clock_out = false;
							}else{
								// clinic is completed with clock in and clock out process do not show it.
								$not_available = 1;
							}
							if($clinic_status->clock_in != null){
								$clinic['clocked']		=	0;
							}elseif($clinic_status->clock_in == null){
								$clinic['clocked']		=	1;
							}
						}else{
							$not_available = 1;
						}
					}
					if($not_available == 0){
						$config_date 		= Config::get('date_format.date');
						$config_month 		= Config::get('date_format.month');
						$config_year 		= Config::get('date_format.year');
						$config_separator 	= Config::get('date_format.separator');
						$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
						
						if($clinic['type'] == 'preferd' || $clinic['type'] == 'available_clinics'){
							if($count<$clinic['personnel']){
								$clinics_data[] 	= 	$clinic;
							}
						}else{
							$clinics_data[] 	= 	$clinic;	
						}
					}
					$not_available = 0;
				}
		  }
		}else{
			// message when clinics not found in user's location.
			$clinic_message = 'Clinics not found in your location';
		}
			if(!empty($latest_announcement)){
				$sorting = 5;
				foreach($latest_announcement as $latest){
					if($latest['stable_time']== null){
						$default_stay_time			= $this->GetAdminSettingsValue('default_announcemnet_stay_feeds');
						$stay_time					= ($default_stay_time*24);
					}else{
						$stay_time = $latest['stable_time'];
					}
					$estimated_time_addition		=	'+'.$stay_time.' hour';
					$announcement_time_after_add 	= strtotime($estimated_time_addition,strtotime($latest['created_at']));
					$announcement_end_time			= date('Y-m-d H:i:s',$announcement_time_after_add);
					$current_time 					= date('Y-m-d H:i:s');
					if($current_time <= $announcement_end_time){
						$check_declined_status	=		AnnouncementStatusModel::where('provider_id',$input_data['user_id'])->where('announcement_id',$latest['id'])->where('decline',1)->get()->count();
						if($check_declined_status <= 0){
							if(!empty($latest['image'])){
								$image_path = WEBSITE_UPLOADS_URL.'announcement/'.$latest['image'];
							}else{
								$image_path = "";
							}
							$config_date 		= Config::get('date_format.date');
							$config_month 		= Config::get('date_format.month');
							$config_year 		= Config::get('date_format.year');
							$config_separator 	= Config::get('date_format.separator');
							$latest['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($latest['created_at']));
							$latest['image_path'] 		= 	$image_path;
							$latest['description'] 		= 	substr($latest['description'],0,50);
							$latest['timestamp'] 		= 	strtotime($latest['created_at']);
							$latest['type'] 			= 	'announcement';
							$latest['sorting_key'] 		= 	$sorting;
							$latest_announc[] 			= 	$latest;
							$latest_announce = array_slice($latest_announc, 0, 3);
						}
					}
					$sorting++;
				}
			}
			
			if(!empty($latest_announce)){
					$home_feeds = array_merge($clinics_data,$latest_announce);
				}else{
					$home_feeds = $clinics_data;
				}
				$sorting_key = array();
				foreach ($home_feeds as $home_feed) {
				   $sorting_key[]    = $home_feed['sorting_key'];
				}
				/* $timestamps = array();
				foreach ($home_feeds as $home_feed) {
				   $timestamps[]    = $home_feed['timestamp'];
				} */
			if(!empty($home_feeds)){
				$time_format  = $user['time_format'];
				$userimg = WEBSITE_UPLOADS_URL.'users/'.$user['image'];
				if(isset($user['image']) && $user['image'] != '' ){
					$user_image = $userimg;
				}else{
					$user_image = WEBSITE_UPLOADS_URL.'users/man.png';
				}
				array_multisort($sorting_key, SORT_ASC, $home_feeds);
				//array_multisort($sorting_key,SORT_ASC,$home_feeds);
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok','home_feeds'=>$home_feeds,'image'=>$user_image,'additional_message'=>$clinic_message,'time_format'=>$time_format)));
			}
			return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found')));
		}
		else{
			return $this->encrypt(json_encode(array('status'=>'deleted','message'=>'User is deleted.')));
		}
	}
	}
	/**
     * function for get single provider mileage data.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function Get_Provider_Mileage(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 			=> 'required',
		  //'device_id'  		=> 'required',
		  'platform_type' 		=> 'required',
		  'mileage_required'  	=> 'required',
		  'drive_time_required' => 'required',
		  'clinic_id'  			=> 'required', 
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
			  //$update = 1;
			  $update = ClinicStatusModel::UpdateMileage($input_data['user_id'],$input_data['clinic_id'],$this->RemoveAlphabets($input_data['mileage_required']),$this->RemoveAlphabets($input_data['drive_time_required']));
			if($update){
				// process to save provider mileage, drive time timesheet record starts here//
					$timesheet_data = TimesheetRecords::where('clinic_id',$input_data['clinic_id'])->where('provider_id',$input_data['user_id'])->first();
					$clinic_spend_time 	= $timesheet_data->clinic_spend_time;
					$hourly_rate		= $timesheet_data->hourly_rate;
					$mileage 			= $this->RemoveAlphabets($input_data['mileage_required']);
					$drivetime 			= $this->RemoveAlphabets($input_data['drive_time_required']);
					
					$status = TimesheetRecords::where('clinic_id',$input_data['clinic_id'])->where('provider_id',$input_data['user_id'])->update(['mileage'=>$mileage,'drive_time'=>$drivetime]);

				// process to save provider mileage, drive time timesheet record starts here//
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Mileage & Drive time succesfully updated.')));
			}else{
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'Technical error.')));
			}
		  }
	 }
	public function UpdateDeviceId($device_id,$user_id){
		 $count = DB::table('api_tokens')->where('user_id',$user_id)->count();
		 if($count>0){
			 $update 	= DB::table('api_tokens')->where('user_id',$user_id)->update(array('device_id'=>$device_id));
		 }else{
			 // Creates token
			$faker 		= Faker::create();
			$auth_token 	= $faker->uuid();
			$model 	= new ApiTokens;
			$model->device_id     	=	$device_id;
			$model->user_id     	=	$user_id;
			$model->auth_token     	=	$auth_token;
			$model->save();

		 }
		 
	 }
	 /**
     * function for clockout push notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function ClockOutNotification(){
				$user_clinics = ClinicStatusModel::whereNull('clock_out')->whereNotNull('clock_in')->get()->toArray();
				if($user_clinics){
					foreach ($user_clinics as $user_clinic) {
						$provider_id						= $user_clinic['provider_id'];
						$provider_data 						= ApiTokens::where('user_id',$user_clinic['provider_id'])->first()->toArray();
						$provider_device_id					= 	$provider_data['device_id'];
						$clinic_data 						= 	ClinicsModel::where('id',$user_clinic['clinic_id'])->first()->toArray();
						$clinic_estimate_time    			= 	$clinic_data['estimated_duration'];
						$clock_in_time 						= 	$user_clinic['clock_in'];
						$clinic_end_time					= 	date('Y-m-d H:i',strtotime($clinic_data['end_time']));
						$default_time_remind_clockout   	= 	'+'.$this->GetAdminSettingsValue('default_time_clockout').' minutes';
						$clinic_date_time_with_final_estimate = 	strtotime($default_time_remind_clockout,strtotime($clinic_end_time));
						$clinic_final_end_time								= 	date('Y-m-d H:i',$clinic_date_time_with_final_estimate);
						$current_time													= 	strtotime(date('Y-m-d H:i'));
						if($user_clinic['clock_out'] == null && $user_clinic['clock_in'] != null && $current_time >=  $clinic_final_end_time ){
							$notification_message = 	 [	"notification" => [
																	          	"body" 	=> "Please clock out the clinic!",
																	          	"title" => "Clock out notification"
																	        ],
																					"data" => [
																	            "clinic_id" => $user_clinic['clinic_id'],
																	        ],
																	    ];
							$PushNotification =   $this->PushNotification($provider_device_id,$notification_message);
							if($PushNotification == 1){
									return $this->encrypt(json_encode(array('status'=>'success','message'=>'Ok.','data'=>$notification_message)));
							}
							else{
								return $this->encrypt(json_encode(array('status'=>'success','message'=>'Technical error.')));
							}
						}else{
							return false;
						}

					}
				}
	 }

	public function clinic_confirm($tookan=null){
        $clinic_data = ClinicsModel::where('tookan',$tookan)->first();
        if(!empty($clinic_data)){
            $type = $this->decrypt(Input::get('type'));
            if($type == 'accept'){
                $model = ClinicsModel::find($clinic_data->id);
                $model->tookan = null;
                $model->status = 1;
                $model->save();

                $status_model = new ClinicsStatusModel();
                $status_model->clinic_id = $clinic_data->id;
                $status_model->provider_id = $clinic_data->provider_id;
                $status_model->status = 1;
                $status_model->save();
            }elseif($type == 'reject'){
                $model = ClinicsModel::find($clinic_data->id);
                $model->tookan = null;
                $model->status = 0;
                $model->save();

                $status_model = new ClinicsStatusModel();
                $status_model->clinic_id = $clinic_data->id;
                $status_model->provider_id = $clinic_data->provider_id;
                $status_model->status = 1;
                $status_model->save();
            }else{
              Toast::error(trans('You are using wrong link!'));
              Redirect::route('/');
            }
        }else{
          Toast::error(trans('You are using wrong link!'));
          Redirect::route('/');
        }
      }
	  
	   /**
     * function for update clock in time.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function SyncClinics(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 /* $input_data = '[{"user_id":"38","clinic_id":"92","Type":"clock_in","Time_clockout":"","Time_clockin":1507028158,"mileage":"","drive_time":""}, {"user_id":"38","clinic_id":"92","Type":"clock_out","Time_clockout":1507028177,"Time_clockin":"","mileage":"1","drive_time":"2"}]';
		 $input_data = json_decode($input_data);
		 //prd($json); */
		 if(isset($input_data)){
			foreach($input_data as $data){
				$user_record = DB::table('users')->select('hourly_rate')->where('id',$data->user_id)->first();
				$hourly_rate = $user_record->hourly_rate;
				if($data->Type=='clock_in'){
						$clinic_id 				= $data->clinic_id;
						$user_id 				= $data->user_id;
						$Time_clockin 			= $data->Time_clockin;	
						$clock_in_time 	= date('Y-m-d H:i:s',$Time_clockin);
						//prd($clinic_id);
						$update 	 	= ClinicStatusModel::UpdateSyncClockIn($user_id,$clinic_id,$clock_in_time);
						//return $this->encrypt(json_encode(array('status'=>'success','message'=>'Successfully clocked in.')));  
					}
				if($data->Type=='clock_out'){
					$clinic_id 				= $data->clinic_id;
					$user_id 				= $data->user_id;
					$Time_clockout 			= $data->Time_clockout;
					$Time_clockin 			= $data->Time_clockin;
					$mileage 				= $data->mileage;
					$drive_time 			= $data->drive_time;
					
					$clinic_status_data 	= ClinicStatusModel::where('clinic_id',$clinic_id)->where('provider_id',$user_id)->first();	
					$clock_out_time 	= date('Y-m-d H:i:s',$Time_clockout);	
					$clinic_spend_time = round((strtotime($clock_out_time)-strtotime($clinic_status_data->clock_in))/60);
					if($mileage == "" && $drive_time == ""){
						$mileage_user 		= 0;
						$income_user 		= 0;
						$drive_time_user 	= 0;
					}else{
						$mileage_user 		= $mileage;
						$drive_time_user 	= $drive_time;
						$income_user 		= number_format((($clinic_spend_time)/60)*$hourly_rate,2);
					}
					
					$update 	 	= ClinicStatusModel::UpdateSyncClockout($user_id,$clinic_id,$clock_out_time,$mileage_user,$drive_time_user,$clinic_spend_time);
					  if($update){
						  // process to insert provider timesheet record starts here//
							$clinic_data = ClinicsModel::where('id',$clinic_id)->first();
							if($clinic_data){
								$clinic_date_time 		= $clinic_data->date.' '.$clinic_data->time;
								
								$clinics_status_record = ClinicStatusModel::where('clinic_id',$clinic_id)->where('provider_id',$user_id)->first();
								
								$clockin_user_time 		= $clinics_status_record->clock_in;
								$clockout_user_time 	= $clock_out_time;
								
								
								$count = TimesheetRecords::where('clinic_id',$clinic_id)->where('provider_id',$user_id)->count();
									if($count == 0){
										$clinic_spend_time			= round((strtotime($clockout_user_time)-strtotime($clockin_user_time))/60);
										if($mileage == "" && $drive_time == ""){
											$mileage_user 		= 0;
											$income_user 		= 0;
											$drive_time_user 	= 0;
										}else{
											$mileage_user 		= $mileage;
											$drive_time_user 	= $drive_time;
											$income_user 		= number_format((($clinic_spend_time)/60)*$hourly_rate,2);
										}
										$hourly_rate 				= $user_record->hourly_rate;
										$model			 			= new TimesheetRecords;
										$model->clinic_id   		= $clinic_id;
										$model->provider_id   		= $user_id;
										$model->clinic_date   		= $clinic_date_time;
										$model->clinic_location   	= $clinic_data->location_name;
										$model->clinic_latitude   	= $clinic_data->latitude;
										$model->clinic_longitude   	= $clinic_data->longitude;
										$model->clock_in   			= $clockin_user_time;
										$model->clock_out   		= $clockout_user_time;
										$model->clinic_spend_time  	= $clinic_spend_time;
										$model->mileage   			= $mileage_user;
										$model->drive_time   		= $drive_time_user;
										$model->hourly_rate   		= $hourly_rate;
										$model->income   			= $income_user;
										$save_model = $model->save();
									}

							// process to insert provider timesheet record end here//
							}
						}
						//return $this->encrypt(json_encode(array('status'=>'success','message'=>'Successfully clocked out.'))); 
				}
				
			  }
			return $this->encrypt(json_encode(array('status'=>'success','message'=>'Success.'))); 
		 }
	 }
	 /**
     * function for get user clinics for offline.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	public function MyClinicsOffline(){
		$input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		$rules = array(
		'user_id' 			 => 'required',
		'platform_type'  	 => 'required',
		/* 'latitude'  		 => 'required',
		'longitude'  		 => 'required', */
		);
		$validator = Validator::make($input_data,$rules);
		if ($validator->fails()){
			$messages = $validator->messages();
			return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		}else{
				$config_date 		= Config::get('date_format.date');
				$config_month 		= Config::get('date_format.month');
				$config_year 		= Config::get('date_format.year');
				$config_separator 	= Config::get('date_format.separator');
				$user_id 		= 	$input_data['user_id']; 
				$user			=	User_model::GetUserById($user_id);
				if(!empty($user)){
					$all_clinics = array();
					$is_past_clinic  = 0;
					// process for upcoming clinics starts here //
						$clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')->where('clinic_status.provider_id',$input_data['user_id'])
					      ->where('clinic_status.status',1)->orderBy('clinics.date','Desc')->get()->toArray();
					      if(!empty($clinics)){
					        foreach($clinics as $clinic){
							   // preptime key for offline clinic starts//
								$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
							   // preptime key for offline clinic ends here //
					          $is_past_clinic  = 0;
					          $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					          ->where('provider_id',$input_data['user_id'])
					          ->where('status',1)->count();
					          $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
							 // prd($mileage_info);
					          if(!empty($mileage_info)){
					            if($mileage_info[0]['mileage'] != "" && $mileage_info[0]['drive_time'] != ""){
					            $mileage 		= $mileage_info[0]['mileage'].' Miles';
					            $drive_time 	= $mileage_info[0]['drive_time'].' Minutes';
					            }
					          }
					          if($my_clinic_count>0){
					            $is_my_clinic = true;
					          }else{
					            $is_my_clinic = false;
					          }
										$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
										$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
										$user_time_zone_value = $user_time_zone[0]['timezone'];
										$time->setTimezone(new DateTimeZone($user_time_zone_value));
										$clinic_date_time 		= $time->format('Y-m-d H:i:s');
										$clinic_date_only 		= $time->format('Y-m-d');


										$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
										$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
										$prep_time 		= $Preptime->format('H:i');

										$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
										$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
										$clinic_end_time 		= $EndTime->format('H:i');

										$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
											if($user['time_format'] == 24){
												$format = 'H:i';
											}elseif($user['time_format'] == 12){
												$format = 'h:i a';
											}
											$clinic['time']				=	date($format, strtotime($clinic_date_time));
											$clinic['prep_time']		=	date($format, strtotime($prep_time));
											$clinic['end_time']			=	date($format, strtotime($clinic_end_time));

										$clinic['contact_name'] = 	$clinic['name'];
										$clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
										$date 					= 	$clinic['date'];
										$time 					= 	$clinic['time'];
										$full_date 				= 	$time.' '.$date;
										$clinic_timestamp 		= 	strtotime($full_date);
										//$current_timestamp 		=	time();
										$current = new DateTime(date('Y-m-d H:i:s'));
										$current->setTimezone(new DateTimeZone($user_time_zone_value));
										$current_timestamp 		= strtotime($current->format('Y-m-d H:i:s'));
										$current_date           = 	$current->format('Y-m-d');
										$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
										$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
										$clinic_end_timestamp	=		strtotime($clinic['date'].' '.$clinic['end_time']);

										// primary, medtech, other data listing starts //
										$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
										$primary_name = $primary['first_name'].' '.$primary['last_name'];

										$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
										$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

										$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
										foreach($others as $other){
												$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
										}
										// primary, medtech, other data listing ends //
										$clinic['primary_name']		=	isset($primary_name)?$primary_name:array();
										$clinic['medtech_name']		=	isset($medtech_name)?$medtech_name:array();
										$clinic['other_name']		=	isset($other_names)?$other_names:array();
										$other_names				=	array();
							
							$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					          if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
								$clinic['clocked']					=	"";
					            $is_past_clinic 					= 	1;
					            $clinic['type'] 					= 	'Past';
					            $clinic['status_name'] 				= 	'Past clinic';
					            $clinic['clinic_name'] 				= 	$clinic['name'];
					            $clinic['name'] 					= 	$clinic['name'];
					            $clinic['duration'] 				= 	$clinic['estimated_duration'];
					            $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
					            $clinic['drive_time_required'] 		= 	isset($drive_time)?$drive_time:'';
					            if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
								  }else{
									 $clinic['mileage_status'] 		=	1;
									 $clinic['drive_time_status'] 		= 	1;
									 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
									 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
								  }
					          }else{
								  if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
											if(!empty($clinic_status)){
												if($clinic_status->clock_in != null){
													$clinic['clocked']		=	0;
												}elseif($clinic_status->clock_in == null){
													$clinic['clocked']		=	1;
												}
												if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
													$clinic['clocked']			=	"";
													$is_past_clinic 			= 	1;
													$clinic['type'] 			= 	'Past';
													$clinic['status_name'] 		= 	'Past clinic';
												}else {
												$clinic['type'] 				= 	'clock_in';
												$clinic['status_name']			= 	'Clock in clinic';
												}
											}
										}else{
											$clinic['clocked']			=	"";
											$clinic['type'] 			= 	'MyClinics';
								$clinic['status_name'] 	= 	'My clinic';
										}
					            $clinic['name'] 				= 	$clinic['name'];
					            $clinic['duration'] 			= 	'';
					            $clinic['mileage_required'] 	=	"";
					            $clinic['drive_time_required'] 	= 	"";
					            if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
								  }else{
									 $clinic['mileage_status'] 		=	1;
									 $clinic['drive_time_status'] 		= 	1;
									 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
									 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
								  }
					          }
					         if($is_my_clinic && $is_past_clinic != 1){
								$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
					           $all_clinics['upcoming'][] 	= $clinic;
					         }
					      }
					    }
					// process for upcming clinics ends here //
					// process for month clinics starts here //
					    $month_number 				= 	date('m');
					    $month_start_date 			= 	$this->firstDay();
					    $month_end_date 			= 	$this->lastDay();
						
					    $start_date_timestamp 		= 	strtotime($month_start_date);
					    $first_date_month 			= 	strtotime("-7 day", $start_date_timestamp);
					    $my_query_start_date		= 	date('Y-m-d', $first_date_month);
					    $end_date_timestamp 		= 	strtotime($month_end_date);
					    $last_date_month 			= 	strtotime("+7 day", $end_date_timestamp);
					    $my_query_end_date			= 	date('Y-m-d', $last_date_month);
						//prd($my_query_start_date);
						$clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')->where('clinic_status.provider_id',$input_data['user_id'])
							->whereBetween('clinics.date',[$my_query_start_date,$my_query_end_date])
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();
							
					if(!empty($clinics)){
					  foreach($clinics as $clinic){
					  // preptime key for offline clinic starts//
						$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
					  // preptime key for offline clinic ends here //
					    $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					    ->where('provider_id',$input_data['user_id'])
					    ->where('status',1)->count();
					    $clinic_status 		=  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->where('provider_id',$input_data['user_id'])->get()->toArray();
					    if(!empty($clinic_status)){
					      if($clinic_status[0]['mileage'] != "" && $clinic_status[0]['drive_time'] != ""){
					        $mileage 			= $clinic_status[0]['mileage'].' Miles';
					        $drive_time 		= $clinic_status[0]['drive_time'].' Minutes';
					      }
					    }
					    if($my_clinic_count>0){
					      $is_my_clinic = true;
					    }else{
					      $is_my_clinic = false;
					    }
							// primary, medtech, other data listing starts //
							$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
							$primary_name = $primary['first_name'].' '.$primary['last_name'];

							$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
							$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

							$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
							foreach($others as $other){
									$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
							}
					      // primary, medtech, other data listing ends //
					    $clinic['primary_name']			=	isset($primary_name)?$primary_name:array();
					    $clinic['medtech_name']			=	isset($medtech_name)?$medtech_name:array();
					    $clinic['other_name']			=	isset($other_names)?$other_names:array();

							$clinic['contact_name'] 	= 	$clinic['name'];
					    $other_names 					=	array();
					    $userdata 						=	User_model::GetUserById($input_data['user_id']);
					    $clinic['system_calender']		=	$userdata['system_calender'];

							$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
							$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
							$user_time_zone_value = $user_time_zone[0]['timezone'];
							$time->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_date_time 		= $time->format('Y-m-d H:i:s');
							$clinic_date_only 		= $time->format('Y-m-d');

							$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
							$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
							$prep_time 		= $Preptime->format('H:i');

							$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
							$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_end_time 		= $EndTime->format('H:i');

							$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
								if($user['time_format'] == 24){
									$format = 'H:i';
								}elseif($user['time_format'] == 12){
									$format = 'h:i a';
								}
								$clinic['time']			=	date($format, strtotime($clinic_date_time));
								$clinic['prep_time']	=	date($format, strtotime($prep_time));
								$clinic['end_time']		=	date($format, strtotime($clinic_end_time));
					    $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					    $date 							= 	$clinic['date'];
					    $time 							= 	$clinic['time'];
					    $full_date 					= 	$date.' '.$time;
					    $clinic_timestamp 	= 	strtotime(date('Y-m-d H:i',strtotime($full_date)));

							$current = new DateTime(date('Y-m-d H:i:s'));
							$current->setTimezone(new DateTimeZone($user_time_zone_value));
							$current_timestamp 				= strtotime($current->format('Y-m-d H:i:s'));
							$current_date             = 	$current->format('Y-m-d');
					   // $current_timestamp 	=	strtotime(date("Y-m-d h:i:sa"));
						 $allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
						$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
						 $clinic_end_timestamp			=		strtotime($clinic['date'].' '.$clinic['end_time']);

						$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					    if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
						  $clinic['clocked']				=	"";
					      $clinic['type'] 					= 	'Past';
					      $clinic['status_name'] 			= 	'Past clinic';
					      $clinic['name'] 					= 	$clinic['name'];
					      $clinic['duration'] 				= 	$clinic['estimated_duration'];
					      $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
					      $clinic['drive_time_required']	= 	isset($drive_time)?$drive_time:'';
					      if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
					    }else{
							if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
								//if($current_timestamp>$before_timestamp && $clinic_end_timestamp>$current_timestamp){
									if(!empty($clinic_status)){
										if($clinic_status->clock_in != null){
											$clinic['clocked']		=	0;
										}elseif($clinic_status->clock_in == null){
											$clinic['clocked']		=	1;
										}
										if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
											$clinic['clocked']			=	"";
											$is_past_clinic 			= 	1;
											$clinic['type'] 			= 	'Past';
											$clinic['status_name'] 		= 	'Past clinic';
										}else {
										$clinic['type'] 				= 	'clock_in';
										$clinic['status_name']			= 	'Clock in clinic';
										}
									}
								}else{
									$clinic['clocked']			=	"";
									$clinic['type'] 			= 	'MyClinics';
									$clinic['status_name'] 		= 	'My clinic';
								}
					      $clinic['name'] 						= 	$clinic['name'];
					      $clinic['duration'] 					= 	'';
					      $clinic['mileage_required'] 			=	'';
					      $clinic['drive_time_required']		= 	'';
					      if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
						  }else{
							 $clinic['mileage_status'] 		=	1;
							 $clinic['drive_time_status'] 		= 	1;
							 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
							 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
						  }
					    }
					    if($is_my_clinic){
					      $all_clinics['month'][] 	= $clinic;
					    }
					  }
					}
						//$currentTime = date('H:i:s',time());
						// 30-08-2017
						$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
						$user_time_zone_value = $user_time_zone[0]['timezone'];

						$start_time 		=	date('Y-m-d')." 12:00 AM";
						$end_time 			=	date('Y-m-d')." 11:59 PM";
						$user_time_zone 	=	new DateTimeZone($user_time_zone_value);
						$end_search_date  	= new DateTime($end_time, $user_time_zone);
						$start_search_date  = new DateTime($start_time, $user_time_zone);

						$start_search_date->setTimezone(new DateTimeZone('GMT'));
						$start_searchFinalDate = $start_search_date->format('Y-m-d H:i');

						$end_search_date  = new DateTime($end_time, $user_time_zone);

						$end_search_date->setTimezone(new DateTimeZone('GMT'));
						$end_searchFinalDate = $end_search_date->format('Y-m-d H:i');

						$clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
							->where('clinic_status.provider_id',$input_data['user_id'])
							->whereRaw("STR_TO_DATE(CONCAT(`date`, ' ' ,`time`), '%Y-%m-%d %H:%i') BETWEEN STR_TO_DATE('".$start_searchFinalDate."', '%Y-%m-%d %H:%i') AND STR_TO_DATE('".$end_searchFinalDate."', '%Y-%m-%d %H:%i')")
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();					

							//prd($clinics);
							if(!empty($clinics)){
					      foreach($clinics as $clinic){
							 // preptime key for offline clinic starts//
								$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
							  // preptime key for offline clinic ends here //
					        $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					        ->where('provider_id',$input_data['user_id'])
					        ->where('status',1)->count();
							$clinic_status 		=  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->where('provider_id',$input_data['user_id'])->get()->toArray();
							if(!empty($clinic_status)){
							  if($clinic_status[0]['mileage'] != "" && $clinic_status[0]['drive_time'] != ""){
								$mileage 			= $clinic_status[0]['mileage'].' Miles';
								$drive_time 		= $clinic_status[0]['drive_time'].' Minutes';
							  }
							}
					        /* $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
					        if(!empty($mileage_info)){
					            if($mileage_info[0]['mileage'] != "" && $mileage_info[0]['drive_time'] != ""){
					            $mileage 			= $mileage_info[0]['mileage'];
					            $drive_time 		= $mileage_info[0]['drive_time'];
					            }
					        } */
					        if($my_clinic_count>0){
					          $is_my_clinic 	= true;
					        }else{
					          $is_my_clinic 	= false;
					        }

							$clinic['contact_name'] = 	$clinic['name'];
							$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));

							$time->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_date_time 		= $time->format('Y-m-d H:i:s');
							$clinic_date_only 		= $time->format('Y-m-d');

							$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
							$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
							$prep_time 		= $Preptime->format('H:i');

							$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));

							$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
							$clinic_end_time 		= $EndTime->format('H:i');

							$clinic['date']					=	$time->format('d-m-Y');
								if($user['time_format'] == 24){
									$format = 'H:i';
								}elseif($user['time_format'] == 12){
									$format = 'h:i a';
								}
								$clinic['time']				=	date($format, strtotime($clinic_date_time));
								$clinic['prep_time']		=	date($format, strtotime($prep_time));
								$clinic['end_time']			=	date($format, strtotime($clinic_end_time));

					        $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					        $date 							= 	$clinic['date'];
					        $time 							= 	$clinic['time'];
					        $full_date 					= 	$date.' '.$time;
					        $clinic_timestamp 	= 	strtotime($full_date);
									$current = new DateTime(date('Y-m-d H:i:s'));
									$current->setTimezone(new DateTimeZone($user_time_zone_value));
									$current_timestamp 		  = strtotime($current->format('Y-m-d H:i:s'));
									$current_date             = $current->format('Y-m-d');
									$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
									$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
									$clinic_end_timestamp	  =	strtotime($clinic['date'].' '.$clinic['end_time']);

									// primary, medtech, other data listing starts //
									$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
									$primary_name = $primary['first_name'].' '.$primary['last_name'];

									$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
									$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

									$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
									foreach($others as $other){
											$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
									}
									// primary, medtech, other data listing ends //
									$clinic['primary_name']			=	isset($primary_name)?$primary_name:array();
									$clinic['medtech_name']			=	isset($medtech_name)?$medtech_name:array();
									$clinic['other_name']			=	isset($other_names)?$other_names:array();
									$other_names					=	array();
					        //$current_timestamp 	=	time();
					        $clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					        if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
										$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
										if(!empty($clinic_status)){
											if($clinic_status->clock_in == null || $clinic_status->clock_out == null){
												$clinic['mileage_status'] 		=	1;
												$clinic['drive_time_status'] 	= 	1;
											}else{
												if($clinic_status->mileage == 0){
									              $clinic['mileage_status'] 		=	0;
									            }else{
									              $clinic['mileage_status'] 		=	1;
									            }
									            if($clinic_status->drive_time == 0){
									              $clinic['drive_time_status'] 	= 	0;
									            }else{
									              $clinic['drive_time_status'] 	= 	1;
									            }
											}
										}
										
							  $clinic['clocked']				=	"";
					          $clinic['type'] 					= 	'Past';
					          $clinic['status_name'] 			= 	'Past clinic';
					          $clinic['name'] 					= 	$clinic['name'];
					          $clinic['duration'] 				= 	$clinic['estimated_duration'];
					          $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
					          $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';

					        }else{
								if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
									if(!empty($clinic_status)){
										if($clinic_status->clock_in != null){
											$clinic['clocked']		=	0;
										}elseif($clinic_status->clock_in == null){
											$clinic['clocked']		=	1;
										}
										if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
											$clinic['clocked']			=	"";
											$is_past_clinic 			= 	1;
											$clinic['type'] 			= 	'Past';
											$clinic['status_name'] 		= 	'Past clinic';
										}else {
										$clinic['type'] 				= 	'clock_in';
										$clinic['status_name']			= 	'Clock in clinic';
										}
									}

								}else{
									$clinic['clocked']			=	"";
									$clinic['type'] 			= 	'MyClinics';
									$clinic['status_name'] 		= 	'My clinic';
								}
					          $clinic['name'] 							= $clinic['name'];
					          $clinic['duration'] 						= '';
					          $clinic['mileage_required'] 		= '';
					          $clinic['drive_time_required']	= '';
					          //$clinic['mileage_status'] 			=	1;
					          //$clinic['drive_time_status'] 		= 	1;
							  if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
							 
					        }
					        if($is_my_clinic){
								$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
								$all_clinics['day'][] 	= $clinic;
					        }

					      }
					  }
					// clinics of day end here//	
					// clinics of week starts here // 
					$month_number_week 				= 	date('m');
					$month_start_date_week 			= 	$this->firstDay();
					$month_end_date_week 			= 	$this->lastDay();
					$week_values = date('Y-m-d ');
					  
					$week_clinics = ClinicStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
							->where('clinic_status.provider_id',$input_data['user_id'])
							->whereDate('date','>=',$month_start_date_week)
							->whereDate('date','<=',$month_end_date_week)
							->where('clinic_status.status',1)
							->orderBy('clinic_status.created_at','Desc')->get()->toArray();	
					  if(!empty($week_clinics)){
					    foreach($week_clinics as $clinic){
						// preptime key for offline clinic starts//
							$clinic['prep_time_key'] = strtotime($clinic['date'].' '.$clinic['prep_time']);
						// preptime key for offline clinic ends here //
					      $my_clinic_count = ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])
					      ->where('provider_id',$input_data['user_id'])
					      ->where('status',1)->count();
					      $mileage_info =  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->get()->toArray();
					      $clinic_status 		=  ClinicStatusModel::where('clinic_id',$clinic['clinic_id'])->where('provider_id',$input_data['user_id'])->get()->toArray();
							if(!empty($clinic_status)){
							  if($clinic_status[0]['mileage'] != "" && $clinic_status[0]['drive_time'] != ""){
								$mileage 			= $clinic_status[0]['mileage'].' Miles';
								$drive_time 		= $clinic_status[0]['drive_time'].' Minutes';
							  }
							}
					      if($my_clinic_count>0){
					        $is_my_clinic = true;
					      }else{
					        $is_my_clinic = false;
					      }
								// primary, medtech, other data listing starts //
								$primary = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','primary')->get()->first();
								$primary_name = $primary['first_name'].' '.$primary['last_name'];

								$medtech = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','medtech')->get()->first();
								$medtech_name = $medtech['first_name'].' '.$medtech['last_name'];

								$others = Rules::join('users','users.id','=','rules.provider_id')->where('rules.clinic_id',$clinic['clinic_id'])->where('rules.type','others')->get()->toArray();
								foreach($others as $other){
										$other_names[]['name'] = $other['first_name'].' '.$other['last_name'];
								}
					      // primary, medtech, other data listing ends //
					      $clinic['primary_name']		=	isset($primary_name)?$primary_name:array();
					      $clinic['medtech_name']		=	isset($medtech_name)?$medtech_name:array();
					      $clinic['other_name']			=	isset($other_names)?$other_names:array();
					      $other_names 					=	array();
					      $userdata 					=	User_model::GetUserById($input_data['user_id']);

								$clinic['contact_name'] = 	$clinic['name'];
								$time = new DateTime($clinic['date'].' '.$clinic['time'], new DateTimeZone('GMT'));
								$user_time_zone 			= User_model::select('timezone')->where('id',$input_data['user_id'])->get();
								$user_time_zone_value = $user_time_zone[0]['timezone'];
								$time->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_date_time 		= $time->format('Y-m-d H:i:s');
								$clinic_date_only 		= $time->format('Y-m-d');

								$Preptime = new DateTime($clinic['prep_time'], new DateTimeZone('GMT'));
								$Preptime->setTimezone(new DateTimeZone($user_time_zone_value));
								$prep_time 		= $Preptime->format('H:i');

								$EndTime = new DateTime($clinic['end_time'], new DateTimeZone('GMT'));
								$EndTime->setTimezone(new DateTimeZone($user_time_zone_value));
								$clinic_end_time 		= $EndTime->format('H:i');

								$clinic['date']					=	date('d-m-Y', strtotime($clinic_date_time));
									if($user['time_format'] == 24){
										$format = 'H:i';
									}elseif($user['time_format'] == 12){
										$format = 'h:i a';
									}
									$clinic['time']				=	date($format, strtotime($clinic_date_time));
									$clinic['prep_time']		=	date($format, strtotime($prep_time));
									$clinic['end_time']			=	date($format, strtotime($clinic_end_time));
								// clinic time according to user time zone ends //
					      $clinic['system_calender']		=	$userdata['system_calender'];
					      $clinic['start_year']				=	date('Y', strtotime($clinic['date']));
					      $clinic['start_month']			=	date('m', strtotime($clinic['date']));
					      $clinic['start_day']				=	date('d', strtotime($clinic['date']));
					      $clinic['end_year']				=	date('Y', strtotime($clinic['date']));
					      $clinic['end_month']				=	date('m', strtotime($clinic['date']));
					      $clinic['end_day']				=	date('d', strtotime($clinic['date']));
					      $clinic['start_hour']				=	date('H', strtotime($clinic['time']));
					      $clinic['start_minute']			=	date('i', strtotime($clinic['time']));
					      //get clinic end time from addition clinic duration into clinic time.
					      $time_addition					=	'+'.$clinic['estimated_duration'].' minutes';
					      //$clinic['date']					=	date('d-m-Y', strtotime($clinic['date']));
					      //$clinic['time']					=	date('H:i', strtotime($clinic['time']));
					      $clinic_end_timestamp 			= 	strtotime($time_addition, strtotime($clinic['time']));
					      $clinic['end_hour']				=	date('H', $clinic_end_timestamp);
					      $clinic['end_minute']			=	date('i', $clinic_end_timestamp);
					      //$clinic['prep_time']			=	date('h:i a', strtotime($clinic['prep_time']));

					      $clinic['estimated_duration']	=	number_format((($clinic['estimated_duration'])/60),2).' hour';
					      $date 							= 	$clinic['date'];
					      $time 							= 	$clinic['time'];
					      $full_date 						= 	$date.' '.$time;
					      $clinic_timestamp 				= 	strtotime($full_date);
								$current = new DateTime(date('Y-m-d H:i:s'));
								$current->setTimezone(new DateTimeZone($user_time_zone_value));
								$current_timestamp 				= strtotime($current->format('Y-m-d H:i:s'));
								$current_date             		= 	$current->format('Y-m-d');
								$allow_clockin_before_time     		=	$this->GetAdminSettingsValue('allow_clockin_before_preptime');
								$before_timestamp 					=	(strtotime($clinic_date_only.' '.$prep_time))-$allow_clockin_before_time*60;
								$clinic_end_timestamp			=		strtotime($clinic['date'].' '.$clinic['end_time']);

							$clinic_status 			= 	ClinicStatusModel::GetClinicStatusById($clinic['clinic_id'],$input_data['user_id']);
					        if($current_timestamp>$clinic_end_timestamp && ($clinic_status->clock_out != null || $clinic_status->clock_in==null)){
							$clinic['clocked']				=	"";
					        $clinic['type'] 				= 	'Past';
					        $clinic['status_name'] 			= 	'Past clinic';
					        $clinic['name'] 				= 	$clinic['name'];
					        $clinic['duration'] 			= 	$clinic['estimated_duration'];
					        $clinic['mileage_required'] 	=	isset($mileage)?$mileage:'';
					        $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
					        if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }
					      }else{
								if(($current_timestamp>$before_timestamp) && ($clinic_end_timestamp>$current_timestamp || ($clinic_status->clock_out==null && $clinic_status->clock_in!=null))){
									if(!empty($clinic_status)){
										if($clinic_status->clock_in != null){
											$clinic['clocked']		=	0;
										}elseif($clinic_status->clock_in == null){
											$clinic['clocked']		=	1;
										}
										if($clinic_status->clock_in != null && $clinic_status->clock_out != null){
											$clinic['clocked']			=	"";
											$is_past_clinic 			= 	1;
											$clinic['type'] 			= 	'Past';
											$clinic['status_name'] 		= 	'Past clinic';
										}else {
										$clinic['type'] 				= 	'clock_in';
										$clinic['status_name']			= 	'Clock in clinic';
										}
									}
								}else{
									$clinic['clocked']			=	"";
									$clinic['type'] 			= 	'MyClinics';
									$clinic['status_name'] 		= 	'My clinic';
								}
					        $clinic['name'] 				= 	$clinic['name'];
					        $clinic['duration'] 			= 	'';
					        $clinic['mileage_required'] 	=	'';
					        $clinic['drive_time_required'] 	= 	'';
					        if($clinic_status->mileage == 0 && $clinic_status->drive_time == 0){
								 $clinic['mileage_status'] 			=	0;
								 $clinic['drive_time_status'] 		= 	0;
							  }else{
								 $clinic['mileage_status'] 		=	1;
								 $clinic['drive_time_status'] 		= 	1;
								 $clinic['mileage_required'] 		=	isset($mileage)?$mileage:'';
								 $clinic['drive_time_required'] 	= 	isset($drive_time)?$drive_time:'';
							  }

					      }
					      if($is_my_clinic){
							  $clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic['date']));
							  $all_clinics['week'][] 	= $clinic;
					      }
					    }
					  }
					
					// clinics of week ends here //
					$userimg = WEBSITE_UPLOADS_URL.'users/'.$user['image'];
					if(isset($user['image']) && $user['image'] != '' ){
						$user_image = $userimg;
					}else{
						$user_image = WEBSITE_UPLOADS_URL.'users/man.png';
					}
					if(!empty($all_clinics)){
					return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok.','clinics'=>$all_clinics,'image'=>$user_image)));
					}else{
					return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found for given criteria.')));
					}
					}
				else{
					return $this->encrypt(json_encode(array('deleted'=>'success','message'=>'User is deleted.')));
				}
		}
	}
}
