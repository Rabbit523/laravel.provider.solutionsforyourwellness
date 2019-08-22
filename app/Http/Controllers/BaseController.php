<?php
namespace App\Http\Controllers;
use Dingo\Api\Routing\Helpers;
use Illuminate\Routing\Controller;
use Auth,Blade,Config,Cache,Cookie,DB,File,Hash,Input,Mail,mongoDate,Redirect,Request,Response,Session,URL,View,Validator,Str,App,Toast,stdClass;
use ZipArchive,DateTime;
use App\Model\User;
use App\Model\EmailTemplate;
use App\Model\Notifications;
use App\Model\admin\AdminNotifications;
use App\Model\User_model;
use App\Model\EmailLog;
use App\Model\ClinicsModel;
use App\Model\AdminSettings;
use App\Model\ApiTokens;
use App\Model\AnnouncementModel;
/**
* Base Controller
*
* Add your methods in the class below
*
* This is the base controller called everytime on every request
*/
class BaseController extends Controller {
	use Helpers;
	public function __construct() {
		//$default_timezone 	= 	$this->GetAdminSettingsValue('timezone'); // getting default timezone from database.
		//Config::set('app.timezone','Asia/kolkata');	// set time of server from database.
		//$server_timezone 	= 	Config::get('app.timezone');
		//date_default_timezone_set($default_timezone);	
	}// end function __construct()
	public function saveCkeditorImages() {
		if(isset($_GET['CKEditorFuncNum'])){
			$image_url				=	"";
			$msg					=	"";
			// Will be returned empty if no problems
			$callback = ($_GET['CKEditorFuncNum']);        // Tells CKeditor which function you are executing
			$image_details 				= 	getimagesize($_FILES['upload']["tmp_name"]);
			$image_mime_type			=	(isset($image_details["mime"]) && !empty($image_details["mime"])) ? $image_details["mime"] : "";
			if($image_mime_type	==	'image/jpeg' || $image_mime_type == 'image/jpg' || $image_mime_type == 'image/gif' || $image_mime_type == 'image/png'){
				$ext					=	$this->getExtension($_FILES['upload']['name']);
				$fileName				=	"ck_editor_".time().".".$ext;
				$upload_path			=	CK_EDITOR_ROOT_PATH;
				if(move_uploaded_file($_FILES['upload']['tmp_name'],$upload_path.$fileName)){
					$image_url 			= 	CK_EDITOR_URL. $fileName;    
				}
			}else{
				$msg =  'error : Please select a valid image. valid extension are jpeg, jpg, gif, png';
			}
			$output = '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('.$callback.', "'.$image_url .'","'.$msg.'");</script>';
			echo $output;
			exit;
		}
	}
	public function getExtension($str) {
		$i = strrpos($str,".");
		if (!$i) { return ""; }
		$l = strlen($str) - $i;
		$ext = substr($str,$i+1,$l);
		$ext = strtolower($ext);
		return $ext;
	}//end getExtension()
	/**
	* Function to make slug according model from any certain field
	*
	* @param title     as value of field
	* @param modelName as section model name
	* @param limit 	as limit of characters
	*
	* @return string
	*/
	public function getSlug($title, $fieldName,$modelName,$limit = 30){
		$slug 		= 	 substr(Str::slug($title),0 ,$limit);
		$Model		=	 "\App\Model\\$modelName";
		$slugCount 	=    count($Model::where($fieldName, 'regexp', "/^{$slug}(-[0-9]*)?$/i")->get());
		return ($slugCount > 0) ? $slug."-".$slugCount : $slug;
	}//end getSlug()

	/**
	* Function to make slug without model name from any certain field
	*
	* @param title     as value of field
	* @param tableName as table name
	* @param limit 	as limit of characters
	*
	* @return string
	*/
	public function getSlugWithoutModel($title, $fieldName='' ,$tableName,$limit = 30){
		$slug 		=	substr(Str::slug($title),0 ,$limit);
		$slug 		=	Str::slug($title);
		$DB 		= 	DB::table($tableName);
		$slugCount 	= 	count( $DB->whereRaw("$fieldName REGEXP '^{$slug}(-[0-9]*)?$'")->get() );
		return ($slugCount > 0) ? $slug."-".$slugCount: $slug;
	}//end getSlugWithoutModel()

