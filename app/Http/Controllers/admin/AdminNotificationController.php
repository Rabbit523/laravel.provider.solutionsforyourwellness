<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\CertificationModel;
use App\Model\admin\AnnouncementModel;
use App\Model\User_model;
use App\Model\admin\ProvidersModel;
use App\Model\admin\ClinicsModel;
use App\Model\admin\ClinicsStatusModel;
use App\Model\admin\AdminNotifications;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use DateTime,DateTimeZone;

class AdminNotificationController extends BaseController
{

	/**
     * function for Clinic has gone x unfilled
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 //public function ClinicUnfilledNotifications(){
	 	//$sqlQuery 			= 	"SELECT * FROM clinics WHERE `personnel` > (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id)";
		//$clinic_data = DB::select(DB::raw($sqlQuery));
		//if(!empty($clinic_data)){
			//foreach ($clinic_data as $clinic) {
				//if($clinic->default_unfilled_time){
					//$estimated_time 	= 	'+'.($clinic->default_unfilled_time*60).' minutes';
				//}else{
					//$reminder_time 		= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
				 	//$estimated_time 	= 	'+'.($reminder_time*60).' minutes';
				 //}
			 	//$start_time 			= 	strtotime($estimated_time,$clinic->create_timestamp);
			 	//$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

			 	//if($current_time > $start_time){
					//$message = $clinic->name.' has gone unfilled';
					//$for_user               =   'super_user';
					//$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('for_user',$for_user)->where('type','clinic')->where('notification_type','clinic_unfilled')->get()->count();
					//if($admin_notifications == 0){
						//$this->save_admin_notification($clinic->id,'clinic','clinic_unfilled',$message);
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for Clinic is in X time and unfilled
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ClinicTimeUnfilledNotifications(){
	 	//$sqlQuery 			= 	"SELECT * FROM clinics WHERE `personnel` > (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id)";
		//$clinic_data = DB::select(DB::raw($sqlQuery));
		//if(!empty($clinic_data)){
			//foreach ($clinic_data as $clinic) {
				//if($clinic->default_unfilled_time){
					//$estimated_time 	= 	'+'.($clinic->default_unfilled_time*60).' minutes';
				//}else{
					//$reminder_time 		= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
				 	//$estimated_time 	= 	'+'.($reminder_time*60).' minutes';
				//}
			 	//$before_reminder_time 	= 	$this->GetAdminSettingsValue('unfilled_before_time');
			 	//$estimated_before_time	= 	'-'.($before_reminder_time*60).' minutes';

			 	//$start_time 			= 	strtotime($estimated_time,$clinic->create_timestamp);
			 	//$new_start_time 		= 	strtotime($estimated_before_time,$start_time);

			 	//$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

			 	//if($current_time > $new_start_time){
					//$for_user               =   'super_user';
					//$message = $clinic->name.' is unfilled in '.$before_reminder_time .' hours';
					//$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('for_user',$for_user)->where('type','clinic')->where('notification_type','clinic_time_unfilled')->get()->count();
					//if($admin_notifications == 0){
						//$this->save_admin_notification($clinic->id,'clinic','clinic_time_unfilled',$message);
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for Clinic has been filled Notification.
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ClinicFilledNotifications(){
	 	//$sqlQuery 			= 	"SELECT * FROM clinics WHERE `personnel` <= (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id)";
		//$clinic_data = DB::select(DB::raw($sqlQuery));
	 	//if(!empty($clinic_data)){
			//foreach ($clinic_data as $clinic) {
				//$reminder_time 			= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
			 	//$estimated_time 		= 	'+'.($reminder_time*60).' minutes';
			 	//$start_time 			= 	strtotime($estimated_time,$clinic->create_timestamp);
				//$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

			 	//if($current_time > $start_time){
					//$for_user               =   'super_user';
					//$message = $clinic->name.' has been filled';
					//$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('for_user',$for_user)->where('type','clinic')->where('notification_type','clinic_filled')->get()->count();
					//if($admin_notifications == 0){
						//$this->save_admin_notification($clinic->id,'clinic','clinic_filled',$message);
					//}
				//}

			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}
	 
	 ///**
     //* function for Clinic status is pending mileage info
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ClinicPendingMileageNotifications(){
	 	//$clinic_data = ClinicsStatusModel::where('status',1)->where('mileage',0)->whereNotNull('clock_in')->whereNotNull('clock_out')->get()->toArray();
	 	//if(!empty($clinic_data)){
			//foreach ($clinic_data as $clinic) {
				//$for_user               =   'super_user';
				//$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic['id'])->where('for_user',$for_user)->where('type','clinic')->where('notification_type','pending_mileage')->get()->count();
				//if($admin_notifications == 0){
					//$message = $clinic['name'].' status is pending mileage info';
					//$this->save_admin_notification($clinic['id'],'clinic','pending_mileage',$message);
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	  ///**
     //* function for Clinic status is complete
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ClinicStatusCompleteNotifications(){
	 	//$clinic_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')->where('clinic_status.status',1)->where('clinic_status.mileage', '!=', 0)->where('clinic_status.drive_time','!=', 0)->whereNotNull('clinic_status.clock_in')->whereNotNull('clinic_status.clock_out')->get()->toArray();
	 	//if(!empty($clinic_data)){
			//foreach ($clinic_data as $clinic) {
				//$for_user               =   'super_user';
				//$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic['id'])->where('for_user',$for_user)->where('type','clinic')->where('notification_type','complete')->get()->count();
				//if($admin_notifications == 0){
					//$message = $clinic['name'].' status is complete';
					//$this->save_admin_notification($clinic['id'],'clinic','complete',$message);
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for If provider over x hours in a month
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ProviderHoursInaMonthNotification(){
	 	//$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
	 	//if(!empty($providers_data)){
	 		//foreach ($providers_data as $provider) {
				//if($provider['max_hours']){
					//$default_max_hours 	= 	$provider['max_hours'];
				//}else{
					//$default_max_hours 	= 	$this->GetAdminSettingsValue('default_max_scheduled_hours');
				//}
				//$data = DB::table("clinic_status")
									    //->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
									    //->where('status',1)
									    //->where('provider_id',$provider['id'])
									    //->whereNotNull('clock_in')
									    //->whereNotNull('clock_out')
									    //->whereNotNull('clinic_spend_time')
									    //->whereMonth('clock_in',date('m'))
									    //->whereYear('clock_in',date('Y'))
									    //->first();
				//if($data->total_hours){
					//$total_hours = $data->total_hours/60;
					//if($total_hours > $default_max_hours){
						//$difference = $total_hours - $default_max_hours;
						//$for_user               =   'super_user';
						//$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('for_user',$for_user)->where('type','provider')->where('notification_type','provider_hours_in_a_month')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
						//if($admin_notifications == 0){
							//$provider_name 	=  $provider['first_name'].' '. $provider['last_name'];
							//$message 		=  $provider_name.' over '.$difference.' hours in a month';
							//$this->save_admin_notification($provider['id'],'provider','provider_hours_in_a_month',$message);
						//}
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for If provider go over x hours in a day
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ProviderHoursInaDayNotification(){
	 	//$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
	 	//if(!empty($providers_data)){
			//foreach ($providers_data as $provider) {
				//if($provider['max_hours_in_a_day']){
					//$max_hours_per_day 	= 	$provider['max_hours_in_a_day'];
				//}else{
					//$max_hours_per_day 	= 	$this->GetAdminSettingsValue('default_max_scheduled_per_day');
				//}
				//$data = DB::table("clinic_status")
									    //->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
									    //->where('status',1)
									    //->where('provider_id',$provider['id'])
									    //->whereNotNull('clock_in')
									    //->whereNotNull('clock_out')
									    //->whereNotNull('clinic_spend_time')
									    //->whereDay('clock_in',date('d'))
									    //->whereMonth('clock_in',date('m'))
									    //->whereYear('clock_in',date('Y'))
									    //->first();
				//if($data->total_hours){
					//$total_hours = $data->total_hours/60;
					//if($total_hours > $max_hours_per_day){
						//$difference = $total_hours - $max_hours_per_day;
						//$for_user               =   'super_user';
						//$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('for_user',$for_user)->where('type','provider')->where('notification_type','provider_hours_in_a_month')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
						//if($admin_notifications == 0){
							//$provider_name =  $provider['first_name'].' '. $provider['last_name'];
							//$message 	   =  $provider_name.' over '.round($difference).' hours in a day';
							//$this->save_admin_notification($provider['id'],'provider','provider_hours_in_a_month',$message);
						//}
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for If provider go over x hours in a day
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ProviderHoursInaClinicNotification(){
	 	//$clinics_data = ClinicsStatusModel::where('status',1)
								    //->whereNotNull('clock_in')
								    //->whereNotNull('clock_out')
								    //->whereNotNull('clinic_spend_time')
								    //->whereDay('clock_in',date('d'))
								    //->whereMonth('clock_in',date('m'))
								    //->whereYear('clock_in',date('Y'))
								    //->get()->toArray();
		//if(!empty($clinics_data)){
			//foreach ($clinics_data as $clinics) {
				//$clinic = ClinicsModel::where('status',1)->where('id', $clinics['clinic_id'])->first();
				//if($clinic->estimated_duration){
					//$max_hours 	= 	$clinic->estimated_duration;
				//}else{
					//$max_hours 	= 	$this->GetAdminSettingsValue('default_max_scheduled_per_clinic');
				//}
				//if($clinics['clinic_spend_time']){
					//if($clinics['clinic_spend_time'] > $max_hours){
						//$difference = $clinics['clinic_spend_time'] - $max_hours;
						//$for_user               =   'super_user';
						//$admin_notifications 	= 	AdminNotifications::where('required_id',$clinics['clinic_id'])->where('user_id',$clinics['provider_id'])->where('for_user',$for_user)->where('type','clinic')->where('notification_type','provider_hours_in_a_clinic')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
						//if($admin_notifications == 0){
							//$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
							//$provider_name  = 	$provider->first_name.' '. $provider->last_name;
							//$message 		=  $provider_name.' over '.round($difference).' minutes in a day';
							//$this->save_admin_notification($clinics['clinic_id'],'clinic','provider_hours_in_a_clinic',$message,$clinics['provider_id']);
						//}
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for If provider over x hours in a month
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ProviderMilageInaMonthNotification(){
	 	//$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
	 	//if(!empty($providers_data)){
			//foreach ($providers_data as $provider) {
				//if($provider['mileage_info']){
					//$total_mileage 		= $provider['mileage_info'];
				//}else{
					//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_month');
				//}
				//$data = DB::table("clinic_status")
									    //->select(DB::raw("SUM(mileage) as total_mileage"))
									    //->where('status',1)
									    //->where('provider_id',$provider['id'])
									    //->whereNotNull('clock_in')
									    //->whereNotNull('clock_out')
									    //->whereMonth('created_at',date('m'))
									    //->whereYear('created_at',date('Y'))
									    //->first();
				//if($data->total_mileage){
					//if($data->total_mileage > $total_mileage){
						//$difference = $data->total_mileage - $total_mileage;
						//$for_user               =   'super_user';
						//$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('for_user',$for_user)->where('type','provider')->where('notification_type','provider_mileage_in_a_month')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
						//if($admin_notifications == 0){
							//$provider_name = $provider['first_name'].' '. $provider['last_name'];
							//$message =  $provider_name.' over '.$difference.' mileage in a month';
							//$this->save_admin_notification($provider['id'],'provider','provider_mileage_in_a_month',$message);
						//}
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for If provider over x hours in a month
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ProviderMilageInaDayNotification(){
	 	//$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
	 	//if(!empty($providers_data)){
			//foreach ($providers_data as $provider) {
				//if($provider['mileage_per_day']){
					//$total_mileage 		= $provider['mileage_per_day'];
				//}else{
					//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_day');
				//}
				//$data = DB::table("clinic_status")
									    //->select(DB::raw("SUM(mileage) as total_mileage"))
									    //->where('status',1)
									    //->where('provider_id',$provider['id'])
									    //->whereNotNull('clock_in')
									    //->whereNotNull('clock_out')
									    //->whereDay('clock_in',date('d'))
									    //->whereMonth('clock_in',date('m'))
									    //->whereYear('clock_in',date('Y'))
									    //->first();
				//if($data->total_mileage){
					//if($data->total_mileage > $total_mileage){
						//$difference = $data->total_mileage - $total_mileage;
						//$for_user               =   'super_user';
						//$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('for_user',$for_user)->where('type','provider')->where('notification_type','provider_mileage_in_a_day')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
						//if($admin_notifications == 0){
							//$provider_name = $provider['first_name'].' '. $provider['last_name'];
							//$message =  $provider_name.' over '.$difference.' mileage in a day';
							//$this->save_admin_notification($provider['id'],'provider','provider_mileage_in_a_day',$message);
						//}
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	 //}

	 ///**
     //* function for If provider goes over x mileage in a clinic
     //*
     //* @param null
     //*
     //* @return response data on success otherwise error.
     //*/
	 //public function ProviderMilageInaClinicNotification(){

