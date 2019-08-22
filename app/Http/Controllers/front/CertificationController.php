<?php

namespace App\Http\Controllers\front;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\CertificationModel;
use App\Model\User_model;
use App\Model\AnnouncementModel;
use App\Model\ProvidersModel;
use App\Model\Notifications;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Image;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CertificationController extends BaseController
{
	 /**
     * function for add or upload certificates of providers.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	  public function Add(Request $request){
		  $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		  $rules = array(
		  'user_id' 				=> 'required',
		 	// 'file'  				=> 'required',
		  'device_id'  			=> 'required',
		  'subject'  				=> 'required',
		  'description'  		=> 'required',
		  'platform_type'  	=> 'required',
		  'date'   					=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
			$file	=	Input::file('file');
			if($file){
				$filename 			=  	time().'_'.$file->getClientOriginalName();
				$file->move(public_path('uploads/certificates/') , $filename);
				$fileExtension 	= $file->getClientOriginalExtension();
			}
			$result_id		  			= 	CertificationModel::SaveCertificate($filename,$fileExtension,$input_data);
			if($result_id != null){
				$message = 'Submission of new certifications doc';
            	$this->save_admin_notification($result_id,'certification','new_certifications',$message);
				// get certificate from last insert id.
				$savedcertificate 	=  CertificationModel::GetCertificateById($result_id); 
			}

			foreach($savedcertificate as $certificate){
				$certificate['file_path'] 	= 	WEBSITE_UPLOADS_URL.'certificates/'.$certificate['file'];
				$certificate_data[] 		= 	$certificate;
			}
			return $this->encrypt(json_encode(array('status'=>'success','message'=>'Certificate successfully uploaded.','certificate'=>$certificate_data)));
	    }
    }
	/**
     * function for get all .
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function GetCertificates(){
		 $config_date 		= Config::get('date_format.date');
		 $config_month 		= Config::get('date_format.month');
		 $config_year 		= Config::get('date_format.year');
		 $config_separator 	= Config::get('date_format.separator');
		 
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 				=> 'required',
		  'device_id'  			=> 'required',
		  'platform_type'  	=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{

			$certificates = CertificationModel::GetCertificatesByUserId($input_data['user_id']);
			if(!empty($certificates)){
				foreach($certificates as $certificate){
					if (strpos($certificate['date'], '|') !== false) {
						$date = explode('|',$certificate['date']);
					}else{
						$date = explode('-',$certificate['date']);
					}
					$my_date[] = $date;
 					$new_date					=	trim($date[0]).'-'.trim($date[1]).'-'.trim($date[2]);
					$certificate['file_path'] 	= 	WEBSITE_UPLOADS_URL.'certificates/'.$certificate['file'];
					$certificate['description']	=	$this->LimitTheWords($certificate['description'],10);
					$certificate['date'] 		= 	$clinic['date'] 	= date($config_month.$config_separator.$config_date.$config_separator.$config_year, strtotime($new_date));
					$certi[] 					= 	$certificate;

				}
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok.','certificate'=>$certi)));
			}else{
				return $this->encrypt(json_encode(array('status'=>'error','message'=>'Not found.')));
			}
		  }
	 }
	 /**
     * function for get certificate details from given certificate id.
     *
     * @param certificate Id
     *
     * @return response data on success otherwise error.
     */
	 public function GetCertificate(){
		 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
		 $rules = array(
		  'user_id' 				=> 'required',
		  'certificate_id' 	=> 'required',
		  'device_id'  			=> 'required',
		  'platform_type'  	=> 'required',
		  );
		  $validator = Validator::make($input_data,$rules);
		  if ($validator->fails()){
			 $messages = $validator->messages();
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		  }else{
			$certificates = CertificationModel::GetCertificateById($input_data['certificate_id']);
			if(!empty($certificates)){
				foreach($certificates as $certificate){
					$certificate['file_path'] 	= 	WEBSITE_UPLOADS_URL.'certificates/'.$certificate['file'];
					$certi[] = $certificate;
				}
				return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok.','certificate'=>$certi)));
			}else{
				return $this->encrypt(json_encode(array('status'=>'error','message'=>'Not found.')));
			}
		  }
	 }
	 /**
		* function for Delete certificate
		*
		* @param null
		*
		* @return user data.
		*/
	public function DeleteCertificate(){
	 $input_data = $this->GetDecryptedData(Input::get()['request']);
	 $rules = array(
	 //'device_id'        						=> 'required',
	 'platform_type'      					=> 'required',
	 'user_id'       						=> 'required',
	 'certificate_id'      					=> 'required',
		);
	 $validator = Validator::make($input_data, $rules);
		if ($validator->fails()) {
			return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		} else {
				 $user_id					=	$input_data['user_id'];
				 $certificate_id			=	$input_data['certificate_id'];
				 $user			=	User_model::GetUserById($user_id);
				 if(!empty($user)){
						 // calls function for delete certificate
							 $delete 	=	CertificationModel::DeleteCertificate($user_id,$certificate_id);
							 return $this->encrypt(json_encode(array('status'=>'success','message'=>'Successfully deleted.')));
				 }else{
					 return $this->encrypt(json_encode(array('status'=>'error','message'=>'User not found.')));
				 }
		 }
	}
}