	/**
	* Function to search result in database
	*
	* @param data  as form data array
	*
	* @return query string
	*/
	public function search($data){
		unset($data['display']);
		unset($data['_token']);
		$ret	=	'';
		if(!empty($data )){
			foreach($data as $fieldName => $fieldValue){
				$ret	.=	"where('$fieldName', 'LIKE',  '%' . $fieldValue . '%')";
			}
			return $ret;
		}
	}//end search()

	/**
	* Function to sending email
	*
	* @param receiver's email,receiver's full name,email template,email action,replace constants
	*
	* @calls email sending default function.
	* @calls email save logs function if email sent.
	*
	* @return response true if email send otherwise false.
	*/
	public function mail_send($action,$email,$full_name,$subject_replace_array=array(),$body_replace_array,$attachment='null',$clinic_id=null,$userid=null,$type=null){
		/* choose email template and actions */
		$template				=	EmailTemplate::where('action','=',$action)->first();
		$from					=	env('MAIL_USERNAME');
		$subject				=	$template->subject;
		$body					=	$template->body;
		$settingsEmail			= 	Config::get('Site.email');
		$body_constants			=	$this->GetEmailConstants($body);
		$subject_constants		=	$this->GetEmailConstants($subject);
		if(!empty($subject_constants) && !empty($subject_replace_array)){
			$subject			=  	str_replace($subject_constants,$subject_replace_array,$subject);
		}
		$messageBody			=  	str_replace($body_constants,$body_replace_array,$body);
		$mail_send				=	$this->sendMail($email,$full_name,$subject,$messageBody,$settingsEmail,$files = false,$path='',$attachment='');
		if($mail_send){
			$this->Savelogs($email,$from,$subject,$messageBody,$status='Sent',$clinic_id,$userid,$type);
			return true;
		}else{
			$this->Savelogs($email,$from,$subject,$messageBody,$status='Not sent',$clinic_id,$userid,$type);
			return false;
		}
	}
	/**
	* Function for save email logs if email sent successfully.
	*
	* @param receiver's email,sent from,email subject,email body etc.
	*
	* @return null.
	*/
	public function Savelogs($email,$from,$subject,$messageBody,$status,$clinic_id,$user_id=null,$type){
		$model					=	new EmailLog;
		$model->email_to		=	$email;
		$model->email_from		=	$from;
		$model->email_type		=	'html';
		$model->subject			=	$subject;
		$model->message			=	$messageBody;
		$model->status			=	$status;
		$model->clinic_id		=	$clinic_id;
		$model->user_id			=	$user_id;
		$model->type			=	$type;
		$saved					=	$model->save();
	}
	/**
	* Function for checking mail sent status
	*/
	public function CheckMailSentStatus($clinic_id,$user_id,$type){
		$data = DB::table('email_logs')
				->where('clinic_id',$clinic_id)
				->where('user_id',$user_id)
				->where('type',$type)
				->get()->count();
		if($data){
			return 1;
		}else{
			return 0;
		}
	}
	/**
	* Function for get email constants from given string/emailBody.
	*
	* @param $string/emailBody.
	*
	* @return response array of constants on success otherwise false.
	*/
	public function GetEmailConstants($string){
		preg_match_all('/\{(.*?)}/', $string, $result);
		if(!empty($result[0])){
			return $result[0];
		}else{
			return false;
		}
	}
	/**
	* Default/Main function for sending email.
	*
	* @param receiver's email,receiver's full name,email subject,email body,from address,attachment file etc.
	*
	* @return response true if email sent.
	*/
	public function sendMail($to,$fullName,$subject,$messageBody, $from = '',$files = false,$path='',$attachmentName='') {
		$data										=	array();
		$data['to']							=	$to;
		$data['from']						=	(!empty($from) ? $from : env('MAIL_USERNAME'));
		$data['fullName']				=	$fullName;
		$data['subject']				=	$subject;
		$data['filepath']				=	$path;
		$data['attachmentName']	=	$attachmentName;
		if($files===false){
			Mail::send('emails.email', array('messageBody'=> $messageBody), function($message) use ($data){
				$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject']);

			});
		}else{
			if($attachmentName!=''){
				Mail::send('emails.email', array('messageBody'=> $messageBody), function($message) use ($data){
					$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath'],array('as'=>$data['attachmentName']));
				});
			}else{
				Mail::send('emails.email', array('messageBody'=> $messageBody), function($message) use ($data){
					$message->to($data['to'], $data['fullName'])->from($data['from'])->subject($data['subject'])->attach($data['filepath']);
				});
			}
		}
		return true;
	}

