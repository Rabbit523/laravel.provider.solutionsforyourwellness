<?php
	namespace App\Http\Controllers\front;
	use App\Http\Controllers\BaseController;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\Pages_model;
	use App\Model\User_model;
	use App\Model\ProviderModel;
	use App\Model\ClinicsModel;
	use App\Model\ClinicStatusModel;
	use App\Model\EmailAction;
	use App\Model\EmailTemplate;
	use App\Model\EmailLog;
	use App\Model\ApiTokens;
	use Faker\Factory as Faker;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,DateTime;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	//use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
	use App\Http\Helpers;

class TimesheetController extends BaseController{
	/**
		* function for get timesheet of a provider.
		*
		* @param null
		*
		* @return response data on success otherwise error.
		*/
	public function Timesheet(){
		$input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		$rules = array(
		 'user_id' 			=> 'required',
		 //'device_id'  	=> 'required',
		 'platform_type' 	=> 'required',
		 'latitude'  		=> 'required',
		 'longitude'  		=> 'required',
		 'month'  			=> 'required',
		 );
		 $validator = Validator::make($input_data,$rules);
		 if ($validator->fails()){
			$messages = $validator->messages();
			return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		 }else{
			  /* $input_data['user_id'] = 38;
			 $input_data['month'] 	= 'september';  */
			 $timesheet 			= array();
			 $month_number			=	date('m',strtotime($input_data['month']));
			 $year					=	date('Y',strtotime($input_data['month']));
			 if(empty($month_name)){
				 $month_name 	= date("F");
			 }
			 $clinics_status 		= 	ClinicStatusModel::
										where('provider_id',$input_data['user_id'])
										->whereMonth('clock_out',$month_number)
										->whereYear('clock_out',$year)
										->get()->toArray();
			$first_date				=	$this->firstDay($month_number);
			$last_date				=	$this->lastDay($month_number);
			$current_date			=	date('Y-m-d',time());
			if(!empty($clinics_status)){
				while (strtotime($first_date) <= strtotime($last_date)) {
					$days_data[] = $this->GetGraphData($input_data['user_id'],$first_date,$current_date,$month_number,$year);
					$first_date  = date ("Y-m-d", strtotime("+1 days", strtotime($first_date)));
				}
				 $max_value							= $this->FindMax($days_data);
				 $gross_info 						= $this->GetGrossInformation($input_data['user_id'],$month_number);
				 $pay_period 	  					= $this->GetPayPeriodView($input_data['user_id'],$month_number,$year);
				 $week_view	  						= $this->GetWeekView($input_data['user_id'],$month_number,$year);
				 $timesheet['gross_info'] 			= $gross_info;
				 $timesheet['month_last_date'] 		= date('d',strtotime($this->lastDay($month_number)));
				 $timesheet['graph_highest_value'] 	= round($max_value+30);
				 $timesheet['graph_data'] 			= $days_data;
				 $timesheet['day_view']   			= $days_data;
				 $timesheet['pay_period'] 			= $pay_period;
				 $timesheet['week_view']  			= $week_view;
				 return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok.','timesheet'=>$timesheet)));
			}else{
				return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found in this month.')));
			}
		}
	}
	public function GetGraphData($user_id,$date,$current_date,$month_number,$year){
		$showing_date 	= 	date('j',strtotime($date));
		$day_name 		= 	date('D',strtotime($date));
		if($date<=$current_date){
			$timesheet_data = 	ClinicStatusModel::select('users.hourly_rate',DB::raw('SUM(clinic_status.mileage) as mileage'),DB::raw('SUM(clinic_status.clinic_spend_time) as hours_time'),DB::raw('SUM(clinic_status.drive_time) as drive_time'),DB::raw('users.hourly_rate*((SUM(clinic_status.clinic_spend_time)+SUM(clinic_status.drive_time))/60) as income'))
			->leftjoin('users','clinic_status.provider_id','users.id')
			->where('clinic_status.status','1')
			->whereNotNull('clinic_status.clock_in')
			->whereNotNull('clinic_status.clock_out')
			->whereDate('clock_out',$date)
			->where('clinic_status.provider_id',$user_id)
			->groupBy(['users.hourly_rate','clinic_status.provider_id'])
			->get()->toArray();
			if(!empty($timesheet_data)){
				$graph_data['mileage'] 		= 	$timesheet_data[0]['mileage'];
				$graph_data['drive_time'] 	= 	$timesheet_data[0]['drive_time'];
				$graph_data['hours_time'] 	= 	$timesheet_data[0]['hours_time'];
				$graph_data['status']  		= 	'1';
				$graph_data['day']  		= 	$showing_date;
				$graph_data['day_name']  	= 	$day_name;
				$graph_data['income']		=	$timesheet_data[0]['income'];
			}else{
				$graph_data['mileage'] 		= 	'0';
				$graph_data['drive_time'] 	= 	'0';
				$graph_data['hours_time'] 	= 	'0';
				$graph_data['status']  		= 	'0';
				$graph_data['day']  		= 	$showing_date;
				$graph_data['day_name']  	= 	$day_name;
				$graph_data['income']		=	'0';
			}
		}else{
				$graph_data['mileage'] 		= 	'-1';
				$graph_data['drive_time'] 	= 	'-1';
				$graph_data['hours_time'] 	= 	'-1';
				$graph_data['status'] 		= 	'-1';
				$graph_data['day']  		= 	$showing_date;
				$graph_data['day_name']  	= 	$day_name;
				$graph_data['income']		=	'-1';
		}
		return $graph_data;
	}
	public function GetPayPeriodView($user_id,$month_number,$year){
		$total_days			=	$this->GetTotalDaysInMonth($month_number,$year);
		$pay_period_type 	=	$this->GetAdminSettingsValue('pay_period');
		if($pay_period_type == '1-15'){
			$pay_value_explode = explode('-',$pay_period_type);
			$start_day		   = $pay_value_explode[0];
			$pay_period_range  = $pay_value_explode[1];
		}else{
			$start_day 			=	$this->GetAdminSettingsValue('pay_period_start');
			$pay_period_range 	=	$this->GetAdminSettingsValue('pay_period_days');
		}
		$start_date			=	$year.'-'.$month_number.'-'.$start_day;
		$start_date			=	date('Y-m-d',strtotime($start_date));
		$pay_period			=	array();
		$last_day			=	array();
		while($start_day<$total_days){
			$last_day			=	$this->GetLastDayOfPayPeriod($start_day,$pay_period_range,$total_days);
			$date_range			=	$this->GetDateRange($start_date,$pay_period_range);
			$pay_period			=	$this->GetPayPeriod($start_day,$last_day);
			$start_day 			= 	$start_day+$pay_period_range;
			$start_date 		= 	date('Y-m-d', strtotime('+'.$pay_period_range.' day', strtotime($start_date)));
			$pay_period_data[]	=	$this->GetPayPeriodData($user_id,explode(',',$date_range),$pay_period);
		}
		return $pay_period_data;
	}
	public function GetPayPeriodData($user_id,$date_array,$pay_period){
		$start_date   	= $date_array[0];
		$end_date   	= $date_array[1];
		$timesheet_data = 	ClinicStatusModel::select('users.hourly_rate',DB::raw('SUM(clinic_status.mileage) as mileage'),DB::raw('SUM(clinic_status.clinic_spend_time) as hours_time'),DB::raw('SUM(clinic_status.drive_time) as drive_time'),DB::raw('users.hourly_rate*((SUM(clinic_status.clinic_spend_time)+SUM(clinic_status.drive_time))/60) as income'))
		->leftjoin('users','clinic_status.provider_id','users.id')
		->where('clinic_status.status','1')
		->whereNotNull('clinic_status.clock_in')
		->whereNotNull('clinic_status.clock_out')
		->whereDate('clinic_status.clock_out','>=',$start_date)
		->whereDate('clinic_status.clock_out','<=',$end_date)
		->where('clinic_status.provider_id',$user_id)
		->groupBy(['users.hourly_rate','clinic_status.provider_id'])
		->get()->toArray();
		$data = array();
		if(!empty($timesheet_data)){
			foreach($timesheet_data as $time){
				$data  					= $time;
				$data['pay_period']  	= $pay_period;
			}
		}else{
			$data['hourly_rate'] 	=	'';
			$data['mileage'] 		=	'';
			$data['hours_time'] 	=	'';
			$data['drive_time'] 	=	'';
			$data['income'] 		=	'';
			$data['pay_period'] 	=	$pay_period;
		}
		return $data;
	}
	public function GetWeekView($user_id,$month_number,$year){
		$week			=	array();
		$start_date		=	$this->firstDay($month_number,$year);
		$end_date		=	$this->lastDay($month_number,$year);
		$weeks			=	$this->GetMonthWeeksDateRange($start_date,$end_date,$month_number,$year);
		$x 				=	1;
		foreach($weeks as $week){
			$timesheet_data 	= 	ClinicStatusModel::select('users.hourly_rate',DB::raw('SUM(clinic_status.mileage) as mileage'),DB::raw('SUM(clinic_status.clinic_spend_time) as hours_time'),DB::raw('SUM(clinic_status.drive_time) as drive_time'),DB::raw('users.hourly_rate*((SUM(clinic_status.clinic_spend_time)+SUM(clinic_status.drive_time))/60) as income'))
			->leftjoin('users','clinic_status.provider_id','users.id')
			->where('clinic_status.status','1')
			->whereNotNull('clinic_status.clock_in')
			->whereNotNull('clinic_status.clock_out')
			->whereBetween('clock_out',$week)
			->where('clinic_status.provider_id',$user_id)
			->groupBy(['users.hourly_rate','clinic_status.provider_id'])
			->get()->toArray();
			if(!empty($timesheet_data)){
				$timesheet_data[0]['week'] 	= 	$this->Ordinal($x);
				$data[] 					= 	$timesheet_data[0];
			}else{
				$data[]	=	array(
					'hourly_rate' 	=>	'',
					'mileage' 		=>	'',
					'hours_time' 	=>	'',
					'drive_time' 	=>	'',
					'income' 		=>	'',
					'week' 			=>	$this->Ordinal($x),
				);
			}
			$x++;
		}
		return $data;
	}
	public function GetGrossInformation($user_id,$month){
		$user_rate 		= 	User_model::select('hourly_rate')->where('id',$user_id)->first();
		$hourly_rate 	=   $user_rate->hourly_rate;
		$gross_data 	= 	ClinicStatusModel::select('clinic_status.provider_id','users.hourly_rate',DB::raw('SUM(clinic_status.mileage) as mileage'),DB::raw('SUM(clinic_status.clinic_spend_time) as hours_time'),DB::raw('SUM(clinic_status.drive_time) as drive_time'),DB::raw('users.hourly_rate*((SUM(clinic_status.clinic_spend_time)+SUM(clinic_status.drive_time))/60) as income'))
		->leftjoin('users','clinic_status.provider_id','users.id')
		->where('clinic_status.status','1')
		->whereNotNull('clinic_status.clock_in')
		->whereNotNull('clinic_status.clock_out')
		->whereMonth('clock_out',$month)
		->where('clinic_status.provider_id',$user_id)
		->groupBy(['users.hourly_rate','clinic_status.provider_id'])
		->get()->toArray();
		$data = array();
		if(!empty($gross_data)){
			foreach($gross_data as $gross){
				$data['gross_mileage_total'] 		= $gross['mileage'].' ml';
				$data['gross_drive_time_total'] 	= $gross['drive_time'].' hr';
				$data['gross_hours_time_total'] 	= $gross['hours_time'].' hr';
				$data['gross_income_total'] 		= '$'.$gross['income'];
			}
		}	 
		return $data;
	}
}