	 	//$clinics_data = ClinicsStatusModel::where('status',1)
								    //->whereNotNull('clock_in')
								    //->whereNotNull('clock_out')
								    //->whereDay('clock_in',date('d'))
								    //->whereMonth('clock_in',date('m'))
								    //->whereYear('clock_in',date('Y'))
								    //->get()->toArray();
		//if(!empty($clinics_data)){
			//foreach ($clinics_data as $clinics) {
				//$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
				//if($provider->mileage_per_clinic){
					//$max_mileage = 	$provider->mileage_per_clinic;
				//}else{
					//$max_mileage 	= 	$this->GetAdminSettingsValue('default_max_mileage_per_clinic');
				//}
				//if($max_mileage){
					//if($clinics['mileage'] > $max_mileage){
						//$difference = $clinics['mileage'] - $max_mileage;
						//$for_user               =   'super_user';
						//$admin_notifications 	= 	AdminNotifications::where('required_id',$clinics['clinic_id'])->where('user_id',$clinics['provider_id'])->where('for_user',$for_user)->where('type','clinic')->where('notification_type','provider_mileage_in_a_clinic')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
						//if($admin_notifications == 0){
							//$provider_name  = 	$provider->first_name.' '. $provider->last_name;
							//$message 		=   $provider_name.' over '.round($difference).' mileage in a clinic';
							//$this->save_admin_notification($clinics['clinic_id'],'clinic','provider_mileage_in_a_clinic',$message,$clinics['provider_id']);
						//}
					//}
				//}
			//}
			//echo "success";
		//}else{
			//echo "no records available";
		//}
		//die;
	//}
	