	public  function arrayStripTags($array){
		$result			=	array();
		foreach ($array as $key => $value) {
			// Don't allow tags on key either, maybe useful for dynamic forms.
			$key = strip_tags($key,ALLOWED_TAGS_XSS);

			// If the value is an array, we will just recurse back into the
			// function to keep stripping the tags out of the array,
			// otherwise we will set the stripped value.
			if (is_array($value)) {
				$result[$key] = $this->arrayStripTags($value);
			} else {
				// I am using strip_tags(), you may use htmlentities(),
				// also I am doing trim() here, you may remove it, if you wish.
				$result[$key] = trim(strip_tags($value,ALLOWED_TAGS_XSS));
			}
		}

		return $result;

	}

	/**
	* Function to sending email
	*
	* @param receiver's email,receiver's full name,email template,email action,replace constants
	*
	* @calls email sending default function.
	* @calls email save logs function if email sent.
	*
	* @return response true if email send otherwise false.
	*/
	public static function GetAdminSettings($field_name){
		$result =  DB::table('admin_settings')->select('field_value')->where('field_name',$field_name)->first();
		return $result->field_value;
	}
	public static function GetAdminSettingsValue($field_name){
		$result =  DB::table('admin_settings')->select($field_name)->where('id',20)->first();
		return $result->$field_name;
	}
	/**
	* Function to get admin's notifications setting type
	* @return setting type
	*/
	public static function GetAdminNotificationTypeSettings($admin_id){
		$result =  DB::table('users')->select('notify_only')->where('id',$admin_id)->first();
		$type =  $result->notify_only;
		if($type == 1){
		 return 'email';
		}elseif($type == 2){
		 return 'push';
		}elseif($type == 3){
		 return 'both';
		}elseif($type == 4){
		 return 'none';
		}
	}

	/**
	* Function to get admin setting data
	*
	* @param fieldname, field id
	*
	* @return response response.
	*/
	public static function GetAdminSettings_data($field_name,$id){
		$result =  DB::table('admin_settings')->select($field_name)->where('id',$id)->first();
		//prd($result);
		return $result;

	}

	/**
	 * Function to _update_all_status
	 *
	 * param source tableName,id,status,fieldName
	*/
	public function _update_all_status($tableName = null,$id = 0,$status= 0,$fieldName = 'is_active'){
		DB::beginTransaction();
		$response			=	DB::statement("CALL UpdateAllTableStatus('$tableName',$id,$status)");
		if(!$response) {
			DB::rollback();
			Session::flash('error', trans("messages.msg.error.something_went_wrong"));
			return Redirect::back();
		}
		DB::commit();
	}// end _update_all_status()

	/**
	 * Function to _delete_table_entry
	 *
	 * param source tableName,id,fieldName
	 */
	public function _delete_table_entry($tableName = null,$id = 0,$fieldName = null){
		DB::beginTransaction();
		$response			=	DB::statement("CALL DeleteAllTableDataById('$tableName',$id,'$fieldName')");
		if(!$response) {
			DB::rollback();
			Session::flash('error', trans("messages.msg.error.something_went_wrong"));
			return Redirect::back();
		}
		DB::commit();
	}// end _delete_table_entry()
	/**
	 * function for upload image and resize image.
	 *
	 * param imagesource,folder_location,resize,resize array of multiple sizes
	 *
	 * return imagename on success and false on failure.
	 */
	public function ImageUpload($image,$folder_name,$resize=true,$resize_options=null){
			if(!File::exists(public_path('uploads/'.$folder_name))) {
					File::makeDirectory(public_path('uploads/'.$folder_name,$mode=0777,true));
			}
			$filename 		= 	time().'_' . $image->getClientOriginalName();
			$upload_path 	=	public_path('uploads/'.$folder_name.DS);
			$path 			= 	$upload_path.$filename;
			// Upload image only in original size.
			\Image::make($image->getRealPath())->save($path);
			if($resize){
				// if image resize is true then upload image with multiple sizes.
				if(!empty($resize_options)){
						foreach($resize_options as $options){
							$dimensions = explode('x',$options);
							$resize_path = $upload_path.$options;
							if(!File::exists($resize_path)) {
									File::makeDirectory($resize_path,$mode=0777,true);
							}
							$new_path 	=	$resize_path.DS. $filename;
							\Image::make($image->getRealPath())->resize($dimensions[0],$dimensions[1])->save($new_path);
						}
					}
				}
			return $filename;
	}
	/**
	 * Function for generate Random string
	 *
	 * @param number
	 * @return randomstring.
	 */
	public function generate_random_string($length = 10) {
		 $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		 $charactersLength = strlen($characters);
		 $randomString = '';
		 for ($i = 0; $i < $length; $i++) {
			 $randomString .= $characters[rand(0, $charactersLength - 1)];
		 }
		 return $randomString;
	}

