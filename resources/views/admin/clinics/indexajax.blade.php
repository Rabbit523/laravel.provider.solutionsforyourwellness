<?php
$table_data			=	array();
$table_data['aaData']	=	array();
$x = 1;
$config_date 		= Config::get('date_format.date');
$config_month 		= Config::get('date_format.month');
$config_year 		= Config::get('date_format.year');
$config_separator 	= Config::get('date_format.separator');
foreach ($clinicdetails as $clinic){

	$edit_btn		=		'<a href="'.URL::route('edit-clinic',$clinic->id).'"><i class="mdi-editor-border-color" title="edit"></i></a>';
	$delete_btn	=	'<a title="delete" data-toggle="tooltip" class="delete_record_btn" href="javascript:void(0);" data-url="'.URL::route('delete-clinic',$clinic->id).'"> <i class="mdi-action-delete" title="Delete"></i></a>';
	$checkbox = '<input type="checkbox" id="'.'click'.$clinic->id.'" value="'.$clinic->id.'" name="chk_ids[]" class="checked_clinics" /><label for="'.'click'.$clinic->id.'"></label>';
	$clinic_calender = '<a  href="'.URL::route('clinic-calender',$clinic->id).'" class="" title="Clinic calendar"><i class="mdi-notification-event-available"></i></a>';
	if($clinic->name != null){
		$clinic_name = $clinic->name;
	}
	else{
		$clinic_name = 'Unfilled clinic';
	}
	$accepted_status = CustomHelper::Accepted_status($clinic->id);

	$user_id = Auth::user()->id;
	$user_time_zone 			= DB::table('users')->select('timezone')->where('id',$user_id)->get();
	$user_time_zone_value = $user_time_zone[0]->timezone;

	$clinic_date_time = new DateTime($clinic->date.' '.$clinic->time, new DateTimeZone('GMT'));
	$clinic_date_time->setTimezone(new DateTimeZone($clinic->timezone));
	$date_time 		= $clinic_date_time->format('Y-m-d H:i');
	$date         	= $clinic_date_time->format('Y-m-d');
	$time         	= $clinic_date_time->format('H:i');

	//$user_time_zone 			= DB::table('users')->select('timezone')->where('id',$user_id)->get();
	$number_provider = DB::table('clinics')->select('personnel')->where('id',$clinic->id)->get()->first();
	$total_required =	$number_provider->personnel;
	$accepted_users = DB::table('clinic_status')->where('clinic_id',$clinic->id)->where('status',1)->get()->count();
	if($accepted_users == $total_required){
		$rule_accepted = DB::table('rules')->where('clinic_id',$clinic->id)->get()->count();
			if($rule_accepted == $total_required && $total_required != 1){
				$rule =	'<a class="btn waves-effect waves-light blue" style="padding: 0 0.5rem;text-transform:lowercase">assigned</a>';
			}elseif($rule_accepted != $total_required && $total_required > 1){
				$rule = '<a class="btn waves-effect waves-light blue" style="padding: 0 0.5rem;text-transform:lowercase"  href="'.URL::route('asign-rules',$clinic->id).'" >assign rule</a>';
			}else{
				$rule =	'<a class="btn waves-effect waves-light blue" style="padding: 0 0.5rem;text-transform:lowercase;display:none">assigned</a>';
			}
	}else{
		// $rule =	'<a class="btn waves-effect waves-light blue" style="padding: 0 0.5rem;text-transform:lowercase">Disabled</a>';
		$rule = '';
	}
	$table_data['aaData'][]			=	array(
											'sno'=>$checkbox,
											'name'=>$clinic_name,
											'phone'=>$clinic->phone,
											'address'=>$clinic->location_name,
											'time'=>$time,
											'date'=>date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($date)),
											'rule'=>$rule,
											'personnel'=>$clinic->personnel,
											'accepted'=>$accepted_users,
											'action'=>$edit_btn.'&nbsp;'.$delete_btn,
										);
										$x++;
}
$table_data['iTotalRecords']					=	$total_data;
$table_data['iTotalDisplayRecords']		=	$total_filtered_data;
echo json_encode($table_data);die;
?>
