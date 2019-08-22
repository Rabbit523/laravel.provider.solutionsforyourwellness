<?php
namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Storage;
use App\Model\admin\ProvidersModel;
use App\Model\admin\ClinicsModel;
use App\Model\admin\ClinicsStatusModel;
use App\Model\TimesheetRecords;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast,Carbon;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminTimesheetController extends BaseController
{
	/**
	* Function for show timesheet records of all users.
	*
	*@param null
	*
	*@return timesheet list page of all users.
	*/
    public function Index(){
	  $month_number		=	date('m');
	  $year				=	date('Y');
	  $providers		=	ProvidersModel::where('role_id',0)->get();
	  foreach($providers as $provider){
		  
		 $timesheet 		= 	TimesheetRecords::select('providers_timesheet.provider_id','users.first_name','users.last_name','providers_timesheet.hourly_rate',
		DB::raw('SUM(providers_timesheet.mileage) as total_mileage'),
		DB::raw('TRUNCATE(SUM(providers_timesheet.clinic_spend_time)/60,2) as total_spend_time'),
		DB::raw('TRUNCATE(SUM(providers_timesheet.drive_time)/60,2) as total_drive_time'),
		DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income_total'))
		->leftjoin('users','providers_timesheet.provider_id','users.id')
		->whereNotNull('providers_timesheet.clock_in')
		->whereNotNull('providers_timesheet.clock_out')
		->whereMonth('providers_timesheet.clock_out',$month_number)
		->whereYear('providers_timesheet.clock_out',$year)
		->where('providers_timesheet.provider_id',$provider->id)
		->groupBy(['users.first_name','users.last_name','providers_timesheet.hourly_rate','providers_timesheet.provider_id'])->get()->toArray();
		  
		if(empty($timesheet)){
			$data = array('provider_id'=>$provider->id,'timesheet_status'=>0);
		}else{
			foreach($timesheet as $time){
				$data 	= $time;
				$data['timesheet_status'] = 1;
			}
		}
		$records[] = 	$data;
	  }
	  $clinics_status 		= 	TimesheetRecords::
									whereMonth('clock_out',$month_number)
									->whereYear('clock_out',$year)
									->get()->toArray();
		$first_date				=	date($year.'-'.$month_number.'-01');
		$last_date 				=   date('Y-m-t', strtotime($year.'-'.$month_number.'-01'));
		$current_date			=	date('Y-m-d',time());
		$month_array   			=   array('01','02','03','04','05','06','07','08','09','10','11','12');
		foreach($month_array as $month){
			$month_data[] = $this->GetByMonthDataForAll($month,$year);
		}
		
		if(!empty($clinics_status)){
			while (strtotime($first_date) <= strtotime($last_date)) {
				$days_data[] = $this->GetGraphDataForAll($first_date,$current_date,$month_number,$year);
				$first_date  = date ("Y-m-d", strtotime("+1 days", strtotime($first_date)));
			}
			 $max_value							= $this->FindMax($days_data);
			 $gross_info 						= $this->GetGrossInformationForAll($month_number);
			 $pay_period 	  					= $this->GetPayPeriodViewForAll($month_number,$year);
			 $week_view	  						= $this->GetWeekViewForAll($month_number,$year);
			 $timesheet['gross_info'] 			= $gross_info;
			 $timesheet['month_last_date'] 		= date('d',strtotime($this->lastDay($month_number)));
			 $timesheet['graph_highest_value'] 	= round($max_value+30);
			 $timesheet['graph_data'] 			= $days_data;
			 $timesheet['day_view']   			= $days_data;
			 $timesheet['pay_period'] 			= $pay_period;
			 $timesheet['week_view']  			= $week_view;
			 $timesheet['month_view']  			= $month_data;
		}
		
	  return view('admin.timesheet.index',compact('records','providers','timesheet'));
    }
	/**
	* Function for Download Excel sheet.
	*
	*@param null
	*
	*@return download excel sheet of timesheet record and return back to page.
	*/
	public function DownloadExcel($user_id=""){
		$month_number			=	date('m');
		$year					=	date('Y');
		$providers		=	Input::get('download_timesheet_excel');
		$type 			= 	'xlsx';
		if($user_id != ""){
			$providers = [$user_id];
		}else{
			$providers		=	Input::get('download_timesheet_excel');
		}
		
		if(!empty($providers)){
				$x = 0;
				foreach($providers as $provider){
					$records 		= 	TimesheetRecords::select('providers_timesheet.clinic_date','users.first_name','users.last_name','providers_timesheet.hourly_rate',
					DB::raw('SUM(providers_timesheet.mileage) as total_mileage'),
					DB::raw('TRUNCATE(SUM(providers_timesheet.clinic_spend_time)/60,2) as total_spend_time'),
					DB::raw('TRUNCATE(SUM(providers_timesheet.drive_time)/60,2) as total_drive_time'),
					DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income_total'))
					->leftjoin('users','providers_timesheet.provider_id','users.id')
					->whereNotNull('providers_timesheet.clock_in')
					->whereNotNull('providers_timesheet.clock_out')
					->where('providers_timesheet.income','!=',0)
					->whereMonth('providers_timesheet.clock_out',$month_number)
					->whereYear('providers_timesheet.clock_out',$year)
					->where('providers_timesheet.provider_id',$provider)
					->groupBy(['users.first_name','users.last_name','providers_timesheet.hourly_rate','providers_timesheet.provider_id','providers_timesheet.clinic_date'])->get()->toArray();
					
						foreach($records as $record){
							$name					=	$record['first_name'].' '.$record['last_name'];
							$record 				= 	array('name' =>$name) + $record;
							$mileage_array[]		=	$record['total_mileage'];
							$drive_time_array[]		=	$record['total_drive_time'];
							$total_time_array[]		=	$record['total_spend_time'];
							$price_array[]			=	number_format($record['income_total'],2);
							unset($record['first_name']);
							unset($record['last_name']);
							$data[$x][] = $record;
						}
								$gross_info				=	array(
									'',
									'',
									'',
									
									isset($mileage_array)?array_sum($mileage_array):'0'.' Miles',
									isset($total_time_array)?array_sum($total_time_array):'0'.' Mins',
									isset($drive_time_array)?array_sum($drive_time_array):'0'.' Mins',
									
									'$'.isset($price_array)?array_sum($price_array):'0',
								);
								$data[$x][] 		= $gross_info;
								$mileage_array 		= array();
								$total_time_array 	= array();
								$drive_time_array 	= array();
								$price_array 		= array();
								$x++;
				}
					
					
					//prd($data);
					return Excel::create('pay_period_'.time(), function($excel) use ($data) {
					foreach($data as $user_data){
						$excel->sheet($user_data[0]['name'], function($sheet) use ($user_data)
						{
							$sheet->row(1, ['Col 1', 'Col 2', 'Col 3','Col 4','Col 5','Col 6','Col 7']); // etc etc	
							$sheet->row(1, function($row) { $row->setBackground('#CCCCCC'); });
							$sheet->fromArray($user_data);
						});
					}
				})->download($type);	
		
		}else{
			Toast::error(trans('Please select at least 1 record!'));
			return Redirect::back();
		}
		return Redirect::back();
	}
	/**
	* Function for show timesheet record of all users for ajax pagination.
	*
	*@param null
	*
	*@return timesheet ajax list page.
	*/
	public function AjaxLoad(){
		$month_number			=	date('m');
		$year					=	date('Y');
		$length					= 	Input::get('length');
		$start					= 	Input::get('start');
		$search					= 	Input::get('search');
		$totaldata 				= 	$this->CountRecords(); // function counts records in user table.
		$total_filtered_data	=	$totaldata;
		$search					=	$search['value'];
		$order					=	Input::get('order');
		$column_id				=	$order[0]['column'];
		$column_order			=	$order[0]['dir'];
		$providers				=	ProvidersModel::where('role_id',0)->get();
		if($search){
			foreach($providers as $provider){
				$timesheet 		= 	TimesheetRecords::select('providers_timesheet.provider_id','users.first_name','users.last_name','providers_timesheet.hourly_rate',
					DB::raw('SUM(providers_timesheet.mileage) as total_mileage'),
					DB::raw('TRUNCATE(SUM(providers_timesheet.clinic_spend_time)/60,2) as total_spend_time'),
					DB::raw('TRUNCATE(SUM(providers_timesheet.drive_time)/60,2) as total_drive_time'),
					DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income_total'))
				->leftjoin('users','providers_timesheet.provider_id','users.id')
				->whereNotNull('providers_timesheet.clock_in')
				->whereNotNull('providers_timesheet.clock_out')
				->whereMonth('providers_timesheet.clock_out',$month_number)
				->whereYear('providers_timesheet.clock_out',$year)
				->where('providers_timesheet.provider_id',$provider->id)
				->groupBy(['users.first_name','users.last_name','providers_timesheet.hourly_rate','providers_timesheet.provider_id'])->where(function($query){
				})->where(function($query) use ($search) {
				$query->where('users.first_name', 'LIKE', '%'.$search.'%')
				->orWhere('users.last_name', 'LIKE', '%'.$search.'%');
				})->limit($length)->offset($start)->get()->toArray();
				if(!empty($timesheet)){
					foreach($timesheet as $time){
						$data = $time;
						$data['timesheet_status'] = 1;
						$data['search_status'] = 1;
					}
				}else{
						$data = array('provider_id'=>$provider->id,'timesheet_status'=>2,'search_status'=>1);	
				}
				$records[] = $data;
			}
		}else{
			foreach($providers as $provider){
				$timesheet 		= 	TimesheetRecords::select('providers_timesheet.provider_id','users.first_name','users.last_name','providers_timesheet.hourly_rate',
					DB::raw('SUM(providers_timesheet.mileage) as total_mileage'),
					DB::raw('TRUNCATE(SUM(providers_timesheet.clinic_spend_time)/60,2) as total_spend_time'),
					DB::raw('TRUNCATE(SUM(providers_timesheet.drive_time)/60,2) as total_drive_time'),
					DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income_total'))
				->leftjoin('users','providers_timesheet.provider_id','users.id')
				->whereNotNull('providers_timesheet.clock_in')
				->whereNotNull('providers_timesheet.clock_out')
				->whereMonth('providers_timesheet.clock_out',$month_number)
				->whereYear('providers_timesheet.clock_out',$year)
				->where('providers_timesheet.provider_id',$provider->id)
				->groupBy(['users.first_name','users.last_name','providers_timesheet.hourly_rate','providers_timesheet.provider_id'])
				->limit($length)->offset($start)->get()->toArray();
				if(empty($timesheet)){
					$data = array('provider_id'=>$provider->id,'timesheet_status'=>0,'search_status'=>0);
				}else{
					foreach($timesheet as $time){
						$data = $time;
						$data['timesheet_status'] = 1;
						$data['search_status'] = 0;
					}
				}
				$records[] = $data;
			}
		}
		return view('admin.timesheet.indexajax', ['records' => $records,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
	}
	/**
	* Function for count users record.
	*
	*@param null
	*
	*@return number of users/providers.
	*/
	public function CountRecords(){
		$count = ProvidersModel::where('role_id',0)->count();
		return $count;
	}
	
	
	/**
	* Function for timsheet record by user id.
	*
	*@param user Id
	*
	*@return timesheet list page of given user Id.
	*/
	public function TimesheetRecord($id){
		$month_number			=	date('m');
		$year					=	date('Y');
		
				$user_timesheet 	= 	TimesheetRecords::select('users.first_name','users.last_name','providers_timesheet.id','providers_timesheet.provider_id','providers_timesheet.clinic_date','providers_timesheet.clinic_location','providers_timesheet.clock_in','providers_timesheet.clock_out','providers_timesheet.clinic_spend_time','providers_timesheet.mileage','providers_timesheet.drive_time','providers_timesheet.income','providers_timesheet.hourly_rate','providers_timesheet.clinic_id','clinics.name',DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income')
				,DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as total_income')
				)
				->leftjoin('users','providers_timesheet.provider_id','users.id')
				->leftjoin('clinics','providers_timesheet.clinic_id','clinics.id')
				->whereNotNull('providers_timesheet.clock_in')
				->whereNotNull('providers_timesheet.clock_out')
				->whereMonth('providers_timesheet.clock_out',$month_number)
				->whereYear('providers_timesheet.clock_out',$year)
				->where('providers_timesheet.provider_id',$id)
				->groupBy(['users.first_name','users.last_name','providers_timesheet.id','providers_timesheet.provider_id','providers_timesheet.clinic_date','providers_timesheet.clinic_location','providers_timesheet.clock_in','providers_timesheet.clock_out','providers_timesheet.clinic_spend_time','providers_timesheet.mileage','providers_timesheet.drive_time','providers_timesheet.income','providers_timesheet.hourly_rate','providers_timesheet.clinic_id','clinics.name'])
				->get(); 
		if(empty($user_timesheet)){
			$user_timesheet = array();
		}	
		$records = 	$user_timesheet;
		if(empty($records)){
			 $records = array(); 
		}

		$timesheet 			= 	array();
		$month_number		=	date('m');
		$year				=	date('Y');
		if(empty($month_name)){
			 $month_name 	= date("F");
		}
		$clinics_status 		= 	TimesheetRecords::
									where('provider_id',$id)
									->whereMonth('clock_out',$month_number)
									->whereYear('clock_out',$year)
									->get()->toArray();
		$first_date				=	date($year.'-'.$month_number.'-01');
		$last_date 				=   date('Y-m-t', strtotime($year.'-'.$month_number.'-01'));
		//$last_date			=	$this->lastDay($month_number);
		$current_date			=	date('Y-m-d',time());
		$month_array   			=   array('01','02','03','04','05','06','07','08','09','10','11','12');
		foreach($month_array as $month){
			$month_data[] = $this->GetByMonthData($id,$month,$year);
		}
		
		if(!empty($clinics_status)){
			while (strtotime($first_date) <= strtotime($last_date)) {
				$days_data[] = $this->GetGraphData($id,$first_date,$current_date,$month_number,$year);
				$first_date  = date ("Y-m-d", strtotime("+1 days", strtotime($first_date)));
			}
		
			 $max_value							= $this->FindMax($days_data);
			 $gross_info 						= $this->GetGrossInformation($id,$month_number);
			 $pay_period 	  					= $this->GetPayPeriodView($id,$month_number,$year);
			 $week_view	  						= $this->GetWeekView($id,$month_number,$year);
			 $timesheet['gross_info'] 			= $gross_info;
			 $timesheet['month_last_date'] 		= date('d',strtotime($this->lastDay($month_number)));
			 $timesheet['graph_highest_value'] 	= round($max_value+30);
			 $timesheet['graph_data'] 			= $days_data;
			 $timesheet['day_view']   			= $days_data;
			 $timesheet['pay_period'] 			= $pay_period;
			 $timesheet['week_view']  			= $week_view;
			 $timesheet['month_view']  			= $month_data;
			 //return view('admin.timesheet.view', compact('timesheet'));
		}
	  return view('admin.timesheet.view',compact('records','timesheet'));
	}
	public function GetByMonthData($user_id,$month_number,$year){
			$timesheet_data 		= 	TimesheetRecords::select('providers_timesheet.hourly_rate',
			DB::raw('sum(providers_timesheet.mileage) as mileage'),
			DB::raw('TRUNCATE(sum(providers_timesheet.clinic_spend_time)/60, 2) as hours_time'),
			DB::raw('TRUNCATE(sum(providers_timesheet.drive_time)/60,2) as drive_time'),
			DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income'))
			->leftjoin('users','providers_timesheet.provider_id','users.id')
			->whereNotNull('providers_timesheet.clock_in')
			->whereNotNull('providers_timesheet.clock_out')
			->whereMonth('providers_timesheet.clock_out',$month_number)
			->whereYear('providers_timesheet.clock_out',$year)
			->where('providers_timesheet.provider_id',$user_id)
			->groupBy(['users.first_name','users.last_name','providers_timesheet.hourly_rate','providers_timesheet.provider_id'])
			->get()->toArray();
			if(!empty($timesheet_data)){
				$graph_data['mileage'] 		= 	$timesheet_data[0]['mileage'];
				$graph_data['drive_time'] 	= 	$timesheet_data[0]['drive_time'];
				$graph_data['hours_time'] 	= 	$timesheet_data[0]['hours_time'];
				$graph_data['status']  		= 	'1';
				$graph_data['month']  		= 	$month_number;
				$graph_data['income']		=	number_format($timesheet_data[0]['income'],2);
			}else{
				$graph_data['mileage'] 		= 	'0';
				$graph_data['drive_time'] 	= 	'0';
				$graph_data['hours_time'] 	= 	'0';
				$graph_data['status']  		= 	'0';
				$graph_data['month']  		= 	$month_number;
				$graph_data['income']		=	'0';
			}
		return $graph_data;
	}
	public function GetGraphData($user_id,$date,$current_date,$month_number,$year){
		$showing_date 	= 	date('j',strtotime($date));
		$day_name 		= 	date('D',strtotime($date));
		if($date<=$current_date){
			$timesheet_data 		= 	TimesheetRecords::select('providers_timesheet.hourly_rate',
			DB::raw('sum(providers_timesheet.mileage) as mileage'),
			DB::raw('TRUNCATE(sum(providers_timesheet.clinic_spend_time)/60, 2) as hours_time'),
			DB::raw('TRUNCATE(sum(providers_timesheet.drive_time)/60,2) as drive_time'),
			DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income')
			)
				->leftjoin('users','providers_timesheet.provider_id','users.id')
				->leftjoin('clinics','providers_timesheet.clinic_id','clinics.id')
				->whereNotNull('providers_timesheet.clock_in')
				->whereNotNull('providers_timesheet.clock_out')
				->whereDate('providers_timesheet.clock_out',$date)
				->where('providers_timesheet.provider_id',$user_id)
				->groupBy(['providers_timesheet.hourly_rate','providers_timesheet.provider_id'])
				->get()->toArray();
			
			if(!empty($timesheet_data)){
				$graph_data['mileage'] 		= 	$timesheet_data[0]['mileage'];
				$graph_data['drive_time'] 	= 	$timesheet_data[0]['drive_time'];
				$graph_data['hours_time'] 	= 	$timesheet_data[0]['hours_time'];
				$graph_data['status']  		= 	'1';
				$graph_data['day']  		= 	$showing_date;
				$graph_data['day_name']  	= 	$day_name;
				$graph_data['income']		=	number_format($timesheet_data[0]['income'],2);
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
			$last_day			=	$this->GetLastDayOfPayPeriod($start_day,$pay_period_range,$total_days,$month_number,$year);
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
		
		$timesheet_data 		= 	TimesheetRecords::select('providers_timesheet.hourly_rate',
		DB::raw('sum(providers_timesheet.mileage) as mileage'),
		DB::raw('TRUNCATE(sum(providers_timesheet.clinic_spend_time)/60, 2) as hours_time'),
		DB::raw('TRUNCATE(sum(providers_timesheet.drive_time)/60,2) as drive_time'),
		DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income')
		)
		->leftjoin('users','providers_timesheet.provider_id','users.id')
		->leftjoin('clinics','providers_timesheet.clinic_id','clinics.id')
		->whereNotNull('providers_timesheet.clock_in')
		->whereNotNull('providers_timesheet.clock_out')
		->whereDate('providers_timesheet.clock_out','>=',$start_date)
		->whereDate('providers_timesheet.clock_out','<=',$end_date)
		->where('providers_timesheet.provider_id',$user_id)
		->groupBy(['providers_timesheet.hourly_rate','providers_timesheet.provider_id'])
		->get()->toArray();
		$data = array();
		if(!empty($timesheet_data)){
			foreach($timesheet_data as $time){
				$data  					= $time;
				$data['pay_period']  	= $pay_period;
			}
		}else{
			$data['hourly_rate'] 	=	'0';
			$data['mileage'] 		=	'0';
			$data['hours_time'] 	=	'0';
			$data['drive_time'] 	=	'0';
			$data['income'] 		=	'0';
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
			$timesheet_data 		= 	TimesheetRecords::select('providers_timesheet.hourly_rate',
			DB::raw('sum(providers_timesheet.mileage) as mileage'),
			DB::raw('TRUNCATE(sum(providers_timesheet.clinic_spend_time)/60, 2) as hours_time'),
			DB::raw('TRUNCATE(sum(providers_timesheet.drive_time)/60,2) as drive_time'),
			DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income'))
			->leftjoin('users','providers_timesheet.provider_id','users.id')
			->leftjoin('clinics','providers_timesheet.clinic_id','clinics.id')
			->whereNotNull('providers_timesheet.clock_in')
			->whereNotNull('providers_timesheet.clock_out')
			->whereBetween('providers_timesheet.clock_out',$week)
			->where('providers_timesheet.provider_id',$user_id)
			->groupBy(['providers_timesheet.hourly_rate','providers_timesheet.provider_id'])
			->get()->toArray();
			if(!empty($timesheet_data)){
				$timesheet_data[0]['week'] 	= 	$x;
				$data[] 					= 	$timesheet_data[0];
			}else{
				$data[]	=	array(
					'hourly_rate' 	=>	'0',
					'mileage' 		=>	'0',
					'hours_time' 	=>	'0',
					'drive_time' 	=>	'0',
					'income' 		=>	'0',
					'week' 			=>	$x,
				);
			}
			$x++;
		}
		return $data;
	}
	public function GetGrossInformation($user_id,$month){
		$user_rate 		= 	ProvidersModel::select('hourly_rate')->where('id',$user_id)->first();
		$hourly_rate 	=   $user_rate->hourly_rate;
		
		$gross_data 		= 	TimesheetRecords::select('providers_timesheet.provider_id','providers_timesheet.hourly_rate',
		DB::raw('sum(providers_timesheet.mileage) as mileage'),
		DB::raw('TRUNCATE(sum(providers_timesheet.clinic_spend_time)/60, 2) as hours_time'),
		DB::raw('TRUNCATE(sum(providers_timesheet.drive_time)/60,2) as drive_time'),
		DB::raw('TRUNCATE(sum(providers_timesheet.income), 2) as income')
		)
		->leftjoin('users','providers_timesheet.provider_id','users.id')
		->leftjoin('clinics','providers_timesheet.clinic_id','clinics.id')
		->whereNotNull('providers_timesheet.clock_in')
		->whereNotNull('providers_timesheet.clock_out')
		->whereMonth('providers_timesheet.clock_out',$month)
		->where('providers_timesheet.provider_id',$user_id)
		->groupBy(['providers_timesheet.provider_id','providers_timesheet.hourly_rate'])
		->get()->toArray();
		$data = array();
		if(!empty($gross_data)){
			foreach($gross_data as $gross){
				$data['gross_mileage_total'] 		= $gross['mileage'];
				$data['gross_drive_time_total'] 	= $gross['drive_time'];
				$data['gross_hours_time_total'] 	= $gross['hours_time'];
				$data['gross_income_total'] 		= number_format($gross['income'],2);
			}
		}	 
		return $data;
	}
	public function GetByMonthDataForAll($month_number,$year){
			//$user_hourly_rate = DB::table('users')->select('hourly_rate')->where('id',$user_id)->get()->first();
			//$hourly_rate 	  = $user_hourly_rate->hourly_rate;
			$timesheet_data 		= 	TimesheetRecords::select(DB::raw('sum(income) as income'))
			->whereNotNull('providers_timesheet.clock_in')
			->whereNotNull('providers_timesheet.clock_out')
			->whereMonth('providers_timesheet.clock_out',$month_number)
			->whereYear('providers_timesheet.clock_out',$year)
			->get()->toArray();
			if(!empty($timesheet_data)){
				$graph_data['status']  		= 	'1';
				$graph_data['month']  		= 	$month_number;
				$graph_data['income']		=	number_format($timesheet_data[0]['income'],2);
			}else{
				$graph_data['status']  		= 	'0';
				$graph_data['month']  		= 	$month_number;
				$graph_data['income']		=	'0';
			}
		return $graph_data;
	}
	public function GetGraphDataForAll($date,$current_date,$month_number,$year){
		$showing_date 	= 	date('j',strtotime($date));
		$day_name 		= 	date('D',strtotime($date));
		if($date<=$current_date){
			$timesheet_data 		= 	TimesheetRecords::select(DB::raw('sum(`mileage`) as mileage'),DB::raw('TRUNCATE(sum(`clinic_spend_time`)/60, 2) as hours_time'),DB::raw('sum(`drive_time`) as drive_time'),DB::raw('TRUNCATE(sum(`income`), 2) as income'))
				->whereNotNull('providers_timesheet.clock_in')
				->whereNotNull('providers_timesheet.clock_out')
				->whereDate('providers_timesheet.clock_out',$date)
				->get()->toArray();
			
			if(!empty($timesheet_data)){
				$graph_data['mileage'] 		= 	isset($timesheet_data[0]['mileage'])?$timesheet_data[0]['mileage']:0;
				$graph_data['drive_time'] 	= 	isset($timesheet_data[0]['drive_time'])?$timesheet_data[0]['drive_time']:0;
				$graph_data['hours_time'] 	= 	isset($timesheet_data[0]['hours_time'])?$timesheet_data[0]['hours_time']:0;
				$graph_data['status']  		= 	'1';
				$graph_data['day']  		= 	$showing_date;
				$graph_data['day_name']  	= 	$day_name;
				$graph_data['income']		=	number_format($timesheet_data[0]['income'],2);
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
	public function GetGrossInformationForAll($month){
		$year = date('Y');
		$gross_data 		= 	TimesheetRecords::select(DB::raw('sum(`mileage`) as mileage'),DB::raw('TRUNCATE(sum(`clinic_spend_time`)/60, 2) as hours_time'),DB::raw('sum(`drive_time`) as drive_time'),DB::raw('TRUNCATE(sum(`income`), 2) as income'))
				->whereNotNull('providers_timesheet.clock_in')
				->whereNotNull('providers_timesheet.clock_out')
				->whereMonth('providers_timesheet.clock_out',$month)
				->whereYear('providers_timesheet.clock_out',$year)
				->get()->toArray();
		$data = array();
		if(!empty($gross_data)){
			foreach($gross_data as $gross){
				$data['gross_mileage_total'] 		= $gross['mileage'];
				$data['gross_drive_time_total'] 	= $gross['drive_time'];
				$data['gross_hours_time_total'] 	= $gross['hours_time'];
				$data['gross_income_total'] 		= number_format($gross['income'],2);
			}
		}	 
		return $data;
	}
	public function GetPayPeriodViewForAll($month_number,$year){
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
			$last_day			=	$this->GetLastDayOfPayPeriod($start_day,$pay_period_range,$total_days,$month_number,$year);
			$date_range			=	$this->GetDateRange($start_date,$pay_period_range);
			$pay_period			=	$this->GetPayPeriod($start_day,$last_day);
			$start_day 			= 	$start_day+$pay_period_range;
			$start_date 		= 	date('Y-m-d', strtotime('+'.$pay_period_range.' day', strtotime($start_date)));
			$pay_period_data[]	=	$this->GetPayPeriodDataForAll(explode(',',$date_range),$pay_period);
		}
		return $pay_period_data;
	}
	public function GetPayPeriodDataForAll($date_array,$pay_period){
		$start_date   	= $date_array[0];
		$end_date   	= $date_array[1];
		
		$timesheet_data 		= 	TimesheetRecords::select(DB::raw('sum(`mileage`) as mileage'),DB::raw('TRUNCATE(sum(`clinic_spend_time`)/60, 2) as hours_time'),DB::raw('sum(`drive_time`) as drive_time'),DB::raw('TRUNCATE(sum(`income`), 2) as income'))
		->leftjoin('clinics','providers_timesheet.clinic_id','clinics.id')
		->whereNotNull('providers_timesheet.clock_in')
		->whereNotNull('providers_timesheet.clock_out')
		->whereDate('providers_timesheet.clock_out','>=',$start_date)
		->whereDate('providers_timesheet.clock_out','<=',$end_date)
		->get()->toArray();
		$data = array();
		if(!empty($timesheet_data)){
			foreach($timesheet_data as $time){
				$data  					= $time;
				$data['pay_period']  	= $pay_period;
			}
		}else{
			$data['hourly_rate'] 	=	'0';
			$data['mileage'] 		=	'0';
			$data['hours_time'] 	=	'0';
			$data['drive_time'] 	=	'0';
			$data['income'] 		=	'0';
			$data['pay_period'] 	=	$pay_period;
		}
		return $data;
	}
	public function GetWeekViewForAll($month_number,$year){
		$week			=	array();
		$start_date		=	$this->firstDay($month_number,$year);
		$end_date		=	$this->lastDay($month_number,$year);
		$weeks			=	$this->GetMonthWeeksDateRange($start_date,$end_date,$month_number,$year);
		$x 				=	1;
		foreach($weeks as $week){
			$timesheet_data 		= 	TimesheetRecords::select(DB::raw('sum(`mileage`) as mileage'),DB::raw('TRUNCATE(sum(`clinic_spend_time`)/60, 2) as hours_time'),DB::raw('sum(`drive_time`) as drive_time'),DB::raw('TRUNCATE(sum(`income`), 2) as income'))
			->whereNotNull('providers_timesheet.clock_in')
			->whereNotNull('providers_timesheet.clock_out')
			->whereBetween('providers_timesheet.clock_out',$week)
			->get()->toArray();
			if(!empty($timesheet_data)){
				$timesheet_data[0]['week'] 	= 	$x;
				$data[] 					= 	$timesheet_data[0];
			}else{
				$data[]	=	array(
					'hourly_rate' 	=>	'0',
					'mileage' 		=>	'0',
					'hours_time' 	=>	'0',
					'drive_time' 	=>	'0',
					'income' 		=>	'0',
					'week' 			=>	$x,
				);
			}
			$x++;
		}
		return $data;
	}
	public function SearchByDate(){
		$provider_id 	= Input::get('provider_id');
		$start_date 	= Input::get('start_date');
		$end_date 		= Input::get('end_date');
		
		$start_final			=	date('Y-m-d',strtotime($start_date));
		$end_final				=	date('Y-m-d',strtotime($end_date));
		
		$user_timesheet 	= 	TimesheetRecords::select('users.first_name','users.last_name','providers_timesheet.id','providers_timesheet.provider_id','providers_timesheet.clinic_date','providers_timesheet.clinic_location','providers_timesheet.clock_in','providers_timesheet.clock_out','providers_timesheet.clinic_spend_time','providers_timesheet.mileage','providers_timesheet.drive_time','providers_timesheet.income','providers_timesheet.hourly_rate','providers_timesheet.clinic_id','clinics.name',DB::raw('TRUNCATE(providers_timesheet.hourly_rate*((SUM(providers_timesheet.clinic_spend_time))/60),2) as income'))
		->leftjoin('users','providers_timesheet.provider_id','users.id')
		->leftjoin('clinics','providers_timesheet.clinic_id','clinics.id')
		->whereNotNull('providers_timesheet.clock_in')
		->whereNotNull('providers_timesheet.clock_out')
		->whereBetween('providers_timesheet.clock_out',[$start_final, $end_final])
		->where('providers_timesheet.provider_id',$provider_id)
		->groupBy(['users.first_name','users.last_name','providers_timesheet.id','providers_timesheet.provider_id','providers_timesheet.clinic_date','providers_timesheet.clinic_location','providers_timesheet.clock_in','providers_timesheet.clock_out','providers_timesheet.clinic_spend_time','providers_timesheet.mileage','providers_timesheet.drive_time','providers_timesheet.income','providers_timesheet.hourly_rate','providers_timesheet.clinic_id','clinics.name'])
		->get();
			
		if(isset($user_timesheet) && !empty($user_timesheet)){
			return view('admin.timesheet.view',compact('user_timesheet'));
		}
	}
}
