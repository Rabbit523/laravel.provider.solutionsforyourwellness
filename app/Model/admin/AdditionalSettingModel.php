<?php

namespace App\Model\admin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast,DB;
use Illuminate\Support\Facades\Hash;

class AdditionalSettingModel extends Authenticatable
{
    use Notifiable;
	protected $table = 'admin_settings';

	/**
    * Function for update city.
    *
    * @param city id.
    *
    * @return response true on success otherwise false.
    */
  	public static function UpdateSetting(){
		if(Input::get('pay_period')=='1-15'){
			$pay_period 		= '1-15';
			$pay_period_start 	= null;
			$pay_period_days  	= null;
		}else{
			$pay_period 		= 'custom';
			$pay_period_start 	= Input::get('payperiod_start');
			$pay_period_days  	= Input::get('payperiod_days');
		}
  	  $model				                    =	new CitiesModel;
      $copyright_text                   = Input::get('copyright_text');
      $default_hours                    = Input::get('default_hours');
      $google_map_api                   = Input::get('google_map_api');
      $clock_in_default_time            = Input::get('clock_in_default_time');
      $default_time_stay_in_feeds       = Input::get('default_time_stay_in_feeds');
      $default_prep_time                = Input::get('default_prep_time');
      $max_distance                     = Input::get('max_distance');
      $preferred_wait_time              = Input::get('preferred_wait_time');
      $default_time_clockout            = Input::get('default_time_clockout');
      $default_auto_discard_time        = Input::get('default_auto_discard_time');
      $default_announcemnet_stay_feeds  = Input::get('default_announcemnet_stay_feeds');
      $default_max_scheduled_hours      = Input::get('default_max_scheduled_hours');
      $default_max_scheduled_per_day    = Input::get('default_max_scheduled_per_day');
      $default_max_scheduled_per_clinic = Input::get('default_max_scheduled_per_clinic');
      $default_max_mileage_per_month    = Input::get('default_max_mileage_per_month');
      $default_max_mileage_per_day      = Input::get('default_max_mileage_per_day');
      $default_max_mileage_per_clinic   = Input::get('default_max_mileage_per_clinic');
      $pay_period                       = $pay_period;
      $pay_period_start                 = $pay_period_start;
      $pay_period_days                 	= $pay_period_days;
      $location_missing_clockin_out     = Input::get('location_missing_clockin_out');
      $allow_clockin_before_preptime    = Input::get('allow_clockin_before_preptime');
      $accept_margin_time               = Input::get('accept_margin_time');
      $unfilled_before_time             = Input::get('unfilled_before_time');
	  $default_miles_clockout           = Input::get('default_miles_clockout');
	  $notify_clockout_time_admin       = Input::get('notify_clockout_time_admin');
	  $notify_clockout_mile_admin       = Input::get('notify_clockout_mile_admin');
      //$location_wrong_clockin_out     = Input::get('location_wrong_clockin_out');
      //$TimeZone                       = Input::get('timezone');

      $update_array = array(
                            'copyright_text'                  => $copyright_text,
                            'default_hours'                   => $default_hours,
                            'google_map_api'                  => $google_map_api,
                            'clock_in_default_time'           => $clock_in_default_time,
                            'default_time_stay_in_feeds'      => $default_time_stay_in_feeds,
                            'default_prep_time'               => $default_prep_time,
                            'max_distance'                    => $max_distance,
                            'preferred_wait_time'             => $preferred_wait_time,
                            'default_time_clockout'           => $default_time_clockout,
                            'default_auto_discard_time'       => $default_auto_discard_time,
                            'default_announcemnet_stay_feeds' => $default_announcemnet_stay_feeds,
                            'default_max_scheduled_hours'     => $default_max_scheduled_hours,
                            'default_max_scheduled_per_day'   => $default_max_scheduled_per_day,
                            'default_max_scheduled_per_clinic'=> $default_max_scheduled_per_clinic,
                            'default_max_mileage_per_month'   => $default_max_mileage_per_month,
                            'default_max_mileage_per_day'     => $default_max_mileage_per_day,
                            'default_max_mileage_per_clinic'  => $default_max_mileage_per_clinic,
                            'pay_period'                      => $pay_period,
                            'pay_period_start'                => $pay_period_start,
                            'pay_period_days'                 => $pay_period_days,
                            'location_missing_clockin_out'    => $location_missing_clockin_out,
                            'allow_clockin_before_preptime'   => $allow_clockin_before_preptime,
                            'accept_margin_time'              => $accept_margin_time,
                            'unfilled_before_time'            => $unfilled_before_time,
                            'default_miles_clockout'          => $default_miles_clockout,
                            'notify_clockout_time_admin'      => $notify_clockout_time_admin,
                            'notify_clockout_mile_admin'      => $notify_clockout_mile_admin,
                            'green_miles_start'      		  => Input::get('green_miles_start'),
                            'green_miles_end'     			  => Input::get('green_miles_end'),
                            'yellow_miles_start'      		  => Input::get('yellow_miles_start'),
                            'yellow_miles_end'      		  => Input::get('yellow_miles_end'),
                            'red_miles_start'      			  => Input::get('red_miles_start'),

                            );
    $saved           =   AdditionalSettingModel::where('id',20)->update($update_array);
  		if(!$saved){
  			return false;
  		}
  		return true;
  	}
}