	public function encrypt_array($array) {
              //$key = $this->hex2bin($key);
              $iv = ENCRYPTION_IV;
              $td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);
              mcrypt_generic_init($td, ENCRYPTION_KEY, $iv);
			foreach($array as $key=>$value){
				$encrypted = mcrypt_generic($td, $value);
				  mcrypt_generic_deinit($td);
				  mcrypt_module_close($td);
				  $new_array[] =  bin2hex($encrypted);
			}
    }

	public function encrypt($str) {
		$this->WriteTextFile($str,'Response');
		//$key = $this->hex2bin($key);
		$iv = ENCRYPTION_IV;

		$td = mcrypt_module_open('rijndael-128', '', 'cbc', $iv);

		mcrypt_generic_init($td, ENCRYPTION_KEY, $iv);
		$encrypted = mcrypt_generic($td, $str);

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		return bin2hex($encrypted);
    }

    public function decrypt($code) {
		//$key = $this->hex2bin($key);
		$code = $this->hex2bin($code);
		$iv = ENCRYPTION_IV;

		$td = mcrypt_module_open('rijndael-128','', 'cbc', $iv);

		mcrypt_generic_init($td, ENCRYPTION_KEY, $iv);
		$decrypted = mdecrypt_generic($td, $code);

		mcrypt_generic_deinit($td);
		mcrypt_module_close($td);

		$decrypt_data = utf8_encode(trim($decrypted));
		$this->WriteTextFile($decrypt_data,'Request');
               
		return $decrypt_data;
    }

    protected function hex2bin($hexdata) {
	    $bindata = '';
	    for ($i = 0; $i < strlen($hexdata); $i += 2) {
	            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
	     }
	     return $bindata;
    }
	/**
	 * Function for generate Decrypted array from json encrypted string object.
	 *
	 * @param input
	 *
	 * @return decrypted array.
	 */
	public function GetDecryptedData($input){
            //echo 'hii'; die;
	       //$image=str_replace(array(''),'',$this->decrypt($input));
						 //$result=str_replace(array(''),'',$this->decrypt($input));						 	
               $result = (array)json_decode($this->decrypt($input['request']));
               return $result;
	}
	/**
	 * Function for generate Encrypted object from array.
	 *
	 * @param array
	 *
	 * @return encrypted object array.
	 */
	public function CreateEncryptedData($array){
		$result = (object)($this->encrypt(json_encode($array)));
		return $result;
	}
	/**
	 * Function for get distance from person lat long to target lat long.
	 *
	 * @param array of person lat long and array of target lat long
	 *
	 * @return distance in kilometer.
	 */
	public function CalculateDistance($person_lat_long, $target_lat_long){
		$lat1 	= 	$person[0];
		$lng1 	= 	$person[1];
		$lat2 	= 	$target[0];
		$lng2 	= 	$target[1];
		$pi 	= 	3.14159;
		$rad 	= 	doubleval($pi/180.0);

		$lon1 	= 	doubleval($lng1)*$rad;
		$lat1 	= 	doubleval($lat1)*$rad;
		$lon2 	= 	doubleval($lng2)*$rad;
		$lat2 	= 	doubleval($lat2)*$rad;
		$theta 	= 	$lng2 - $lng1;

		$dist 	= 	acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));

		if ($dist < 0)
			$dist += $pi;


		$miles 		= 	doubleval($dist * 69.1);
		$inches 	= 	doubleval($miles * 63360);
		$km  		= 	doubleval($dist * 115.1666667);

		$dist 		= 	sprintf( "%.2f",$dist);
		$miles 		= 	sprintf( "%.2f",$miles);
		$inches 	= 	sprintf( "%.2f",$inches);
		$km 		= 	sprintf( "%.2f",$km);
		//Here you can return whatever you please
		return $km;
	}
	
	/**
	 * Function for get distance from person lat long to target lat long.
	 *
	 * @param array of person lat long and array of target lat long
	 *
	 * @return distance in kilometer.
	 */
	public function CalculateDistanceMile($person_lat_long, $target_lat_long){
		$lat1 	= 	$person[0];
		$lng1 	= 	$person[1];
		$lat2 	= 	$target[0];
		$lng2 	= 	$target[1];
		$pi 	= 	3.14159;
		$rad 	= 	doubleval($pi/180.0);

		$lon1 	= 	doubleval($lng1)*$rad;
		$lat1 	= 	doubleval($lat1)*$rad;
		$lon2 	= 	doubleval($lng2)*$rad;
		$lat2 	= 	doubleval($lat2)*$rad;
		$theta 	= 	$lng2 - $lng1;

		$dist 	= 	acos(sin($lat1) * sin($lat2) + cos($lat1) * cos($lat2) * cos($theta));

		if ($dist < 0)
			$dist += $pi;


		$miles 		= 	doubleval($dist * 69.1);
		$inches 	= 	doubleval($miles * 63360);
		$km  		= 	doubleval($dist * 115.1666667);

		$dist 		= 	sprintf( "%.2f",$dist);
		$miles 		= 	sprintf( "%.2f",$miles);
		$inches 	= 	sprintf( "%.2f",$inches);
		$km 		= 	sprintf( "%.2f",$km);
		//Here you can return whatever you please
		return $miles;
	}

	public function Make_zip(){
		$zipName = $row['orderId'];
		$zip 		= 	new ZipArchive();
		$zip_file_name 	=	public_path('uploads/files/'.$zipName.'.zip');
		if ($zip->open($zip_file_name, ZipArchive::CREATE) === TRUE) {
			foreach($files_array as $file){
				$zip->addFile($file['path'], $file['name']);
			}
			$zip->close();
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.basename($zip_file_name));
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($zip_file_name));
			ob_clean();
			flush();
			readfile($zip_file_name);
			// Unlink All Text Files
			$dir = public_path('uploads/files/');
			array_walk(glob($dir . '/*'), function ($fn) {
			if (is_file($fn))
				unlink($fn);
			});
			File::delete($zip_file_name);
		}
	}
	/**
	 * Function for cut the string after given number of words and show special characters like dots after string.
	 *
	 * @param string,number of words,place character like dots
	 *
	 * @return distance in kilometer.
	 */
	public function LimitTheWords($string,$limit_words,$characters="..."){
		$txtArr = explode(" ",$string);
		if(count($txtArr)>$limit_words){
			$txtArr[$limit_words] = $characters;
			$arrReady = (array_slice($txtArr,0,$limit_words+1));
			return implode(" ",$arrReady);
		}else if(strlen($string)>50){
			return substr($string,1,50).'...';
		}else{
			return $string;
		}
	}
	/**
	 * Function for remove alphabetic characters from a string.
	 *
	 * @param string
	 *
	 * @return string without alphabatic characters.
	 */
	public function RemoveAlphabets($string){
		$res = preg_replace("/[^0-9,.]/", "",$string );
		return $res;
	}

	/**
	 * Function for array to object conversetion
	 *
	 * @param $array, &$obj
	 *
	 * @return string without alphabatic characters.
	 */

	public function array_to_obj($array, &$obj){
	    foreach ($array as $key => $value){
	      if (is_array($value)){
		      $obj->$key = new stdClass();
		      $this->array_to_obj($value, $obj->$key);
	      }else{
	        $obj->$key = $value;
	      }
	    }
	  	return $obj;
   	}
	public function GetTotalDaysInMonth($month_number,$year){
		$number = cal_days_in_month(CAL_GREGORIAN,$month_number,$year); // 31
		return $number;
	}
	function GetLastDayOfPayPeriod($start_day,$range,$total_days){
			//$total_days = 31;
			//$total_days = cal_days_in_month(CAL_GREGORIAN,$month,$year);
			$last_day 	=	$start_day+$range;
			if($last_day == $total_days){
				return $last_day;
			}elseif($last_day>$total_days){
				$extra_days = $last_day-$total_days;
				return $extra_days-1;
			}else{
				return $last_day-1;
			}
		}
	function GetPayPeriod($start_day,$last_day){
		$pay_period	=	$start_day.'th-'.$last_day.'th';
		return $pay_period;
	}
	public function GetDateRange($start_date,$pay_period_range){
				$end_date = date('Y-m-d', strtotime('+'.$pay_period_range.' day', strtotime($start_date)));
				$end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_date)));
				return $start_date.','.$end_date;
			}
	public function GetWeeksRangeInMonth($start_date,$pay_period_range){
		$end_date = date('Y-m-d', strtotime('+'.$pay_period_range.' day', strtotime($start_date)));
		$end_date = date('Y-m-d', strtotime('-1 day', strtotime($end_date)));
		return $start_date.','.$end_date;
	}
	public function GetMonthWeeksDateRange($start_date,$end_date,$month_number,$year){
		for($i=1;$i<=4;$i++){
			if($i == 4){
				$end_date 	=	$this->lastDay($month_number,$year);
			}else{	
				$end_date 	= 	date('Y-m-d', strtotime('+6 day', strtotime($start_date)));
			}	
			$week[]  	=	['start_date'=>$start_date,'end_date'=>$end_date];
			$start_date = 	date('Y-m-d', strtotime('+1 day', strtotime($end_date)));
			
		}
		return $week;
	}
	public function Ordinal($number) {
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if ((($number % 100) >= 11) && (($number%100) <= 13))
			return $number. 'th';
		else
			return $number. $ends[$number % 10];
	}	
	 /**
	 * Function for .
	 *
	 * @param string
	 *
	 * @return string without alphabatic characters.
	 */
	public function lastDay($month = '', $year = '') {
	   if (empty($month)) {
		  $month = date('m');
	   }
	   if (empty($year)) {
		  $year = date('Y');
	   }
	   $result = strtotime("{$year}-{$month}-01");
	   $result = strtotime('-1 second', strtotime('+1 month', $result));
	   return date('Y-m-d', $result);
	}
	public function firstDay($month = '', $year = ''){
		if (empty($month)) {
		  $month = date('m');
	   }
	   if (empty($year)) {
		  $year = date('Y');
	   }
	   $result = strtotime("{$year}-{$month}-01");
	   return date('Y-m-d', $result);
	}
	/**
	 * Function for get week range from given date.
	 *
	 * @param current date
	 *
	 * @return array of start and end date of week.
	 */
	public function GetWeekRange($date) {
		$ts 	= 	strtotime($date);
		$start 	= 	(date('w',$ts) == 0)?$ts:strtotime('last sunday',$ts);
		return array(date('Y-m-d', $start),
		date('Y-m-d', strtotime('next saturday', $start)));
	}
	public function GetFifteenthDate($date){
		$today 	= strtotime($date);  //GRAB THE DATE - YOU WOULD WANT TO REPLACE THIS WITH YOUR VALUE
		$year 	= date("Y", $today);  //GET MONTH AND YEAR VALUES
		$month 	= date("m", $today);
		return date("Y-m-d", mktime(0, 0, 0, $month, 15, $year)); //OUTPUT WITH MKTIME
 	 }
  	public function CheckInRange($week_range,$input_date){
		// Convert to timestamp
		$start_ts = strtotime($week_range[0]);
		$end_ts 	= strtotime($week_range[1]);
		$user_ts 	= strtotime($input_date);
		if(($user_ts >= $start_ts) && ($user_ts <= $end_ts)){
		  return 1;
		}else{
		  return 0;
		}
  	}
	/**
	 * Function for get maximum value from multidimensional array.
	 *
	 * @param array
	 *
	 * @return maximum value from array.
	 */
	public function FindMax($array){
		foreach($array as $value){
			unset($value['day_name']);
			$new_array[] = max($value);
		}
		return max($new_array);
	}

	 /**
     * function for clockout push notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function notification_api($device_id,$message){
	 		$obj 			= new stdClass();
			$obj_message 	= $this->array_to_obj($message,$obj);
			// API access key from Google API's Console
			$registrationIds = array( $device_id );
			$fields = array
			(
				'registration_ids' 	=> $registrationIds,
				'data'			=> $obj_message
			);

			$headers = array
			(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			);

			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
			$result = curl_exec($ch);
			$error = curl_error($ch);
			curl_close( $ch );
			if($result){
				return true;
			}else{
				return false;
			}
	 }

	 /**
     * function for Instant Announcement push notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function InstantAnnouncementNotification($user_ids=null,$announcement_id=null){
	 	// prd($user_ids);
	 		$notifications = Notifications::where('status','not_sent')->where('required_id',$announcement_id)->where('type','announcement')->where('announcement_type','instant')->whereIn('user_id',$user_ids)->get()->toArray();
			if($notifications){
				foreach ($notifications as $row) {
					$provider_id 		= $row['user_id'];
					$provider_data 		= ApiTokens::where('user_id',$provider_id)->first();
					if($provider_data){
						$provider_device_id = $provider_data->device_id;
						$announsement_data 	= AnnouncementModel::where('id',$row['required_id'])->first();
						$user_data 			= User_model::select('push_notification')->where('id',$provider_id)->first();
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
							$PushNotification 	=   $this->notification_api($provider_device_id,$notification_message);
							if($PushNotification){
								$notification_message['device_id'] = $provider_device_id;
								$this->update_notification($notification_message,$row['id']);
							}
						}
					}
				}
			}
	 }
	 
	 
	 /**
	 * function for Instant Clinic Update Notification
	 *
	 * @param null
	 *
	 * @return null.
	 */
	 public function ClinicUpdateNotification($provider_id=null,$id=null){
			$provider_data 		= ApiTokens::where('user_id',$provider_id)->first();
			if($provider_data){
				$provider_device_id = $provider_data->device_id;
				$clinic_data 	= ClinicsModel::where('id',$id)->first();
				$user_data 		= User_model::select('notification_groupby','push_notification')->where('id',$provider_id)->first();
				if($user_data->push_notification == 1){
					$notification_message = ["notification" => [
														          	"body" 	=> "Clinic is upadted!",
														          	"title" => "Clinic update notification"
														       	],
												"notification_data" => [
														    "clinic_id" => $clinic_data->id,
														    "type" 	  => 'clinic_update',
														],
											];
					if($this->notification_api($provider_device_id,$notification_message)){
						$notification_message['notification_data']['user_id'] = $provider_id;
						$notification_message['device_id'] = $provider_device_id;
						$this->save_notification($notification_message);
					}
				}
			}
	 }
	 /**
     * function for Clinic Update Notification By Crone
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function ClinicUpdateNotificationByCrone(){
			$notifications = Notifications::where('status','not_sent')->where('type','update_clinic')->get()->toArray();
			if($notifications){
				foreach ($notifications as $row) {
					$provider_id 		= $row['user_id'];
					$provider_data 		= ApiTokens::where('user_id',$provider_id)->first();
					if($provider_data){
						$provider_device_id = $provider_data->device_id;
						$clinic_data 	= ClinicsModel::where('id',$row['required_id'])->first();
						$user_data 			= User_model::select('notification_groupby','push_notification')->where('id',$provider_id)->first();
						if($user_data->push_notification == 1){
							$notification_message = ["notification" => [
																          	"body" 	=> "Clinic is upadted!",
																          	"title" => "Clinic update notification"
																       	],
														"notification_data" => [
																    "clinic_id" => $clinic_data->id,
																    "type" 	  	=> 'clinic_update',
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
				echo "success";
			}else{
				echo "no records available";
			}
			die;
	 }
	 /**
     * function for clockout push notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function save_notification($message = array()){
	 		if(!empty($message)){
	 			$model = new Notifications();
		 		$model->device_id = $message['device_id'];
		 		$model->message = json_encode($message);
		 		$model->user_id = $message['notification_data']['user_id'];
		 		$model->required_id = $message['notification_data']['clinic_id'];
		 		$model->type = $message['notification_data']['type'];
		 		$model->status = 'sent';
		 		$model->save();
			}
	 }
	 /**
     * function for save_announcement notification
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function update_notification($message,$id){
	 		if(!empty($message)){
	 			$model = Notifications::find($id);
		 		$model->device_id = $message['device_id'];
		 		$model->message = json_encode($message);
		 		$model->status = 'sent';
		 		$model->save();
			}
	 }
	 /**
	 * function for get drive time between two clicncs
	 *
	 * @param null
	 *
	 * @return response data.
	 */
	 function GetDrivingTime($lat1, $lat2, $long1, $long2){
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
	 /**
	 * function for get miles between two clicncs
	 *
	 * @param null
	 *
	 * @return response data.
	 */
	 function GetDrivingMiles($lat1, $lat2, $long1, $long2){
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
			$time_in_meter = isset($response_a['rows'][0]['elements'][0]['distance']['value'])?($response_a['rows'][0]['elements'][0]['distance']['value']):0; // In seconds
			$time_in_miles  = $time_in_meter/1609.34; // covert into minuts
			$miles 			  = number_format($time_in_miles,2);
			return $miles;
		 }else{
		 	return 0;
		 }
	 }
	 /**
	 * function for user full name by id
	 *
	 * @param id
	 *
	 * @return full name of user on success otherwise false.
	 */
	 public function GetNameById($id){
		 $user = DB::table('users')->select('first_name','last_name')->where('id',$id)->get()->first();
		 if(!empty($user)){
			return $user->first_name.' '.$user->last_name;
		 }else{
			 return false;
		 }
	 }
	 /**
	 * function for check given special character present or not in string
	 *
	 * @param string,special character
	 *
	 * @return true when get given character present in sting otherwise false.
	 */
	 public function CheckCharacterInString($string,$special_character){
		if (strpos($string,$special_character) !== false) {
			return true;
		}else{
			return false;
		}
	 }

	 /**
     * function for save admin notifications
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function save_admin_notification($required_id,$type,$notification_type,$message,$user_id=null){
 		if($required_id){
 			$model = new AdminNotifications();
	 		$model->required_id = $required_id;
	 		$model->message = $message;
	 		$model->user_id = $user_id;
	 		$model->type 	= $type;
	 		$model->notification_type 	= $notification_type;
	 		$model->status 	= 'sent';
	 		$model->save();
		}
	 }
	 
	 public function GetCityNameFromLatLng($latitude,$longitude){
		$lat = $latitude;
		$lng = $longitude;
		$data = file_get_contents("http://maps.google.com/maps/api/geocode/json?latlng=$lat,$lng&sensor=false");
		$data = json_decode($data);
		$add_array  = $data->results;
		if($add_array){
		$add_array = $add_array[0];
		$add_array = $add_array->address_components;
		$country = "Not found";
		$state = "Not found"; 
		$city = "Not found";
		foreach ($add_array as $key) {
			  if($key->types[0] == 'administrative_area_level_2')
			  {
				$city = $key->long_name;
			  }
			  if($key->types[0] == 'administrative_area_level_1')
			  {
				$state = $key->long_name;
			  }
			  if($key->types[0] == 'country')
			  {
				$country = $key->long_name;
			  }
			}
			return $city;
		}else{
			return false;
		}
		
	 }
	/*  function for write text file for request and response data.
	/*  
	/*  @param userid.
	/*  
	/*	return none  
	/*  
	*/
	public function WriteTextFile($string,$type){
		$filename	=	'RequestResponse.txt';
		$new_line	=	"\n";
		$filepath	=	public_path('/temp/'.$filename);
		// getting old data from file
		$content 	= 	File::get($filepath);
		// new data
		$data 		=	$type.':-'.$string;
		// merging new data with old data
		$final_data = $content.$new_line.$data;
		File::put($filepath,$final_data);
	} 
	/*  function for get distance between two coordinates
	/*  
	/*  @param lat1,lng1,lat2,lng2.
	/*  
	/*	return result in kilometers  
	/*  
	*/
	public static function getDistanceBetweenPoints($lat1, $lon1, $lat2, $lon2) {
		$theta = $lon1 - $lon2;
		$miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
		$miles = acos($miles);
		$miles = rad2deg($miles);
		$miles = $miles * 60 * 1.1515;
		$kilometers = $miles * 1.609344;
		return number_format($kilometers,2);
	}
}
