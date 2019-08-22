<?php

namespace App\Http\Controllers\front;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\ClinicsModel;
use App\Model\ApiTokens;
use App\Model\AnnouncementModel;
use App\Model\ClinicStatusModel;
use App\Model\ProvidersModel;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use App\Model\Notifications;
use App\Model\User_model;
use App\Model\AdminSettings;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Image,stdClass;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PushNotificationsController extends BaseController
{
	 /**
     * function for clockout push notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClockOutNotification(){
			$user_clinics = ClinicStatusModel::where('status',1)->whereNull('clock_out')->whereNotNull('clock_in')->get()->toArray();
			if($user_clinics){
				foreach ($user_clinics as $user_clinic) {
					$provider_id 		= $user_clinic['provider_id'];
					$provider_data 		= ApiTokens::where('user_id',$user_clinic['provider_id'])->first();
					if($provider_data){
						$provider_device_id = $provider_data->device_id;
						$clinic_data 		= ClinicsModel::where('id',$user_clinic['clinic_id'])->first();
						$clock_in_time 		= $user_clinic['clock_in'];

						$user_data = User_model::select('push_notification','reminder_clock_out')->where('id',$user_clinic['provider_id'])->first();
						if($user_data->push_notification == 1){
							 if($user_data->reminder_clock_out == 0 || $user_data->reminder_clock_out == null){
								$reminder_times 		= $this->GetAdminSettingsValue('default_time_clockout');
								$estimated_time 	= '+'.$reminder_times.' minutes';
								$reminder_time		=  $this->GetAdminSettingsValue('default_time_clockout');
							}elseif($user_data->reminder_clock_out == 1){
								$estimated_time 	= '+1 minutes';
								$reminder_time		=  $this->GetAdminSettingsValue('default_time_clockout');
							}
							else{
								$estimated_time 	= '+'.$user_data->reminder_clock_out.' minutes';
								$reminder_time		=  $this->GetAdminSettingsValue('default_time_clockout');
							} 
							
							//prd($reminder_time);
							$current_time	 	= strtotime(date('Y-m-d H:i:s'));
							$current_time1	 	= strtotime(date('Y-m-d H:i'));
							$clinic_end_time 	= strtotime($clinic_data->date.' '.$clinic_data->end_time);
							
							$clinic_latitude    = $clinic_data->latitude;
							$clinic_longitude   = $clinic_data->longitude;
							$clinic_endTime				  = strtotime($clinic_data->date.' '.$clinic_data->end_time);
							$clinic_endTimeWithRemindTime = strtotime($estimated_time,$clinic_endTime);	
			
							
							$user_latLong 	= DB::table('geolocation')->where('clinic_id',$user_clinic['clinic_id'])->where('user_id',$user_clinic['provider_id'])->orderBy('id','DESC')->LIMIT(1)->get()->first();
							if(isset($user_latLong)){
							$user_lat 		= $user_latLong->latitude;
							$user_lng 		= $user_latLong->longitude;
							$drive_time 	= $this->GetDrivingTime($clinic_latitude,$user_lat,$clinic_longitude,$user_lng);
							$drive_miles 	= $this->GetDrivingMiles($clinic_latitude,$user_lat,$clinic_longitude,$user_lng);
							//$reminder_time 		= $this->GetAdminSettingsValue('default_time_clockout');
							$reminder_mile 		= $this->GetAdminSettingsValue('default_miles_clockout');
							
							$admin_reminder_time 		= $this->GetAdminSettingsValue('notify_clockout_time_admin');
							$admin_reminder_mile 		= $this->GetAdminSettingsValue('notify_clockout_mile_admin');
							
							
							//process to send email notification and save record in notification table of admin starts //
							if(($user_clinic['clock_out'] == null && $user_clinic['clock_in'] != null) && ($drive_time >=  $admin_reminder_time || $drive_miles >= $admin_reminder_mile)){
								$user_data = DB::table('users')->where('id',$user_latLong->user_id)->get()->first();
								$clinicData = DB::table('clinics')->where('id',$user_latLong->clinic_id)->get()->first();
								// save notification start // 
								$message       =   'Reminder clockout notification for '.$user_data->first_name.' '.$user_data->last_name;
								$check_already_added   = DB::table('admin_notifications')->where('required_id',$user_latLong->clinic_id)->where('type','clockout')->get()->count();
								if($check_already_added ==0){
									$this->save_admin_notification($user_latLong->clinic_id,'clockout','clockout',$message);
									// save notification end //
									
									// mail send to admin starts//
									$providerName  = $user_data->first_name.' '.$user_data->last_name;
									$clinicName    = $clinicData->name;
									$clinicAddress    = $clinicData->location_name;
									$replace_array 			=   array($clinicName,$providerName,$clinicAddress);
									$email_send    			=   $this->mail_send('notify_admin_for_provider_clockout',$user_data->email,$providerName,$replace_array);
									// mail send to admin end here //
								}
								
								
							}
							//process to send email notification and save record in notification table of admin end //
							
							//process to send push notification and save record in notification table of provider starts //
							/* if($user_clinic['clinic_id']==63){
								prd($user_clinic['clock_out']);
							}  */
							
							if(($user_clinic['clock_out'] == null && $user_clinic['clock_in'] != null) && (time() >=  $clinic_endTimeWithRemindTime /* || $drive_miles > $reminder_mile */)){
								
								$notification_message = ["notification" => [
																	          	"body" 	=> "Please clock out the clinic!",
																	          	"title" => "Clock out notification"
																	       	],
															"notification_data" => [
																	    "user_id" 	=> $user_clinic['provider_id'],
																	    "clinic_id" => $user_clinic['clinic_id'],
																	    "type" 		=> 'clockout',
																	],
														];
								$notification = Notifications::select('updated_at')->where('type','clockout')->where('required_id',$user_clinic['clinic_id'])->where('user_id',$user_clinic['provider_id'])->orderBy('id','desc')->first();
								if($notification){
									$notification_updated_date 	= $notification->updated_at;
									$notification_end_time  = strtotime('+'.$reminder_time.' minutes',strtotime($notification_updated_date));
									if($current_time > $notification_end_time){
										$PushNotification 	=   $this->notification_api($provider_device_id,$notification_message);
										if($PushNotification){
											$notification_message['device_id'] = $provider_device_id;
											$this->save_notification($notification_message);
										}
									}
								}else{
									$PushNotification 	=   $this->notification_api($provider_device_id,$notification_message);
									if($PushNotification){
										$notification_message['device_id'] = $provider_device_id;
										$this->save_notification($notification_message);
									}
								}
							}
							//process to send push notification and save record in notification table of provider end //
							}
						}
					}
				}
				echo "success";
			}else{
				echo "No records available";
			}die;
	 }

	 /**
     * function for clockIn push notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClockInNotification(){
			$user_clinics = ClinicStatusModel::where('status',1)->whereNull('clock_in')->whereNull('clock_out')->get()->toArray();
			if($user_clinics){
				foreach ($user_clinics as $user_clinic) {
					$provider_id 		= $user_clinic['provider_id'];
					$provider_data 		= ApiTokens::where('user_id',$user_clinic['provider_id'])->first();
					if($provider_data){
						$provider_device_id = $provider_data->device_id;
						$clinic_data 		= ClinicsModel::where('id',$user_clinic['clinic_id'])->first();
						$date = $clinic_data->date;
						$time = $clinic_data->time;
						
						$clinic_start_time = $date. " ". $time;
						$user_data = User_model::select('push_notification','reminder_clock_in')->where('id',$user_clinic['provider_id'])->first();
						if($user_data->push_notification == 1){
							if($user_data->reminder_clock_in == 'Default'){
								$reminder_clockin = $this->GetAdminSettingsValue('clock_in_default_time');
								$estimated_time 	= '-'.($reminder_clockin).' minutes';
							}else{
								$estimated_time 	= '-'.($user_data->reminder_clock_in*60).' minutes';
							}
							$current_time	 		= strtotime(date('Y-m-d H:i'));
							$appointment_start_time = strtotime($estimated_time,strtotime($clinic_start_time));
							if($current_time >=  $appointment_start_time){
								$notification_message = ["notification" => [
																	          	"body" 	=> "Please clock in the clinic!",
																	          	"title" => "Clock in notification"
																	       	],
															"notification_data" => [
																	    "user_id" 	=> $user_clinic['provider_id'],
																	    "clinic_id" => $user_clinic['clinic_id'],
																	    "type" 		=> 'clockin',
																	],
														];
								$notification = Notifications::where('type','clockin')->where('required_id',$user_clinic['clinic_id'])->where('user_id',$user_clinic['provider_id'])->orderBy('id','desc')->first();
								if(empty($notification)){
									$PushNotification 	=   $this->notification_api($provider_device_id,$notification_message);
									if($PushNotification){
										$notification_message['device_id'] = $provider_device_id;
										$this->save_notification($notification_message);
									}
								}
							}
						}
					}
				}
				echo "success";
			}else{
				echo "No records available";
			}die;
	 }

	 /**
     * function for group announsement push notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function AnnouncementGroupNotification(){
			$notifications = Notifications::where('status','not_sent')->where('type','announcement')->where('announcement_type','app_setting')->get()->toArray();
			if($notifications){
				foreach ($notifications as $row) {
					$provider_id 		= $row['user_id'];
					$provider_data 		= ApiTokens::where('user_id',$provider_id)->first();
					if($provider_data){
						$provider_device_id = $provider_data->device_id;
						$announsement_data 	= AnnouncementModel::where('id',$row['required_id'])->first();
						$user_data 			= User_model::select('notification_groupby','push_notification')->where('id',$provider_id)->first();
						if(isset($announsement_data) && $announsement_data != null || !empty($announsement_data)){
							if($user_data->push_notification == 1){
							$notification_message = ["notification" => [
																          	"body" 	=> "New announcement is added!",
																          	"title" => "Announcement notification"
																       	],
														"notification_data" => [
																    "announcement_id" => $announsement_data->id,
																    "title" => isset($announsement_data->title)?$announsement_data->title:'',
																    "message" => isset($announsement_data->description)?$announsement_data->description:'',
																    "type" 	  => 'announcement',
																],
													];
							$current_time	 	= strtotime(date('Y-m-d H:i:s'));
							$announsment_time 	= strtotime($row['updated_at']);
							if($user_data->notification_groupby == 0 || $user_data->notification_groupby == null){
								if($current_time >  $announsment_time){
									if($this->notification_api($provider_device_id,$notification_message)){
										$notification_message['device_id'] = $provider_device_id;
										$this->update_notification($notification_message,$row['id']);
									}
								}
							}else if($user_data->notification_groupby == 2){
								$estimated_time 	= '+60 minutes';
								$appointment_start_time = strtotime($estimated_time,strtotime($row['updated_at']));
								if($current_time > $appointment_start_time){
									if($this->notification_api($provider_device_id,$notification_message)){
										$notification_message['device_id'] = $provider_device_id;
										$this->update_notification($notification_message,$row['id']);
									}
								}
							}else if($user_data->notification_groupby == 3){
								$morning_time = strtotime(date('Y-m-d').' 06:00:00');
								$morning_expire_time = strtotime(date('Y-m-d').' 06:01:10');
								$evening_time = strtotime(date('Y-m-d').' 18:00:00');
								$evening_expire_time = strtotime(date('Y-m-d').' 18:01:10');
								if($current_time >= $morning_time && $current_time < $morning_expire_time){
									if($this->notification_api($provider_device_id,$notification_message)){
										$notification_message['device_id'] = $provider_device_id;
										$this->update_notification($notification_message,$row['id']);
									}
								}
								if($current_time >= $evening_time && $current_time < $evening_expire_time){
									if($this->notification_api($provider_device_id,$notification_message)){
										$notification_message['device_id'] = $provider_device_id;
										$this->update_notification($notification_message,$row['id']);
									}
								}
							}else if($user_data->notification_groupby == 4){
								$morning_time = strtotime(date('Y-m-d').' 06:00:00');
								$morning_expire_time = strtotime(date('Y-m-d').' 06:01:10');
								if($current_time >= $morning_time && $current_time < $morning_expire_time){
									if($this->notification_api($provider_device_id,$notification_message)){
										$notification_message['device_id'] = $provider_device_id;
										$this->update_notification($notification_message,$row['id']);
									}
								}
							}
						}
						}
					}
				}
				echo "success";
			}else{
				echo "no records available";
			}die;
	 }
}
