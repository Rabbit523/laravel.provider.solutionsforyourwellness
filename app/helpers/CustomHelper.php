<?php

//use Illuminate\Http\Request;
use Carbon\Carbon;


class CustomHelper{

    public static function format_something()
    {
        return 1;
    }
    public static function GetUserData($id){
			$result =  DB::table('users')->where('id',$id)->first();
			return $result;
		}
    public static function Accepted_status($clinic_id){
			$result =  DB::table('clinic_status')->where('clinic_id',$clinic_id)->first();
      if($result != null){
        $user_id = $result->provider_id;
        $userdata =  DB::table('users')->where('id',$user_id)->first();
        //$result =  DB::table('clinic_status')->where('clinic_id',$clinic_id)->first();
        $result =  $userdata->first_name.' '.$userdata->last_name;
      }
      else{
        $result =  'Unfilled';
      }
			return $result;
		}
	public static function user_notifications($user_id){
       $notications =DB::table('admin_notifications')->where('user_id',$user_id)->where('admin_views',0)->limit(4)->get();
	   if($notications){
		   return $notications;
	   }else{
		   return false;
	   }
        
    }
    public static function all_notifications($admin_id){
      $notifications = DB::table('admin_notifications')->where('admin_views',0)->where('user_id',$admin_id)->orderBy('id','desc')->limit(5)->get();
      if(!empty($notifications)){
        return $notifications;
      }else{
        return false;
      }
    }
	public static function all_super_user_notifications(){
      $notifications = DB::table('admin_notifications')->where('admin_views',0)->orderBy('id','desc')->get();
      if(!empty($notifications)){
        return $notifications;
      }else{
        return false;
      }
    }
    public static function count_unread_notifications($admin_id){
      $unread_notifications = DB::table('admin_notifications')->where('user_id',$admin_id)->where('admin_views',0)->get()->count();
      if(!empty($unread_notifications)){
        return $unread_notifications;
      }else{
        return false;
      }
    }
	public static function count_super_user_unread_notifications(){
      $unread_notifications = DB::table('admin_notifications')->where('admin_views',0)->get()->count();
      if(!empty($unread_notifications)){
        return $unread_notifications;
      }else{
        return false;
      }
    }
    public static function CheckAsignStatus($clinic_id){
      $required_personals = DB::table('clinics')->select('personnel')->where('id',$clinic_id)->get()->first();
      $personals = $required_personals->personnel;

      $rules_personals = DB::table('rules')->where('clinic_id',$clinic_id)->get()->count();
      //prd($personals);

      if($personals==$rules_personals){
        return true;
      }else{
        return false;
      }
    }
	public static function time_elapsed_string($datetime, $full = false) {
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
	public static function GetUserNameById($user_id){
		$user = DB::table('users')->where('id',$user_id)->first();
		if(!empty($user)){
			return $user->first_name.' '.$user->last_name;
		}else{
			return 'Not Available';
		}
	}
	/**
	 * function for get drive time between two clicncs
	 *
	 * @param null
	 *
	 * @return response data.
	 */
	 public static function GetDrivingTimeGoogle($lat1, $lat2, $long1, $long2){
	     $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat1.",".$long1."&destinations=".$lat2.",".$long2."&mode=driving";
	     $ch 	= curl_init();
	     curl_setopt($ch, CURLOPT_URL, $url);
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	     curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	     $response = curl_exec($ch);
	     curl_close($ch);
	     $response_a = json_decode($response, true);
	     if($response_a){
			$time_in_seconds = isset($response_a['rows'][0]['elements'][0]['duration']['value'])?($response_a['rows'][0]['elements'][0]['duration']['value']):0; // In seconds
			$time_in_minuts  = $time_in_seconds/60; // covert into minuts
			$time 			  = round($time_in_minuts);
			return $time;
		 }else{
		 	return 0;
		 }
	 }
	public static function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2) {
		$theta = $lon1 - $lon2;
		$miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
		$miles = acos($miles);
		$miles = rad2deg($miles);
		$miles = $miles * 60 * 1.1515;
		$kilometers = $miles * 1.609344;
		return number_format($kilometers,2);
	}

	 public static function GetTimeData($clinic_id,$provider_id,$first_time,$second_time){

			$location_data 		= 	DB::table('geolocation')
										->where('clinic_id',$clinic_id)
										->where('user_id',$provider_id)
										->where('created_at','>=',$first_time)
										->where('created_at','<=',$second_time)
										->first();	
										//prd($location_data);
										
			if(!empty($location_data)){
				$clinic_data  =     DB::table('clinics')
										->where('id',$clinic_id)
										->first();
				$clinic_latitude 	= $clinic_data->latitude;
				$clinic_longitude 	= $clinic_data->longitude;
				$user_latitude		= $location_data->latitude;
				$user_longitude		= $location_data->longitude;
				
				$default_distance 	= CustomHelper::getDistanceBetweenPoints($clinic_latitude,$clinic_longitude,$user_latitude,$user_longitude);
				if($default_distance < 0.5){
					$color['time'] 		= $location_data->created_at;
					$color['color_name'] 		= 'green';
				}elseif($default_distance > 0.5 && $default_distance < 1){
					$color['time'] 		= $location_data->created_at;
					$color['color_name'] 		= 'yellow';
				}elseif($default_distance > 1 && $default_distance < 2){
					$color['time'] 		= $location_data->created_at;
					$color['color_name'] 	= 	'red';
				}else{
					$color['time'] 		= $location_data->created_at;
					$color['color_name'] 	= 	'grey';
				}
				$color 		= 	$color;
				$color['drive_time'] 	= 	$default_distance;
			}else{
				$color['color_name'] 	= 	'grey';
				$color['drive_time'] 	= 	'0';
			}
		//prd($color);
		return $color;
	}
	public static function DateDifference($start_date,$end_date){
		$start_date = new DateTime($start_date);
		$since_start = $start_date->diff(new DateTime($end_date));
		return $since_start->i;
	}
}
?>