	/**
     * function for show single notification page and update notification view
     *
     * @param notification_id
     *
     * @return response single notification page on success otherwise return dashboad page with error.
     */
	 public function GetNotificationById($id){
		 $notification = AdminNotifications::where('id',$id)->first();
		 if(!empty($notification)){
			 if(Auth::user()->role_id == 1){
				$model 					= 	AdminNotifications::find($id);
				$model->admin_views 	= 	1;
			 }else{
				$model 					= 	AdminNotifications::find($id);
				$model->views 			= 	1;
			 }
			 $saved 		=	$model->save();
			 return view('admin.notifications.index',compact('notification'));
		 }else{
			Toast::error(trans("You are using wrong link."));
			return redirect()->route('admindashboard');
		 }
	 }
	 /**
     * function for show single notification page and update notification view
     *
     * @param notification_id
     *
     * @return response single notification page on success otherwise return dashboad page with error.
     */
	 public function AllNotifications($admin_id=null){
		 $notifications = AdminNotifications::where('user_id',$admin_id)->where('admin_views',0)->orderBy('id','desc')->get();
		 if(!empty($notifications)){
			 if(Auth::user()->role_id == 1){
				$model 					= 	AdminNotifications::where('user_id',$admin_id)->update(array('admin_views'=>'1'));
		 }else{
			Toast::error(trans("You are using wrong link."));
			return redirect()->route('admindashboard');
		 }
		 return view('admin.notifications.list',compact('notifications'));
		}
	 }
	 /**
     * function for show single notification page and update notification view
     *
     * @param notification_id
     *
     * @return response single notification page on success otherwise return dashboad page with error.
     */
	 public function AllNotificationsSuperUser(){
		 $notifications = AdminNotifications::where('admin_views',0)->orderBy('id','desc')->get();
		 if(!empty($notifications)){
			 if(Auth::user()->role_id == 1){
				$model 					= 	AdminNotifications::where('user_id',Auth::user()->id)->update(array('admin_views'=>'1'));
		 }else{
			Toast::error(trans("You are using wrong link."));
			return redirect()->route('admindashboard');
		 }
		 return view('admin.notifications.list',compact('notifications'));
		}	
	 }
	/////////////// admin notification starts here //////////////////
	/**
     * function for Per Admin Clinic has gone x unfilled
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClinicUnfilledNotificationsAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$sqlQuery 			= 	"SELECT * FROM clinics WHERE `personnel` > (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id ) ";
			$clinic_data = DB::select(DB::raw($sqlQuery));
			if(!empty($clinic_data)){
				foreach ($clinic_data as $clinic) {
					$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
					if($notification_type == 'email'){
					// email sending process for confirm clinic starts
					$clinic_date_time		= 	new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
					$clinic_date_time->setTimezone(new DateTimeZone($admin->timezone));
					$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
					$date         			= 	$clinic_date_time->format('Y-m-d');
					$time         			= 	$clinic_date_time->format('H:i');
					$subject_replace		=   array($clinic->name);
					$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$date,$time,$clinic->location_name);
					$type 					=	'unfilled_clinic';
					$check_status			=	$this->CheckMailSentStatus($clinic->id,$admin->id,$type);
					
					if($check_status == 0){
						$email_send    			=   $this->mail_send('clinic_unfilled',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic->id,$admin->id,$type);
					}	
					// email sending process for confirm clinic ends
					}elseif($notification_type == 'push'){
						if(($admin->unfilled_notify != '' || $admin->unfilled_notify != null) && $admin->unfilled_notify != 'off'){
							$estimated_time 	= 	'+'.($admin->unfilled_notify*60).' minutes';
						}
						$start_time 			= 	strtotime($estimated_time,strtotime($clinic->created_at));
						$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

						if($current_time > $start_time){
							$message = $clinic->name.' has gone unfilled';
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','clinic_unfilled')->get()->count();
							
							if($admin_notifications == 0){
								$this->save_admin_notification($clinic->id,'clinic','clinic_unfilled',$message,$admin_id);
							}
						}
					}elseif($notification_type == 'both'){
						// email sending process for confirm clinic starts
						$clinic_date_time		= 	new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
						$clinic_date_time->setTimezone(new DateTimeZone($admin->timezone));
						$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
						$date         			= 	$clinic_date_time->format('Y-m-d');
						$time         			= 	$clinic_date_time->format('H:i');
						$subject_replace		=   array($clinic->name);
						$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$date,$time,$clinic->location_name);
						$type 					=	'unfilled_clinic';
						$check_status			=	$this->CheckMailSentStatus($clinic->id,$admin->id,$type);
						
						if($check_status == 0){
							$email_send    			=   $this->mail_send('clinic_unfilled',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic->id,$admin->id,$type);
						}	
						// email sending process for confirm clinic ends
						
						// process to send web push notification starts here //
						if(($admin->unfilled_notify != '' || $admin->unfilled_notify != null) && $admin->unfilled_notify != 'off'){
							$estimated_time 	= 	'+'.($admin->unfilled_notify*60).' minutes';
						}
						$start_time 			= 	strtotime($estimated_time,strtotime($clinic->created_at));
						$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

						if($current_time > $start_time){
							$message = $clinic->name.' has gone unfilled';
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','clinic_unfilled')->get()->count();
							
							if($admin_notifications == 0){
								$this->save_admin_notification($clinic->id,'clinic','clinic_unfilled',$message,$admin_id);
							}
						}
						// process to send web push notification ends here //
						
					}elseif($notification_type == 'none'){
						// no notifications goes nothing
					}
				}
				echo "success";
			}else{
				echo "no records available";
			}
			
		}
	 }
	/**
     * function for Clinic is in X time and unfilled for per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClinicTimeUnfilledNotificationsAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$sqlQuery 	   = 	"SELECT * FROM clinics WHERE `personnel` > (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id) ";
			$clinic_data = DB::select(DB::raw($sqlQuery));
			if(!empty($clinic_data)){
				foreach ($clinic_data as $clinic) {
					$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
					if($notification_type == 'email'){
					// email sending process for confirm clinic starts
					$clinic_date_time		= 	new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
					$clinic_date_time->setTimezone(new DateTimeZone($admin->timezone));
					$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
					$date         			= 	$clinic_date_time->format('Y-m-d');
					$time         			= 	$clinic_date_time->format('H:i');
					$subject_replace		=   array($clinic->name,$admin->x_time_unfilled_notify);
					$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$date,$time,$clinic->location_name);
					$type 					=	'clinic_time_unfilled';
					$check_status			=	$this->CheckMailSentStatus($clinic->id,$admin->id,$type);
					
					if($check_status == 0){
						$email_send    			=   $this->mail_send('clinic_time_unfilled',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic->id,$admin->id,$type);
					}	
					// email sending process for confirm clinic ends
					}elseif($notification_type == 'push'){
						if(($admin->x_time_unfilled_notify != '' || $admin->x_time_unfilled_notify != null) && $admin->x_time_unfilled_notify != 'off'){
						$estimated_time 	= 	'+'.($admin->x_time_unfilled_notify*60).' minutes';
						}
						$start_time 			= 	date('Y-m-d H:i:s',strtotime($estimated_time,strtotime($clinic->created_at)));
						$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

						if($current_time > $start_time){
							$message = $clinic->name.' is unfilled in '.$estimated_time/60 .' hours';
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','clinic_time_unfilled')->get()->count();
							if($admin_notifications == 0){
								$this->save_admin_notification($clinic->id,'clinic','clinic_time_unfilled',$message,$admin_id);
							}
						}
					}elseif($notification_type == 'both'){
						// email sending process for confirm clinic starts
						$clinic_date_time		= 	new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
						$clinic_date_time->setTimezone(new DateTimeZone($admin->timezone));
						$date_time 				= 	$clinic_date_time->format('Y-m-d H:i');
						$date         			= 	$clinic_date_time->format('Y-m-d');
						$time         			= 	$clinic_date_time->format('H:i');
						$subject_replace		=   array($clinic->name,$admin->x_time_unfilled_notify);
						$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$date,$time,$clinic->location_name);
						$type 					=	'clinic_time_unfilled';
						$check_status			=	$this->CheckMailSentStatus($clinic->id,$admin->id,$type);
						
						if($check_status == 0){
							$email_send    			=   $this->mail_send('clinic_time_unfilled',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic->id,$admin->id,$type);
						}	
						// email sending process for confirm clinic ends
						
						// process to send web push notification starts here //
						if(($admin->x_time_unfilled_notify != '' || $admin->x_time_unfilled_notify != null) && $admin->x_time_unfilled_notify != 'off'){
						$estimated_time 	= 	'+'.($admin->x_time_unfilled_notify*60).' minutes';
						}
						$start_time 			= 	date('Y-m-d H:i:s',strtotime($estimated_time,strtotime($clinic->created_at)));
						$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

						if($current_time > $start_time){
							$message = $clinic->name.' is unfilled in '.$estimated_time/60 .' hours';
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','clinic_time_unfilled')->get()->count();
							if($admin_notifications == 0){
								$this->save_admin_notification($clinic->id,'clinic','clinic_time_unfilled',$message,$admin_id);
							}
						}
						// process to send web push notification ends here //
						
					}elseif($notification_type == 'none'){
						// no notifications goes nothing
					}	
				}
				echo "success";
			}else{
				echo "no records available";
			}
		 }
	}
	
	 	
	/**
     * function for If provider over x hours in a month for per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ProviderHoursInaMonthNotificationAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->over_hour_month_notify != '' || $admin->over_hour_month_notify != null) && $admin->over_hour_month_notify != 'off'){	
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
				if(!empty($providers_data)){
					foreach ($providers_data as $provider) {
						$default_max_hours_data = DB::table('users')->where('id',$admin_id)->get()->first();
						$default_max_hours  = $default_max_hours_data->over_hour_month_notify;
						$data = DB::table("clinic_status")
												->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
												->where('status',1)
												->where('provider_id',$provider['id'])
												->whereNotNull('clock_in')
												->whereNotNull('clock_out')
												->whereNotNull('clinic_spend_time')
												->whereMonth('clock_in',date('m'))
												->whereYear('clock_in',date('Y'))
												->first();
							if($data->total_hours){
								$total_hours = $data->total_hours/60;
								if($total_hours > $default_max_hours){
									$difference = number_format($total_hours - $default_max_hours,2);
									
									// email sending process for confirm clinic starts
									$provider_name 	=  $provider['first_name'].' '. $provider['last_name'];
									$subject_replace		=   array($provider_name);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
									$type 					=	'provider_hours_in_a_month';
									$check_status			=	$this->CheckMailSentStatus($provider['id'],$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_hours_in_a_month',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider['id'],$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
									
								}
							}
						}
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->over_hour_month_notify != '' || $admin->over_hour_month_notify != null) && $admin->over_hour_month_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$default_max_hours_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$default_max_hours  = $default_max_hours_data->over_hour_month_notify;
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereNotNull('clinic_spend_time')
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
								if($data->total_hours){
									$total_hours = $data->total_hours/60;
									if($total_hours > $default_max_hours){
										$difference = $total_hours - $default_max_hours;
										$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_hours_in_a_month')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
										if($admin_notifications == 0){
											$provider_name 	=  $provider['first_name'].' '. $provider['last_name'];
											$message 		=  $provider_name.' over '.$difference.' hours in a month';
											$this->save_admin_notification($provider['id'],'provider','provider_hours_in_a_month',$message,$admin_id);
										}
									}
								}
							}
							echo "success";
					}else{
							echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->over_hour_month_notify != '' || $admin->over_hour_month_notify != null) && $admin->over_hour_month_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$default_max_hours_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$default_max_hours  = $default_max_hours_data->over_hour_month_notify;
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereNotNull('clinic_spend_time')
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
								if($data->total_hours){
									$total_hours = $data->total_hours/60;
									if($total_hours > $default_max_hours){
										$difference = number_format($total_hours - $default_max_hours,2);
										$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_hours_in_a_month')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
										if($admin_notifications == 0){
											$provider_name 	=  $provider['first_name'].' '. $provider['last_name'];
											$message 		=  $provider_name.' over '.$difference.' hours in a month';
											$this->save_admin_notification($provider['id'],'provider','provider_hours_in_a_month',$message,$admin_id);
										}
										// email sending process for confirm clinic starts
										$provider_name 	=  $provider['first_name'].' '. $provider['last_name'];
										$subject_replace		=   array($provider_name);
										$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
										$type 					=	'provider_hours_in_a_month';
										$check_status			=	$this->CheckMailSentStatus($provider['id'],$admin->id,$type);
										
										if($check_status == 0){
											$email_send    			=   $this->mail_send('provider_hours_in_a_month',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider['id'],$admin->id,$type);
										}	
										// email sending process for confirm clinic ends
									}
								}
							}
							echo "success";
					}else{
							echo "no records available";
					}
				}
				
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
	 	}
	 }
	 /**
     * function for If provider go over x hours in a day for per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ProviderHoursInaDayNotificationAdmin(){
		 $all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->over_hour_day_notify != '' || $admin->over_hour_day_notify != null) && $admin->over_hour_day_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$max_hours_per_day_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$max_hours_per_day  	= $max_hours_per_day_data->over_hour_day_notify;
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereNotNull('clinic_spend_time')
													->whereDay('clock_in',date('d'))
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
							if($data->total_hours){
								$total_hours = $data->total_hours/60;
								if($total_hours > $max_hours_per_day){
									$difference = number_format($total_hours - $max_hours_per_day,2);
									// email sending process for confirm clinic starts
									$provider_name 	=  $provider['first_name'].' '. $provider['last_name'];
									$subject_replace		=   array($provider_name);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
									$type 					=	'provider_hours_in_a_day';
									$check_status			=	$this->CheckMailSentStatus($provider['id'],$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_hours_in_a_day',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider['id'],$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->over_hour_day_notify != '' || $admin->over_hour_day_notify != null) && $admin->over_hour_day_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$max_hours_per_day_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$max_hours_per_day  	= $max_hours_per_day_data->over_hour_day_notify;
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereNotNull('clinic_spend_time')
													->whereDay('clock_in',date('d'))
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
							if($data->total_hours){
								$total_hours = $data->total_hours/60;
								if($total_hours > $max_hours_per_day){
									$difference = number_format($total_hours - $max_hours_per_day,2);
									$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_hours_in_a_day')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name =  $provider['first_name'].' '. $provider['last_name'];
										$message 	   =  $provider_name.' over '.round($difference).' hours in a day';
										$this->save_admin_notification($provider['id'],'provider','provider_hours_in_a_day',$message,$admin_id);
									}
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->over_hour_day_notify != '' || $admin->over_hour_day_notify != null) && $admin->over_hour_day_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$max_hours_per_day_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$max_hours_per_day  	= $max_hours_per_day_data->over_hour_day_notify;
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(clinic_spend_time) as total_hours"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereNotNull('clinic_spend_time')
													->whereDay('clock_in',date('d'))
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
							if($data->total_hours){
								$total_hours = $data->total_hours/60;
								if($total_hours > $max_hours_per_day){
									$difference = number_format($total_hours - $max_hours_per_day,2);
									$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_hours_in_a_day')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name =  $provider['first_name'].' '. $provider['last_name'];
										$message 	   =  $provider_name.' over '.round($difference).' hours in a day';
										$this->save_admin_notification($provider['id'],'provider','provider_hours_in_a_day',$message,$admin_id);
									}
									// email sending process for confirm clinic starts
									$provider_name 	=  $provider['first_name'].' '. $provider['last_name'];
									$subject_replace		=   array($provider_name);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
									$type 					=	'provider_hours_in_a_day';
									$check_status			=	$this->CheckMailSentStatus($provider['id'],$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_hours_in_a_day',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider['id'],$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}		
		}
	 }
	 /**
     * function for If provider go over x hours in a clinic
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ProviderHoursInaClinicNotificationAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->over_hour_clinic_notify != '' || $admin->over_hour_clinic_notify != null) && $admin->over_hour_clinic_notify != 'off'){
				$clinics_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
										->where('clinic_status.status',1)
										->whereNotNull('clinic_status.clock_in')
										->whereNotNull('clinic_status.clock_out')
										->whereNotNull('clinic_status.clinic_spend_time')
										->whereDay('clinic_status.clock_in',date('d'))
										->whereMonth('clinic_status.clock_in',date('m'))
										->whereYear('clinic_status.clock_in',date('Y'))
										->get()->toArray();
					if(!empty($clinics_data)){
						foreach ($clinics_data as $clinics) {
							$clinic = ClinicsModel::where('status',1)->where('id', $clinics['clinic_id'])->first();
								$max_hours_data = DB::table('users')->where('id',$admin_id)->get()->first();
								$max_hours  	= $max_hours_data->over_hour_clinic_notify;
								$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
							if($clinics['clinic_spend_time']){
								if($clinics['clinic_spend_time'] > $max_hours){
									$difference = $clinics['clinic_spend_time'] - $max_hours;
									// email sending process for confirm clinic starts
									$provider_name  = 	$provider->first_name.' '. $provider->last_name;
									$subject_replace		=   array($provider_name,$clinics['name']);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$clinics['name']);
									$type 					=	'provider_hours_in_a_clinic';
									$check_status			=	$this->CheckMailSentStatus($provider->id,$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_hours_in_a_clinic',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider->id,$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->over_hour_clinic_notify != '' || $admin->over_hour_clinic_notify != null) && $admin->over_hour_clinic_notify != 'off'){
				$clinics_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
										->where('clinic_status.status',1)
										->whereNotNull('clinic_status.clock_in')
										->whereNotNull('clinic_status.clock_out')
										->whereNotNull('clinic_status.clinic_spend_time')
										->whereDay('clinic_status.clock_in',date('d'))
										->whereMonth('clinic_status.clock_in',date('m'))
										->whereYear('clinic_status.clock_in',date('Y'))
										->get()->toArray();
					if(!empty($clinics_data)){
						foreach ($clinics_data as $clinics) {
							$clinic = ClinicsModel::where('status',1)->where('id', $clinics['clinic_id'])->first();
								$max_hours_data = DB::table('users')->where('id',$admin_id)->get()->first();
								$max_hours  	= $max_hours_data->over_hour_clinic_notify;
								$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
							if($clinics['clinic_spend_time']){
								if($clinics['clinic_spend_time'] > $max_hours){
									$difference = $clinics['clinic_spend_time'] - $max_hours;
									$admin_notifications 	= 	AdminNotifications::where('required_id',$clinics['clinic_id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','provider_hours_in_a_clinic')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
										$provider_name  = 	$provider->first_name.' '. $provider->last_name;
										$message 		=  $provider_name.' over '.round($difference).' minutes in a day';
										$this->save_admin_notification($clinics['clinic_id'],'clinic','provider_hours_in_a_clinic',$message,$admin_id);
									}
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->over_hour_clinic_notify != '' || $admin->over_hour_clinic_notify != null) && $admin->over_hour_clinic_notify != 'off'){
				$clinics_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
										->where('clinic_status.status',1)
										->whereNotNull('clinic_status.clock_in')
										->whereNotNull('clinic_status.clock_out')
										->whereNotNull('clinic_status.clinic_spend_time')
										->whereDay('clinic_status.clock_in',date('d'))
										->whereMonth('clinic_status.clock_in',date('m'))
										->whereYear('clinic_status.clock_in',date('Y'))
										->get()->toArray();
					if(!empty($clinics_data)){
						foreach ($clinics_data as $clinics) {
							$clinic = ClinicsModel::where('status',1)->where('id', $clinics['clinic_id'])->first();
								$max_hours_data = DB::table('users')->where('id',$admin_id)->get()->first();
								$max_hours  	= $max_hours_data->over_hour_clinic_notify;
								if($clinics['clinic_spend_time']){
									if($clinics['clinic_spend_time'] > $max_hours){
										$difference = $clinics['clinic_spend_time'] - $max_hours;
										$admin_notifications 	= 	AdminNotifications::where('required_id',$clinics['clinic_id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','provider_hours_in_a_clinic')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
										if($admin_notifications == 0){
											$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
											$provider_name  = 	$provider->first_name.' '. $provider->last_name;
											$message 		=  $provider_name.' over '.round($difference).' minutes in a day';
											$this->save_admin_notification($clinics['clinic_id'],'clinic','provider_hours_in_a_clinic',$message,$admin_id);
										}
										// email sending process for confirm clinic starts
										$provider_name  = 	$provider->first_name.' '. $provider->last_name;
										$subject_replace		=   array($provider_name,$clinics['name']);
										$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$clinics['name']);
										$type 					=	'provider_hours_in_a_clinic';
										$check_status			=	$this->CheckMailSentStatus($provider->id,$admin->id,$type);
										
										if($check_status == 0){
											$email_send    			=   $this->mail_send('provider_hours_in_a_clinic',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider->id,$admin->id,$type);
										}	
										// email sending process for confirm clinic ends
									}
								}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
	 	}
	 }
	 /**
     * function for If provider over x mileage in a month per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ProviderMilageInaMonthNotificationAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->over_mileage_month_notify != '' || $admin->over_mileage_month_notify != null) && $admin->over_mileage_month_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$total_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$total_mileage  	= $total_mileage_data->over_mileage_month_notify;
							//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_month');
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(mileage) as total_mileage"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereMonth('created_at',date('m'))
													->whereYear('created_at',date('Y'))
													->first();
							if($data->total_mileage){
								if($data->total_mileage > $total_mileage){echo 1;die;
									$difference = $data->total_mileage - $total_mileage;
									// email sending process for confirm clinic starts
									$provider_name = $provider['first_name'].' '. $provider['last_name'];
									$subject_replace		=   array($provider_name);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
									$type 					=	'provider_mileage_in_a_month';
									$check_status			=	$this->CheckMailSentStatus($provider->id,$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_mileage_in_a_month',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider->id,$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->over_mileage_month_notify != '' || $admin->over_mileage_month_notify != null) && $admin->over_mileage_month_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$total_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$total_mileage  	= $total_mileage_data->over_mileage_month_notify;
							//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_month');
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(mileage) as total_mileage"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereMonth('created_at',date('m'))
													->whereYear('created_at',date('Y'))
													->first();
							if($data->total_mileage){
								if($data->total_mileage > $total_mileage){
									$difference = $data->total_mileage - $total_mileage;
									$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_mileage_in_a_month')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name = $provider['first_name'].' '. $provider['last_name'];
										$message =  $provider_name.' over '.$difference.' mileage in a month';
										$this->save_admin_notification($provider['id'],'provider','provider_mileage_in_a_month',$message,$admin_id);
									}
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->over_mileage_month_notify != '' || $admin->over_mileage_month_notify != null) && $admin->over_mileage_month_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$total_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$total_mileage  	= $total_mileage_data->over_mileage_month_notify;
							//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_month');
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(mileage) as total_mileage"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereMonth('created_at',date('m'))
													->whereYear('created_at',date('Y'))
													->first();
							if($data->total_mileage){
								if($data->total_mileage > $total_mileage){
									$difference = $data->total_mileage - $total_mileage;
									$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_mileage_in_a_month')->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name = $provider['first_name'].' '. $provider['last_name'];
										$message =  $provider_name.' over '.$difference.' mileage in a month';
										$this->save_admin_notification($provider['id'],'provider','provider_mileage_in_a_month',$message,$admin_id);
									}
									// email sending process for confirm clinic starts
									$provider_name = $provider['first_name'].' '. $provider['last_name'];
									$subject_replace		=   array($provider_name);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
									$type 					=	'provider_mileage_in_a_month';
									$check_status			=	$this->CheckMailSentStatus($provider->id,$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_mileage_in_a_month',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider->id,$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
	 	}
	 }
	 /**
     * function for If provider over x miles in a day per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ProviderMilageInaDayNotificationAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->over_mileage_day_notify != '' || $admin->over_mileage_day_notify != null) && $admin->over_mileage_day_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$total_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$total_mileage  	= $total_mileage_data->over_mileage_day_notify;
								//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_day');
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(mileage) as total_mileage"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereDay('clock_in',date('d'))
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
							if($data->total_mileage){
								if($data->total_mileage > $total_mileage){
									$difference = $data->total_mileage - $total_mileage;
									// email sending process for confirm clinic starts
									$provider_name = $provider['first_name'].' '. $provider['last_name'];
									$subject_replace		=   array($provider_name);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
									$type 					=	'provider_mileage_in_a_day';
									$check_status			=	$this->CheckMailSentStatus($provider['id'],$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_mileage_in_a_day',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider['id'],$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->over_mileage_day_notify != '' || $admin->over_mileage_day_notify != null) && $admin->over_mileage_day_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$total_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$total_mileage  	= $total_mileage_data->over_mileage_day_notify;
								//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_day');
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(mileage) as total_mileage"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereDay('clock_in',date('d'))
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
							if($data->total_mileage){
								if($data->total_mileage > $total_mileage){
									$difference = $data->total_mileage - $total_mileage;
									$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_mileage_in_a_day')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name = $provider['first_name'].' '. $provider['last_name'];
										$message =  $provider_name.' over '.$difference.' mileage in a day';
										$this->save_admin_notification($provider['id'],'provider','provider_mileage_in_a_day',$message,$admin_id);
									}
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->over_mileage_day_notify != '' || $admin->over_mileage_day_notify != null) && $admin->over_mileage_day_notify != 'off'){
				$providers_data = ProvidersModel::where('status',1)->where('role_id', 0)->get()->toArray();
					if(!empty($providers_data)){
						foreach ($providers_data as $provider) {
							$total_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
							$total_mileage  	= $total_mileage_data->over_mileage_day_notify;
								//$total_mileage 		= $this->GetAdminSettingsValue('default_max_mileage_per_day');
							$data = DB::table("clinic_status")
													->select(DB::raw("SUM(mileage) as total_mileage"))
													->where('status',1)
													->where('provider_id',$provider['id'])
													->whereNotNull('clock_in')
													->whereNotNull('clock_out')
													->whereDay('clock_in',date('d'))
													->whereMonth('clock_in',date('m'))
													->whereYear('clock_in',date('Y'))
													->first();
							if($data->total_mileage){
								if($data->total_mileage > $total_mileage){
									$difference = $data->total_mileage - $total_mileage;
									$admin_notifications 	= 	AdminNotifications::where('required_id',$provider['id'])->where('user_id',$admin_id)->where('type','provider')->where('notification_type','provider_mileage_in_a_day')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name = $provider['first_name'].' '. $provider['last_name'];
										$message =  $provider_name.' over '.$difference.' mileage in a day';
										$this->save_admin_notification($provider['id'],'provider','provider_mileage_in_a_day',$message,$admin_id);
									}
									// email sending process for confirm clinic starts
									$provider_name = $provider['first_name'].' '. $provider['last_name'];
									$subject_replace		=   array($provider_name);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$provider['email']);
									$type 					=	'provider_mileage_in_a_day';
									$check_status			=	$this->CheckMailSentStatus($provider['id'],$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_mileage_in_a_day',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$provider['id'],$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
	 	}
	 }
	 /**
     * function for If provider goes over x mileage in a clinic per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ProviderMilageInaClinicNotificationAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->over_mileage_clinic_notify != '' || $admin->over_mileage_clinic_notify != null) && $admin->over_mileage_clinic_notify != 'off'){
				$clinics_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
										->where('clinic_status.status',1)
										->whereNotNull('clinic_status.clock_in')
										->whereNotNull('clinic_status.clock_out')
										->whereDay('clinic_status.clock_in',date('d'))
										->whereMonth('clinic_status.clock_in',date('m'))
										->whereYear('clinic_status.clock_in',date('Y'))
										->get()->toArray();
					if(!empty($clinics_data)){
						foreach ($clinics_data as $clinics) {
							$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
								$max_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
								$max_mileage  	= $max_mileage_data->over_mileage_clinic_notify;
								//$max_mileage 	= 	$this->GetAdminSettingsValue('default_max_mileage_per_clinic');
							if($max_mileage){
								if($clinics['mileage'] > $max_mileage){
									$difference = $clinics['mileage'] - $max_mileage;
									// email sending process for confirm clinic starts
									$provider_name  = 	$provider->first_name.' '. $provider->last_name;
									$subject_replace		=   array($provider_name,$clinics['name']);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$clinics['name']);
									$type 					=	'provider_mileage_in_a_clinic';
									$check_status			=	$this->CheckMailSentStatus($clinics['clinic_id'],$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_mileage_in_a_clinic',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinics['clinic_id'],$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->over_mileage_clinic_notify != '' || $admin->over_mileage_clinic_notify != null) && $admin->over_mileage_clinic_notify != 'off'){
				$clinics_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
										->where('clinic_status.status',1)
										->whereNotNull('clinic_status.clock_in')
										->whereNotNull('clinic_status.clock_out')
										->whereDay('clinic_status.clock_in',date('d'))
										->whereMonth('clinic_status.clock_in',date('m'))
										->whereYear('clinic_status.clock_in',date('Y'))
										->get()->toArray();
					if(!empty($clinics_data)){
						foreach ($clinics_data as $clinics) {
							$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
								$max_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
								$max_mileage  	= $max_mileage_data->over_mileage_clinic_notify;
								//$max_mileage 	= 	$this->GetAdminSettingsValue('default_max_mileage_per_clinic');
							if($max_mileage){
								if($clinics['mileage'] > $max_mileage){
									$difference = $clinics['mileage'] - $max_mileage;
									$admin_notifications 	= 	AdminNotifications::where('required_id',$clinics['clinic_id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','provider_mileage_in_a_clinic')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name  = 	$provider->first_name.' '. $provider->last_name;
										$message 		=   $provider_name.' over '.round($difference).' mileage in a clinic';
										$this->save_admin_notification($clinics['clinic_id'],'clinic','provider_mileage_in_a_clinic',$message,$admin_id);
									}
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->over_mileage_clinic_notify != '' || $admin->over_mileage_clinic_notify != null) && $admin->over_mileage_clinic_notify != 'off'){
				$clinics_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
										->where('clinic_status.status',1)
										->whereNotNull('clinic_status.clock_in')
										->whereNotNull('clinic_status.clock_out')
										->whereDay('clinic_status.clock_in',date('d'))
										->whereMonth('clinic_status.clock_in',date('m'))
										->whereYear('clinic_status.clock_in',date('Y'))
										->get()->toArray();
					if(!empty($clinics_data)){
						foreach ($clinics_data as $clinics) {
							$provider 		= 	ProvidersModel::where('id',$clinics['provider_id'])->first();
								$max_mileage_data = DB::table('users')->where('id',$admin_id)->get()->first();
								$max_mileage  	= $max_mileage_data->over_mileage_clinic_notify;
								//$max_mileage 	= 	$this->GetAdminSettingsValue('default_max_mileage_per_clinic');
							if($max_mileage){
								if($clinics['mileage'] > $max_mileage){
									$difference = $clinics['mileage'] - $max_mileage;
									$admin_notifications 	= 	AdminNotifications::where('required_id',$clinics['clinic_id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','provider_mileage_in_a_clinic')->whereDay('created_at',date('d'))->whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->get()->count();
									if($admin_notifications == 0){
										$provider_name  = 	$provider->first_name.' '. $provider->last_name;
										$message 		=   $provider_name.' over '.round($difference).' mileage in a clinic';
										$this->save_admin_notification($clinics['clinic_id'],'clinic','provider_mileage_in_a_clinic',$message,$admin_id);
									}
									// email sending process for confirm clinic starts
									$provider_name  = 	$provider->first_name.' '. $provider->last_name;
									$subject_replace		=   array($provider_name,$clinics['name']);
									$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$provider_name,$difference,$clinics['name']);
									$type 					=	'provider_mileage_in_a_clinic';
									$check_status			=	$this->CheckMailSentStatus($clinics['clinic_id'],$admin->id,$type);
									
									if($check_status == 0){
										$email_send    			=   $this->mail_send('provider_mileage_in_a_clinic',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinics['clinic_id'],$admin->id,$type);
									}	
									// email sending process for confirm clinic ends
								}
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
	 	}
	}
	/**
     * function for Clinic status is pending mileage info per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClinicPendingMileageNotificationsAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->mileage_info_notify != '' || $admin->mileage_info_notify != null) && $admin->mileage_info_notify != 'off'){
				$clinic_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
											->where('clinic_status.status',1)
											->where('clinic_status.mileage',0)
											->whereNotNull('clinic_status.clock_in')
											->whereNotNull('clinic_status.clock_out')
											->get()->toArray();
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							// email sending process for confirm clinic starts
							$subject_replace		=   array($clinic['name']);
							$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$clinic['name'],$clinic['date'],$clinic['location_name']);
							$type 					=	'pending_mileage';
							$check_status			=	$this->CheckMailSentStatus($clinic['id'],$admin->id,$type);
							
							if($check_status == 0){
								$email_send    			=   $this->mail_send('pending_mileage',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic['id'],$admin->id,$type);
							}	
							// email sending process for confirm clinic ends
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->mileage_info_notify != '' || $admin->mileage_info_notify != null) && $admin->mileage_info_notify != 'off'){
				$clinic_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
											->where('clinic_status.status',1)
											->where('clinic_status.mileage',0)
											->whereNotNull('clinic_status.clock_in')
											->whereNotNull('clinic_status.clock_out')
											->get()->toArray();
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic['id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','pending_mileage')->get()->count();
							if($admin_notifications == 0){
								$message = $clinic['name'].' status is pending mileage info';
								$this->save_admin_notification($clinic['id'],'clinic','pending_mileage',$message,$admin_id);
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->mileage_info_notify != '' || $admin->mileage_info_notify != null) && $admin->mileage_info_notify != 'off'){
				$clinic_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
											->where('clinic_status.status',1)
											->where('clinic_status.mileage',0)
											->whereNotNull('clinic_status.clock_in')
											->whereNotNull('clinic_status.clock_out')
											->get()->toArray();
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic['id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','pending_mileage')->get()->count();
							if($admin_notifications == 0){
								$message = $clinic['name'].' status is pending mileage info';
								$this->save_admin_notification($clinic['id'],'clinic','pending_mileage',$message,$admin_id);
							}
							// email sending process for confirm clinic starts
							$subject_replace		=   array($clinic['name']);
							$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$clinic['name'],$clinic['date'],$clinic['location_name']);
							$type 					=	'pending_mileage';
							$check_status			=	$this->CheckMailSentStatus($clinic['id'],$admin->id,$type);
							
							if($check_status == 0){
								$email_send    			=   $this->mail_send('pending_mileage',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic['id'],$admin->id,$type);
							}	
							// email sending process for confirm clinic ends
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
	 	}
	 }
	 /**
     * function for Clinic has been filled Notification for per admin.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClinicFilledNotificationsAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->clinic_filled_notify != '' || $admin->clinic_filled_notify != null) && $admin->clinic_filled_notify != 'off'){
				$sqlQuery 			= 	"SELECT * FROM clinics WHERE `personnel` <= (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id) ";
					$clinic_data = DB::select(DB::raw($sqlQuery));
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							$reminder_time 			= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
							$estimated_time 		= 	'+'.($reminder_time*60).' minutes';
							$start_time 			= 	strtotime($estimated_time,strtotime($clinic->created_at));
							$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

							// email sending process for confirm clinic starts
							$subject_replace		=  array($clinic->name);
							$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$clinic->name,$clinic->date,$clinic->location_name);
							$type 					=	'clinic_filled';
							$check_status			=	$this->CheckMailSentStatus($clinic->id,$admin->id,$type);
							
							if($check_status == 0){
								$email_send    			=   $this->mail_send('clinic_filled',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic->id,$admin->id,$type);
							}	
							// email sending process for confirm clinic ends

							}
							echo "success";
						}else{
							echo "no records available";
						}
				}
			}elseif($notification_type == 'push'){
				if(($admin->clinic_filled_notify != '' || $admin->clinic_filled_notify != null) && $admin->clinic_filled_notify != 'off'){
				$sqlQuery 			= 	"SELECT * FROM clinics WHERE `personnel` <= (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id) ";
					$clinic_data = DB::select(DB::raw($sqlQuery));
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							$reminder_time 			= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
							$estimated_time 		= 	'+'.($reminder_time*60).' minutes';
							$start_time 			= 	strtotime($estimated_time,strtotime($clinic->created_at));
							$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

								//if($current_time > $start_time){
									$message = $clinic->name.' clinic has been filled';
									$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','clinic_filled')->get()->count();
									if($admin_notifications == 0){
										$this->save_admin_notification($clinic->id,'clinic','clinic_filled',$message,$admin_id);
									}
								//}

							}
							echo "success";
						}else{
							echo "no records available";
						}
				}
			}elseif($notification_type == 'both'){
				if(($admin->clinic_filled_notify != '' || $admin->clinic_filled_notify != null) && $admin->clinic_filled_notify != 'off'){
				$sqlQuery 			= 	"SELECT * FROM clinics WHERE `personnel` <= (SELECT COUNT(id) as total from clinic_status WHERE status = 1 AND clinic_id = clinics.id) ";
					$clinic_data = DB::select(DB::raw($sqlQuery));
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							$reminder_time 			= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
							$estimated_time 		= 	'+'.($reminder_time*60).' minutes';
							$start_time 			= 	strtotime($estimated_time,strtotime($clinic->created_at));
							$current_time	 		= 	strtotime(date('Y-m-d H:i:s'));

							$message = $clinic->name.' clinic has been filled';
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic->id)->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','clinic_filled')->get()->count();
							if($admin_notifications == 0){
								$this->save_admin_notification($clinic->id,'clinic','clinic_filled',$message,$admin_id);
							}
							// email sending process for confirm clinic starts
							$subject_replace		=  array($clinic->name);
							$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$clinic->name,$clinic->date,$clinic->location_name);
							$type 					=	'clinic_filled';
							$check_status			=	$this->CheckMailSentStatus($clinic->id,$admin->id,$type);
							
							if($check_status == 0){
								$email_send    			=   $this->mail_send('clinic_filled',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic->id,$admin->id,$type);
							}	
							// email sending process for confirm clinic ends

							}
							echo "success";
						}else{
							echo "no records available";
						}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
		}
	 }
	/**
     * function for Clinic status is complete per admin
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClinicStatusCompleteNotificationsAdmin(){
		$all_admins   = DB::table('users')->where('role_id',1)->get();
		foreach($all_admins as $admin){
			$admin_id     = $admin->id;
			$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
			if($notification_type == 'email'){
				if(($admin->clinic_status_notify != '' || $admin->clinic_status_notify != null) && $admin->clinic_status_notify != 'off'){
				$clinic_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
													->where('clinic_status.status',1)
													->where('clinic_status.mileage', '!=', 0)
													->where('clinic_status.drive_time','!=', 0)
													->whereNotNull('clinic_status.clock_in')
													->whereNotNull('clinic_status.clock_out')
													->get()->toArray();
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							// email sending process for confirm clinic starts
							$subject_replace		=  array($clinic['name']);
							$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$clinic['name'],$clinic['date'],$clinic['location_name']);
							$type 					=	'complete';
							$check_status			=	$this->CheckMailSentStatus($clinic['id'],$admin->id,$type);
							
							if($check_status == 0){
								$email_send    			=   $this->mail_send('complete',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic['id'],$admin->id,$type);
							}	
							// email sending process for confirm clinic ends
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'push'){
				if(($admin->clinic_status_notify != '' || $admin->clinic_status_notify != null) && $admin->clinic_status_notify != 'off'){
				$clinic_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
													->where('clinic_status.status',1)
													->where('clinic_status.mileage', '!=', 0)
													->where('clinic_status.drive_time','!=', 0)
													->whereNotNull('clinic_status.clock_in')
													->whereNotNull('clinic_status.clock_out')
													->get()->toArray();
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic['id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','complete')->get()->count();
							if($admin_notifications == 0){
								$message = $clinic['name'].' status is complete';
								$this->save_admin_notification($clinic['id'],'clinic','complete',$message,$admin_id);
							}
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'both'){
				if(($admin->clinic_status_notify != '' || $admin->clinic_status_notify != null) && $admin->clinic_status_notify != 'off'){
				$clinic_data = ClinicsStatusModel::join('clinics','clinics.id','=','clinic_status.clinic_id')
													->where('clinic_status.status',1)
													->where('clinic_status.mileage', '!=', 0)
													->where('clinic_status.drive_time','!=', 0)
													->whereNotNull('clinic_status.clock_in')
													->whereNotNull('clinic_status.clock_out')
													->get()->toArray();
					if(!empty($clinic_data)){
						foreach ($clinic_data as $clinic) {
							$admin_notifications 	= 	AdminNotifications::where('required_id',$clinic['id'])->where('user_id',$admin_id)->where('type','clinic')->where('notification_type','complete')->get()->count();
							if($admin_notifications == 0){
								$message = $clinic['name'].' status is complete';
								$this->save_admin_notification($clinic['id'],'clinic','complete',$message,$admin_id);
							}
							// email sending process for confirm clinic starts
							$subject_replace		=   array($clinic['name']);
							$replace_array 			=   array($admin->first_name.' '.$admin->last_name,$clinic['name'],$clinic['date'],$clinic['location_name']);
							$type 					=	'complete';
							$check_status			=	$this->CheckMailSentStatus($clinic['id'],$admin->id,$type);
							
							if($check_status == 0){
								$email_send    			=   $this->mail_send('complete',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_array,null,$clinic['id'],$admin->id,$type);
							}	
							// email sending process for confirm clinic ends
						}
						echo "success";
					}else{
						echo "no records available";
					}
				}
			}elseif($notification_type == 'none'){
				// no notifications goes nothing
			}	
	 	}
	 }
	 // delete notification //
	 public function delete_notifications(){
       $notification_id = Input::get('notification_id');
        $delete_notifications = AdminNotifications::where('id', $notification_id)->delete();
        if($delete_notifications){
          echo 1;
        }else{
          echo 0;
        }
     }
	 // count user unread total messages notification //
	 public function count_user_unread_notification(){
       $user_id = Input::get('user_id');
        $unread_notifications = DB::table('admin_notifications')->where('user_id',$user_id)->where('admin_views',0)->get()->count();
		  if(!empty($unread_notifications)){
			return $unread_notifications;
		  }else{
			return 0;
		  }
     }
	 public function time_elapsed_string($datetime, $full = false) {
		$now = new DateTime;
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = array(
			'y' => 'year',
			'm' => 'month',
			'w' => 'week',
			'd' => 'day',
			'h' => 'hour',
			'i' => 'minute',
			's' => 'second',
		);
		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}
	  public function loadMore(Request $request){
       $notications =DB::table('admin_notifications')->where('user_id',Auth::user()->id)->where('admin_views',0)->paginate(4);
        $html='';
        foreach ($notications as $notification) {
			$data = '<li id="notification_li_'.$notification->id.'">
						<a href="'.URL::route('notification_details',$notification->id).'"><i class="mdi-social-notifications"></i> '.$notification->message.' </a> <i class="mdi-action-delete notification_button" style="height:5px;line-height:5px;float:right;font-size:20px;" data-id="'.$notification->id.'"></i>
						<time class="media-meta" datetime="2015-06-12T20:50:48+08:00">
								'.$this->time_elapsed_string($notification->created_at).'
						</time>
					  </li>';
            $html.=$data;
        }
        if ($request->ajax()) {
            return $html;
        }
        return view('admin.notifications.loadmore',compact('notications'));
    }
	
	public function update_notifications(){
	    $offset = 0;
	    $limit  = 4;
		$notications = AdminNotifications::where('user_id',Input::get('user_id'))->where('admin_views',0)->offset($offset)->limit($limit)->get()->toArray();
        if(!empty($notications)){
			foreach ($notications as $notification) {
				AdminNotifications::where('id',$notification['id'])->update(['admin_views' => 1]);
			}
			return 1;
		}
    }
	
	public function user_notifications(){
	    $offset = 0;
	    $limit  = Input::get('limit');
		$notications = AdminNotifications::where('user_id',Input::get('user_id'))->where('admin_views',0)->offset($offset)->limit($limit)->get()->toArray();
        $data = '';
		if(!empty($notications)){
			foreach ($notications as $notification) {
				$data .= '<li id="notification_li_'.$notification['id'].'">
							<a href="'.URL::route('notification_details',$notification['id']).'"><i class="mdi-social-notifications"></i> '.$notification['message'].' </a> <i class="mdi-action-delete notification_button" onclick="notification_delete(this)" style="height:5px;line-height:5px;float:right;font-size:20px;" data-id="'.$notification['id'].'"></i>
							<time class="media-meta" datetime="2015-06-12T20:50:48+08:00">
									'.$this->time_elapsed_string($notification['created_at']).'
							</time>
						  </li>';
			}
			$total = AdminNotifications::where('user_id',Input::get('user_id'))->where('admin_views',0)->count();
			$new_limit = $limit+4;
			echo $data."~".$limit."~".$new_limit."~".$total;
		}
    }
	public function user_notifications_and_update(){
	    $offset  	= Input::get('offset');
	    $limit  	= 4;
	    $user_id  	= Input::get('user_id');
		$notifications = AdminNotifications::where('user_id',$user_id)->where('admin_views',0)->limit($limit)->get()->toArray();
        $data = '';
		if(!empty($notifications)){
			foreach ($notifications as $notification) {
				$data .= '<li id="notification_li_'.$notification['id'].'">
							<a href="'.URL::route('notification_details',$notification['id']).'"><i class="mdi-social-notifications"></i> '.$notification['message'].' </a> <i class="mdi-action-delete notification_button" onclick="notification_delete(this)"  style="height:5px;line-height:5px;float:right;font-size:20px;" data-id="'.$notification['id'].'"></i>
							<time class="media-meta" datetime="2015-06-12T20:50:48+08:00">
									'.$this->time_elapsed_string($notification['created_at']).'
							</time>
						  </li>';
				AdminNotifications::where('id',$notification['id'])->update(['admin_views' => 1]);
			}
			$total = AdminNotifications::where('user_id',Input::get('user_id'))->where('admin_views',0)->count();
			$offset = $limit+4;
			$stop_ajax_on_hover = 1;
			echo $data."~".$limit."~".$offset."~".$stop_ajax_on_hover."~".$total;
		}
    }
}
