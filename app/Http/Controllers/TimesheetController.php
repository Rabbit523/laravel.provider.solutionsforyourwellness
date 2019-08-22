<?php
	namespace App\Http\Controllers\front;
	use App\Http\Controllers\BaseController;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\Pages_model;
	use App\Model\ProviderModel;
	use App\Model\ClinicsModel;
	use App\Model\ClinicStatusModel;
	use App\Model\EmailAction;
	use App\Model\EmailTemplate;
	use App\Model\EmailLog;
	use App\Model\ApiTokens;
	use Faker\Factory as Faker;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
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
		 'latitude'  			=> 'required',
		 'longitude'  		=> 'required',
		 'month'  				=> 'required',
		 );
		 $validator = Validator::make($input_data,$rules);
		 if ($validator->fails()){
			$messages = $validator->messages();
			return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		 }else{
			 $timesheet 		= array();
			 $month_number	=	date('m',strtotime($input_data['month']));
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
				 $max_value													= $this->FindMax($days_data);
				 $gross_info 												= $this->GetGrossInformation($input_data['user_id'],$month_number);
				 $pay_period 	  										= $this->GetPayPeriodView($input_data['user_id'],$month_number,$year);
				 $week_view	  											= $this->GetWeekView($input_data['user_id'],$month_number,$year);
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
		$user_rate 		= 	User_model::select('hourly_rate')->where('id',$user_id)->first();
		$hourly_rate 	=   $user_rate->hourly_rate;
		$showing_date 	= 	date('j',strtotime($date));
		$day_name 		= 	date('D',strtotime($date));
		if($date<=$current_date){
			$clinics_status  = 	ClinicStatusModel::
								where('provider_id',$user_id)
								->whereDate('clock_out',$date)
								->where('status',1)
								->whereNotNull('clock_in')
								->whereNotNull('clock_out')
								->get()->toArray();
			if(!empty($clinics_status)){
				foreach($clinics_status as $status){
					$mileage[]			= $status['mileage'];
					$drive_time[] 		= $status['drive_time'];
					$hours_time[] 		= ((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60));
				}
				$graph_data['mileage'] 		= 	array_sum($mileage);
				$graph_data['drive_time'] 	= 	array_sum($drive_time);
				$graph_data['hours_time'] 	= 	number_format(array_sum($hours_time),2);
				$graph_data['status']  		= 	1;
				$graph_data['day']  		= 	$showing_date;
				$graph_data['day_name']  	= 	$day_name;
				//$graph_data['income']			=	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
				$graph_data['income']		=	number_format(array_sum($hours_time)*$hourly_rate,2);
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
		$user_rate 		= 	User_model::select('hourly_rate')->where('id',$user_id)->first();
		$hourly_rate 	=   $user_rate->hourly_rate;
		$clinics_status  = 	ClinicStatusModel::
							where('provider_id',$user_id)
							->whereMonth('clock_out',$month_number)
							->get()->toArray();
		if(!empty($clinics_status)){
			$first_pay_period 	= array();
			$second_pay_period 	= array();
			foreach($clinics_status as $status){
				$date = date('d',strtotime($status['clock_out']));
				if($date <= 15){
					$first_pay_period_mileage[] 		= 	$status['mileage'];
					$first_pay_period_drive_time[] 	= 	$status['drive_time'];
					$first_pay_period_hours_time[] 	= 	((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60));
					$first_pay_period_income[]			=	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
				}else{
					$second_pay_period_mileage[] 			= 	isset($status['mileage'])?$status['mileage']:0;
					$second_pay_period_drive_time[] 	= 	$status['drive_time'];
					$second_pay_period_hours_time[] 	= 	((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60));
					$second_pay_period_income[]				=	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
				}
			}
			$first_pay_period['mileage'] 	 		= 	isset($first_pay_period_mileage)?array_sum($first_pay_period_mileage):'0';
			$first_pay_period['drive_time']  	= 	isset($first_pay_period_drive_time)?array_sum($first_pay_period_drive_time):'0';
			$first_pay_period['hours_time']  	= 	isset($first_pay_period_hours_time)?number_format(array_sum($first_pay_period_hours_time),2):'0';
			$first_pay_period['income'] 	 		= 	isset($first_pay_period_income)?array_sum($first_pay_period_income):'0';
			$first_pay_period['period'] 	 		= 	'1st-15th';

			$second_pay_period['mileage'] 	 	= 	isset($second_pay_period_mileage)?array_sum($second_pay_period_mileage):'0';
			$second_pay_period['drive_time'] 	= 	isset($second_pay_period_drive_time)?array_sum($second_pay_period_drive_time):'0';
			$second_pay_period['hours_time'] 	= 	isset($second_pay_period_hours_time)?number_format(array_sum($second_pay_period_hours_time),2):'0';
			$second_pay_period['income'] 	 		= 		isset($second_pay_period_income)?array_sum($second_pay_period_income):'0';
			$second_pay_period['period'] 	 		= 		'16th-'.date('d',strtotime($this->lastDay($month_number))).'th';

			$result[]  	=	$first_pay_period;
			$result[]  	=	$second_pay_period;
			return $result;
		}
	}
	public function GetWeekView($user_id,$month_number,$year){
		$user_rate 		= 	User_model::select('hourly_rate')->where('id',$user_id)->first();
		$hourly_rate 	=   $user_rate->hourly_rate;
		$clinics_status  = 	ClinicStatusModel::
							where('provider_id',$user_id)
							->whereMonth('clock_out',$month_number)
							->get()->toArray();
		if(!empty($clinics_status)){
			$first_week_period 		= array();
			$second_week_period 	= array();
			$third_week_period 		= array();
			$fourth_week_period 	= array();
			foreach($clinics_status as $status){
				$clinic_date = date('d',strtotime($status['clock_out']));
				if($clinic_date <  8 && $clinic_date >= 1){
					$first_week_mileage[] 				= 	$status['mileage'];
					$first_week_drive_time[] 			= 	$status['drive_time'];
					$first_week_hours_time[] 			= 	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60)),2);
					$first_week_income[]					=		number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
				}elseif($clinic_date >=  8 && $clinic_date < 15){
					$second_week_mileage[] 				= 	$status['mileage'];
					$second_week_drive_time[] 		= 	$status['drive_time'];
					$second_week_hours_time[] 		= 	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60)),2);
					$second_week_income[]		=	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
				}elseif($clinic_date >=  15 && $clinic_date < 22){
					$third_week_mileage[] 				= 	$status['mileage'];
					$third_week_drive_time[] 			= 	$status['drive_time'];
					$third_week_hours_time[] 			= 	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60)),2);
					$third_week_income[]					=		number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
				}else{
					$fourth_week_mileage[] 				= 	$status['mileage'];
					$fourth_week_drive_time[] 		= 	$status['drive_time'];
					$fourth_week_hours_time[] 		= 	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60)),2);
					$fourth_week_income[]					=		number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
				}
			}
			//calculating week view records
			$first_week_period['mileage'] 		=  isset($first_week_mileage)?array_sum($first_week_mileage):'0';
			$first_week_period['drive_time'] 	=  isset($first_week_drive_time)?array_sum($first_week_drive_time):'0';
			$first_week_period['hours_time'] 	=  isset($first_week_hours_time)?number_format(array_sum($first_week_hours_time),2):'0';
			$first_week_period['income'] 			=  isset($first_week_income)?array_sum($first_week_income):'0';
			$first_week_period['week'] 				=  '1st week';

			$second_week_period['mileage'] 		=  isset($second_week_mileage)?array_sum($second_week_mileage):'0';
			$second_week_period['drive_time'] =  isset($second_week_drive_time)?array_sum($second_week_drive_time):'0';
			$second_week_period['hours_time'] =  isset($second_week_hours_time)?number_format(array_sum($second_week_hours_time),2):'0';
			$second_week_period['income'] 		=  isset($second_week_income)?array_sum($second_week_income):'0';
			$second_week_period['week'] 			=  '2nd week';

			$third_week_period['mileage'] 		=  isset($third_week_mileage)?array_sum($third_week_mileage):'0';
			$third_week_period['drive_time'] 	=  isset($third_week_drive_time)?array_sum($third_week_drive_time):'0';
			$third_week_period['hours_time'] 	=  isset($third_week_hours_time)?number_format(array_sum($third_week_hours_time),2):'0';
			$third_week_period['income'] 			=  isset($third_week_income)?array_sum($third_week_income):'0';
			$third_week_period['week'] 				=  '3rd week';

			$fourth_week_period['mileage'] 		=  isset($fourth_week_mileage)?array_sum($fourth_week_mileage):'0';
			$fourth_week_period['drive_time'] =  isset($fourth_week_drive_time)?array_sum($fourth_week_drive_time):'0';
			$fourth_week_period['hours_time'] =  isset($fourth_week_hours_time)?number_format(array_sum($fourth_week_hours_time),2):'0';
			$fourth_week_period['income'] 		=  isset($fourth_week_income)?array_sum($fourth_week_income):'0';
			$fourth_week_period['week'] 			=  '4th week';

			$result[]  				=	$first_week_period;
			$result[]  				=	$second_week_period;
			$result[]  				=	$third_week_period;
			$result[]  				=	$fourth_week_period;

			return $result;
		}
	}
	public function GetGrossInformation($user_id,$month){
		$user_rate 		= 	User_model::select('hourly_rate')->where('id',$user_id)->first();
		$hourly_rate 	=   $user_rate->hourly_rate;
		$clinics_status  = 	ClinicStatusModel::
							where('provider_id',$user_id)
							->whereMonth('clock_out',$month)
							->get()->toArray();
		if(!empty($clinics_status)){
			foreach($clinics_status as $status){
			   $mileage[] 		= 	$status['mileage'];
			   $drive_time[] 	= 	$status['drive_time'];
			   $hours_time[] 	= 	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60)),2);
			   $income[] 			= 	number_format(((strtotime(($status['clock_out']))-strtotime($status['clock_in']))/(60*60))*$hourly_rate,2);
			}
		}
		 $gross_mileage_total 		= 	array_sum($mileage).' ml';
		 $gross_drive_time_total 	= 	array_sum($drive_time).' hr';
		 $gross_clinic_time_total	= 	number_format(array_sum($hours_time),2).' hr';
		 $gross_income_total 			= 	'$ '.number_format((array_sum($income)));
			$gross_info 	=	array(
					'gross_mileage_total'			=>	$gross_mileage_total,
					'gross_drive_time_total'	=>	$gross_drive_time_total,
					'gross_hours_time_total'	=>	$gross_clinic_time_total,
					'gross_income_total'			=>	$gross_income_total,
				 );
		return $gross_info;
	}
}
