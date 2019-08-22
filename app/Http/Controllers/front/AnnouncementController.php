<?php

namespace App\Http\Controllers\front;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\CertificationModel;
use App\Model\AnnouncementModel;
use App\Model\AnnouncementStatusModel;
use App\Model\User_model;
use App\Model\ProvidersModel;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AnnouncementController extends BaseController
{
	  /**
     * function for get latest 3 records from database.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function LatestAnnouncement(){
		$announcement 	=	AnnouncementModel::GetLatest();
		if(!empty($announcement)){
			foreach($announcement as $announce){
				$announce['image_path'] = 	WEBSITE_UPLOADS_URL.'announcement/'.$announce['image'];
				$announce['created_at']	=	date("d-m-Y", strtotime($announce['created_at']));
				$ann[] = $announce;
			}
			return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok','announcement'=>$ann)));
		}else{
			return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found')));
		}
	 }
	 /**
     * function for get all records of announcement.
     *
     * @param null
     *
     * @return response data on success otherwise error.
     */
	 public function AllAnnouncement(){
		$input_data = $this->GetDecryptedData(Input::get()['request']);
		$rules = array(
		 'user_id'       		=> 'required',
		 );
		$validator = Validator::make($input_data, $rules);
		 if ($validator->fails()) {
			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
		 } else {
				//$input_data['user_id'] = 82;
					$user_id		=	$input_data['user_id'];
					$user			=	User_model::GetUserById($user_id);
					if(!empty($user)){
						$announcement 	=	AnnouncementModel::GetAll();
						//prd($announcement);
						if(!empty($announcement)){
							foreach($announcement as $announce){
							$is_user_announcement  = 0;
								if($announce['visible_providers']==null || $announce['visible_providers']=='' || $announce['visible_cities']==null || $announce['visible_cities']== ''){
									$is_user_announcement  = 1;
								}else{
									//$is_user_announcement  = 0;
									$user_city     = $user['city_name'];
									$exploded_data = explode(',',$announce['visible_providers']);
									$exploded_cities = explode(',',$announce['visible_cities']);
									
									if(in_array($user_id,$exploded_data)){
										$is_user_announcement  = 1;
									}else if(in_array($user_city,$exploded_cities)){
										$is_user_announcement  = 1;
									}
								}
								if($announce['stable_time']== null){
									$default_stay_time			= $this->GetAdminSettingsValue('default_announcemnet_stay_feeds');
									$stay_time							= ($default_stay_time*24);
								}else{
									$stay_time = $announce['stable_time'];
								}
								$estimated_time_addition							=	'+'.$stay_time.' hour';
								$announcement_time_after_add 					= strtotime($estimated_time_addition,strtotime($announce['created_at']));
								$announcement_end_time								= date('Y-m-d H:i:s',$announcement_time_after_add);
								$current_time 												= date('Y-m-d H:i:s');

								if($current_time <= $announcement_end_time && $is_user_announcement==1){
									if($announce['image'] != null){
										$announce['image_path'] = 	WEBSITE_UPLOADS_URL.'announcement/'.$announce['image'];
									}else{
										$announce['image_path'] = "";
									}
									$announce['created_at']	=	date("d-m-Y", strtotime($announce['created_at']));
									$ann[] = $announce;
									}
								}
								if(isset($ann)){
									return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok','announcement'=>$ann)));
								}else{
									return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found')));
								}
				        }else {
				            return $this->encrypt(json_encode(array('status'=>'error','message'=>'No records found')));
				        }
					}else{
						return $this->encrypt(json_encode(array('status'=>'error','message'=>'User not found.')));
					}
			}
	 }
	 /**
     * function for deactive announcement status.
     *
     * @param announcement id
     *
     * @return response data on success otherwise error.
     */
		 public function DeactiveAnnouncement(){
			 $input_data = $this->GetDecryptedData(Input::get()['request']);
	 		$rules = array(
	 		'platform_type'     => 'required',
	 		'user_id'       		=> 'required',
			'announcement_id'   => 'required',
	 	   );
	 		$validator = Validator::make($input_data, $rules);
	 		 if ($validator->fails()) {
	 			 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
	 		 } else {
	 					$user_id						=	$input_data['user_id'];
						$announcement_id		=	$input_data['announcement_id'];
	 					$user			=	User_model::GetUserById($user_id);
	 					if(!empty($user)){
	 						// calls update profile photo function for update
	 						$last_id 	=	AnnouncementStatusModel::ChangeAnnouncementStatus($user_id,$announcement_id);
	 						if($last_id){
	 							return $this->encrypt(json_encode(array('status'=>'success','message'=>'Announcement successfully declined.')));
	 						}else{
	 							return $this->encrypt(json_encode(array('status'=>'error','message'=>'Technical error please try again later.')));
	 						}
	 					}else{
	 						return $this->encrypt(json_encode(array('status'=>'error','message'=>'User not found.')));
	 					}
	 			}
		 }
	 /**
     * function for get announcement details from given announcement id.
     *
     * @param announcement Id
     *
     * @return response data on success otherwise error.
     */
		 public function GetAnnouncement(){
			 $input_data 		= 	$this->GetDecryptedData(Input::get()['request']);
			 $rules = array(
			  'user_id' 										=> 'required',
			  'announcement_id' 						=> 'required',
			  'device_id'  		=> 'required',
			  'platform_type' => 'required',
			  );
			  $validator = Validator::make($input_data,$rules);
			  if ($validator->fails()){
				 $messages = $validator->messages();
				 return $this->encrypt(json_encode(array('status' => 'error', 'message' => $validator->errors()->all())));
			  }else{
				$announcements = AnnouncementModel::GetAnnouncementById($input_data['id']);
				if(!empty($announcements)){
					foreach($announcements as $announcement){
						$announcement['file_path'] 	= 	WEBSITE_UPLOADS_URL.'announcement/'.$announcement['image'];
						$announce[] = $announcement;
					}
					return $this->encrypt(json_encode(array('status'=>'success','message'=>'ok.','announcement'=>$announce)));
				}else{
					return $this->encrypt(json_encode(array('status'=>'error','message'=>'Not found.')));
				}
			  }
		 }
}
