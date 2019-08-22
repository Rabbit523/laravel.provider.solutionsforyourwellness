<?php
	namespace App\Http\Controllers\admin;
	//use Illuminate\Http\Request;
	use App\Http\Controllers\BaseController;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\admin\Admin;
	use App\Model\admin\ProvidersModel;
	use App\Model\admin\UserAdminModel;
	use App\Model\admin\AnnouncementModel;
	use App\Model\admin\ClinicsStatusModel;
	use App\Model\admin\CertificationModel;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	//use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
	use DateTime,DateTimeZone;

	class AdminPagesController extends BaseController {

		/**
		* Function for display admin dashboard
		*
		* @param null
		*
		* @return view dashboard page.
		*/
		public function showdashboard(){
			
				$config_date 		= Config::get('date_format.date');
				$config_month 		= Config::get('date_format.month');
				$config_year 		= Config::get('date_format.year');
				$config_separator 	= Config::get('date_format.separator');
			
				$certifications =   DB::table('certifications')
														->leftjoin('users', function ($join) {
																$join->on('users.id', '=', 'certifications.user_id');
														})
														->orderBy('certifications.certificate_id','desc')->limit(4)
														->get()->toArray();	
				$announcements =  DB::table('announcement')
							  ->orderBy('announcement.id','desc')
							  ->limit(4)->get();
		
			// display providers that clockout late starts here //
				$late_clockout_providers = DB::table('clinic_status')
															->join('clinics', function ($join) {
																	$join->on('clinics.id', '=', 'clinic_status.clinic_id');
															})
															->join('users', function ($join) {
																	$join->on('clinic_status.provider_id', '=', 'users.id');
															})
															->whereNotNull('clinic_status.clock_in')->whereNotNull('clinic_status.clock_out')->where('clinic_status.status',1)->orderBy('clinic_status.id','DESC')
															->get()->toArray();
															//prd($late_clockout_providers);

			if(!empty($late_clockout_providers)){
				foreach ($late_clockout_providers as $late_clockout_provider) {
				$clinic_date 				=  	$late_clockout_provider->date;
				$clinic_end_time 			=	$late_clockout_provider->end_time;
				$clinic_end_date_time 		=	strtotime($clinic_date.' '.$clinic_end_time);
				$clock_out_time  			=  	strtotime($late_clockout_provider->clock_out);
					if($clock_out_time>$clinic_end_date_time){
						$late_clockout_provider->date = date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($late_clockout_provider->date));
						$late_clockout_provider->clock_in = date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($late_clockout_provider->clock_in));
						$late_clockout_provider->clock_out = date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($late_clockout_provider->clock_out));
						$late_clockout_data[]  = $late_clockout_provider;
					}
				}
			}
			
			// display providers that clockout late end here //

			// display providers with no or wrong location starts here //
				$empty_location =  DB::table('users')->where('role_id',0)->where('latitude','')->orWhere('longitude','')->orderBy('users.id','desc')
				->get()->toArray();
				
			
			$empty_location_users = isset($empty_location)?$empty_location:'no user found';
			//prd($empty_location_users);
			
			// display providers with no or wrong location ends here //

			// display unfilled clinics starts here //
				$all_clinics =  DB::table('clinics')->orderBy('clinics.id','desc')
				->get();
				
				$current_time               =	date('Y-m-d H:i:s');
			foreach ($all_clinics as $clinic) {
				if($clinic->default_unfilled_time != '' || $clinic->default_unfilled_time != null){
					$default_unfilled_time = $clinic->default_unfilled_time;
				}else{
					$default_unfilled_time 		= 	$this->GetAdminSettingsValue('default_time_stay_in_feeds');
				}				
				$time_addition				= 	'+'.$default_unfilled_time.' hour';
				$check_status_count 		= 	DB::table('clinic_status')->where('clinic_id',$clinic->id)->where('status',1)->get()->count();
				$clinic_time      			=   date('Y-m-d H:i:s',strtotime($clinic->date.' '.$clinic->time));
				$clinic_created      		=   date('Y-m-d H:i:s',strtotime($clinic->created_at));
				$time_after_addition 		= 	strtotime($time_addition,strtotime($clinic_created));
				

				if(($check_status_count == 0) && ($time_after_addition <= time()) && ($current_time < $clinic_time)){
					$clinic->date 		= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic->date));
					$clinic->created_at = date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($clinic->created_at));
					$clinics[] = $clinic;
				}
			}
			
			$unfilled_clinics = isset($clinics)?$clinics:'';
			// display unfilled clinics ends here //


			$month_number	=	date('m',strtotime(date('F')));
			$last_month 	= 	date("m",strtotime("-1 month"));
			$year			=	date('Y',strtotime(date('Y')));

			// provider graph starts here //
			$current_month_provider_data 		= 	ProvidersModel::
												 where('role_id',0)
												 ->whereMonth('created_at', date('m'))
												 ->whereYear('created_at', date('Y'))
												 ->get()->toArray();

			$first_date			=	$this->firstDay($month_number);
			$last_date			=	$this->lastDay($month_number);

			$last_month_provider_data 		= 	ProvidersModel::
												 where('role_id',0)
												 ->whereMonth('created_at', $last_month)
												 ->whereYear('created_at', date('Y'))
												 ->get()->toArray();
												 
			$last_month_first_date 			=	$this->firstDay($last_month);
			$last_month_last_date 			=	$this->lastDay($last_month);

			 $current_date			=	date('Y-m-d',time());
			 if(!empty($current_month_provider_data)){
				 while (strtotime($first_date) <= strtotime($last_date)) {
					 $current_month_days_data[] = $this->AdminGraphData($first_date,$current_date,$month_number,$year);
					 $first_date  = date ("Y-m-d", strtotime("+1 days", strtotime($first_date)));
				 }
			 }
			 if(!empty($last_month_provider_data)){
				 while (strtotime($last_month_first_date) <= strtotime($last_month_last_date)) {
					 $last_month_days_data[] = $this->AdminGraphData($last_month_first_date,$current_date,$last_month,$year);
					 $last_month_first_date  = date ("Y-m-d", strtotime("+1 days", strtotime($last_month_first_date)));
				 }
			}
			 $total_last_data = isset($last_month_days_data)?array_sum($last_month_days_data):0;
			 $total_current_data = isset($current_month_days_data)?array_sum($current_month_days_data):0;
			 if($total_current_data != 0){
			 	$provider_diffrence = (($total_current_data-$total_last_data)/$total_current_data*100);
			 }else{
			 	$provider_diffrence = 0;
			 }
			 //prd($total_current_data);
			// $provider_diffrences = (($total_current_data-$total_last_data)/$total_current_data*100);
			// $provider_diffrence = isset($provider_diffrences)?$provider_diffrences:0;
		 // provider graph data ends here //

		 // certificates graph starts here //
			$current_month_certificate_data 	= 	CertificationModel::
													whereMonth('created_at', date('m'))
													->whereYear('created_at', date('Y'))
													->get()->toArray();

			$first_date_certificate				=	$this->firstDay($month_number);
			$last_date_certificate				=	$this->lastDay($month_number);

			$last_month_certificate_data 			= 	CertificationModel::
														whereMonth('created_at', $last_month)
														->whereYear('created_at', date('Y'))
														->get()->toArray();
		 $last_month_first_date_certificate 		=	$this->firstDay($last_month);
		 $last_month_last_date_certificate 			=	$this->lastDay($last_month);

		$current_date_certificate					=	date('Y-m-d',time());
		if(!empty($current_month_certificate_data)){
			while (strtotime($first_date_certificate) <= strtotime($last_date_certificate)) {
				$current_month_certificates[] = $this->AdminCertificateGraphData($first_date_certificate,$current_date_certificate,$month_number,$year);
				$first_date_certificate  = date ("Y-m-d", strtotime("+1 days", strtotime($first_date_certificate)));
			}
		}
		if(!empty($last_month_certificate_data)){
			while (strtotime($last_month_first_date_certificate) <= strtotime($last_month_last_date_certificate)) {
				$last_month_certificates[] = $this->AdminCertificateGraphData($last_month_first_date_certificate,$current_date_certificate,$last_month,$year);
				$last_month_first_date_certificate  = date ("Y-m-d", strtotime("+1 days", strtotime($last_month_first_date_certificate)));
			}
		}
		$total_last_certificate_data		 = isset($last_month_certificates)?array_sum($last_month_certificates):0;
		$total_current_certificate_data	 = isset($current_month_certificates)?array_sum($current_month_certificates):0;
		if($total_current_certificate_data != 0){
			 	$certificate_diffrence = (($total_current_certificate_data-$total_last_certificate_data)/$total_current_certificate_data*100);
			 }else{
			 	$certificate_diffrence = 0;
			 }
		
		// certificates graph data ends here //


		// announcement graph starts here //
		$current_month_announ_data 		= 	AnnouncementModel::
											 whereMonth('created_at', date('m'))
											 ->whereYear('created_at', date('Y'))
											 ->get()->toArray();

	 $first_date_announ					=	$this->firstDay($month_number);
	 $last_date_announ					=	$this->lastDay($month_number);

	 $last_month_announ_data 			= 	AnnouncementModel::
											 whereMonth('created_at', $last_month)
											 ->whereYear('created_at', date('Y'))
											 ->get()->toArray();
	$last_month_first_date_announ 		=	$this->firstDay($last_month);
	$last_month_last_date_announ 		=	$this->lastDay($last_month);

	 $current_date_announ				=	date('Y-m-d',time());
	 if(!empty($current_month_announ_data)){
		 while (strtotime($first_date_announ) <= strtotime($last_date_announ)) {
			 $current_month_announs[] = $this->AdminAnnouncementGraphData($first_date_announ,$current_date_announ,$month_number,$year);
			 $first_date_announ  = date("Y-m-d", strtotime("+1 days", strtotime($first_date_announ)));
		 }
	 }
	 if(!empty($last_month_announ_data)){
		 while (strtotime($last_month_first_date_announ) <= strtotime($last_month_last_date_announ)) {
			 $last_month_announs[] = $this->AdminAnnouncementGraphData($last_month_first_date_announ,$current_date_announ,$last_month,$year);
			 $last_month_first_date_announ  = date("Y-m-d", strtotime("+1 days", strtotime($last_month_first_date_announ)));
		 }
	 }
	 $total_last_announ_data		 = isset($last_month_announs)?array_sum($last_month_announs):0;
	 $total_current_announ_data	 = isset($current_month_announs)?array_sum($current_month_announs):0;
	 if($total_current_announ_data != 0){
		$announcement_difference = (($total_current_announ_data-$total_last_announ_data)/$total_current_announ_data*100);
	 }else{
		$announcement_difference = 0;
	 }
	 
	 // announcement graph data ends here //
	 
	 // admin graph starts here //
			$current_month_admin_data 		= 	ProvidersModel::
												 where('role_id',2)
												 ->whereMonth('created_at', date('m'))
												 ->whereYear('created_at', date('Y'))
												 ->get()->toArray();
												 
			
			$first_date_admin			=	$this->firstDay($month_number);
			$last_date_admin			=	$this->lastDay($month_number);

			$last_month_admin_data 		= 	ProvidersModel::
												 where('role_id',2)
												 ->whereMonth('created_at', $last_month)
												 ->whereYear('created_at', date('Y'))
												 ->get()->toArray();
												 
												 
			$last_month_first_date_admin 			=	$this->firstDay($last_month);
			$last_month_last_date_admin 			=	$this->lastDay($last_month);

			 $current_date			=	date('Y-m-d',time());
			 if(!empty($current_month_admin_data)){
				 while (strtotime($first_date_admin) <= strtotime($last_date_admin)) {
					 $current_month_days_data_admin[] = $this->UserAdminsGraphData($first_date_admin,$current_date,$month_number,$year);
					 $first_date_admin  = date ("Y-m-d", strtotime("+1 days", strtotime($first_date_admin)));
				 }
			 }
			 if(!empty($last_month_admin_data)){
				 while (strtotime($last_month_first_date_admin) <= strtotime($last_month_last_date_admin)) {
					 $last_month_days_data_admin[] = $this->UserAdminsGraphData($last_month_first_date_admin,$current_date,$last_month,$year);
					 $last_month_first_date_admin  = date ("Y-m-d", strtotime("+1 days", strtotime($last_month_first_date_admin)));
				 }
			}
			
			 $total_last_data_admin = isset($last_month_days_data_admin)?array_sum($last_month_days_data_admin):0;
			 $total_current_data_admin = isset($current_month_days_data_admin)?array_sum($current_month_days_data_admin):0;
			 if($total_current_data_admin != 0){
				$admin_diffrence = (($total_current_data_admin-$total_last_data_admin)/$total_current_data_admin*100);
			 }else{
				$admin_diffrence = 0;
			 }
			 
		 // admin graph data ends here //

	return  View::make('admin.dashboard',compact('certifications','announcements','current_month_days_data','provider_diffrence','current_month_certificates','certificate_diffrence','current_month_announs','announcement_difference','late_clockout_data','empty_location_users','unfilled_clinics','current_month_days_data_admin','admin_diffrence'));
	}
	public function AdminGraphData($date,$current_date,$month_number,$year){
 		$showing_date 	= 	date('j',strtotime($date));
 		$day_name 		= 	date('D',strtotime($date));
 			$provider_data  = 	ProvidersModel::
 								 where('role_id',0)
 								->whereDate('created_at',$date)
 								->get()->count();

 		return $provider_data;
 	}
	public function UserAdminsGraphData($date,$current_date,$month_number,$year){
 		$showing_date 	= 	date('j',strtotime($date));
 		$day_name 		= 	date('D',strtotime($date));
 			$provider_data  = 	ProvidersModel::
 								 where('role_id',2)
 								->whereDate('created_at',$date)
 								->get()->count();

 		return $provider_data;
 	}
	public function UserAdminGraphData($date,$current_date,$month_number,$year){
		 $showing_date 	= 	date('j',strtotime($date));
		 $day_name 		= 	date('D',strtotime($date));
			 $provider_data  = 	ProvidersModel::
									where('role_id',2)
								 ->whereDate('created_at',$date)
								 ->get()->count();

		 return $provider_data;
	}
	public function AdminCertificateGraphData($date,$current_date,$month_number,$year){
		 $showing_date 	= 	date('j',strtotime($date));
		 $day_name 		= 	date('D',strtotime($date));
			 $certificate_data  = 	CertificationModel::
									whereDate('created_at',$date)
								 ->get()->count();

		 return $certificate_data;
	}
	public function AdminAnnouncementGraphData($date,$current_date,$month_number,$year){
		$showing_date 	= 	date('j',strtotime($date));
		$day_name 		= 	date('D',strtotime($date));
			$certificate_data  = 	AnnouncementModel::
								 whereDate('created_at',$date)
								->get()->count();

		return $certificate_data;
	}
	public function UpdateClinicStatus($provider_id,$clinic_id){
		if(Input::isMethod('post')){
			$rules = array(
			'clock_in'    	=> 'required',
			'clock_out'  	=> 'required',
			);
		$validator = Validator::make(Input::all(),$rules);
		 if ($validator->fails()) {
			$messages = $validator->messages();
			return Redirect::back()->withErrors($validator)->withInput();
		  } else {
				$id					=	Input::get('clinic_status_id');
				$model				=	ClinicsStatusModel::find($id);
				$model->clock_in	=	Input::get('clock_in');
				$model->clock_out	=	Input::get('clock_out');
				$update				=	$model->save();
				if($update){
					Toast::success('Clinic Status successfully updated!');
					return redirect()->route('admindashboard');
				}else{
					Toast::error('Technical error please try again later!');
					return redirect()->route('admindashboard');
				}
			}	
	   }else{
			$clinic = ClinicsStatusModel::where('provider_id',$provider_id)
					  ->where('clinic_id',$clinic_id)->first();	  
			return view('admin.clinicstatus.edit',compact('clinic'));
	   }
	}
}
