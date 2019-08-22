<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\CertificationModel;
use App\Model\admin\ProvidersModel;
use App\Model\admin\AdminNotifications;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use DateTime;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast,Image,Zipper;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CertificationController extends BaseController
{
  public function index($provider_id=''){
  $providers = ProvidersModel::where('role_id',0)->orderBy('id','desc')->get();
  if($provider_id==''){
    $certifications =   DB::table('certifications')
                          ->join('users', function ($join) {
                              $join->on('users.id', '=', 'certifications.user_id');
                          })
                          ->get();
      return  View::make('admin.certifications.index',compact('certifications','providers'));
  }
  else{
    $certifications =   DB::table('certifications')
                          ->join('users', function ($join) {
                              $join->on('users.id', '=', 'certifications.user_id');
                          })
                          ->where('certifications.user_id',$provider_id)
                          ->get();
      return  View::make('admin.certifications.providerindex',compact('certifications'));
  }
  }
  public function add($provider_id=""){
   Input::replace($this->arrayStripTags(Input::all()));
    if(Input::isMethod('post')){
      $rules = array(
        'user_id'    	=> 'required',
        'file'  		=> 'required',
      );
    $validator = Validator::make(Input::all(),$rules);
      if ($validator->fails()){
          $messages = $validator->messages();
         return Redirect::back()->withErrors($validator)->withInput();
      }else{
        $file 			= 	Input::file('file');
        $filename 		=   time().$file->getClientOriginalName();
        $fileExtension 	= 	$file->getClientOriginalExtension();
        $AllowedExts 	= 	array("jpeg", "jpg", "JPG", "png", "docx", "doc", "DOCX", "pdf", "PDF");
			if(in_array($fileExtension,$AllowedExts)){
			  $file->move(public_path('uploads/certificates/') , $filename);
         //calls function for create provider.
        $certificate  =   CertificationModel::SaveCertificate($filename,$fileExtension); 
		Toast::success('Certificate successfully added');
        if($certificate){
				$certificate_data		=	DB::table('certifications')->where('certificate_id',$certificate)->get();
				$user_data				=	DB::table('users')->where('id',$certificate_data[0]->user_id)->get();
				$provider_name			=	$user_data[0]->first_name.' '.$user_data[0]->last_name;
				$provider_email			=	$user_data[0]->email;
               $message = 'Submission of new certifications doc';
			   $all_admins   = DB::table('users')->where('role_id',1)->where('status',1)->get();
			    $file_path = 	WEBSITE_UPLOADS_URL.'certificates/'.$certificate_data[0]->file;
			    foreach($all_admins as $admin){
					$admin_id     = $admin->id;
					$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
					if($notification_type == 'email'){
					// email sending process starts
					$type 					=	'new_certifications';
					$check_status			=	$this->CheckMailSentStatus($certificate,$admin->id,$type);
					if($check_status == 0){
						$subject_replace		=   array($admin->first_name.' '.$admin->last_name,$provider_name);
						$replace_variables =   array($admin->first_name.' '.$admin->last_name,$provider_name,$provider_email,$file_path);
						$email_send    =   $this->mail_send('new_certifications',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_variables,null,$certificate,$admin->id,$type);
					}	
					// email sending process ends
					}elseif($notification_type == 'push'){
						if(($admin->certifications_notify != '' || $admin->certifications_notify != null) && $admin->certifications_notify != 'off'){
						$admin_notifications 	= 	AdminNotifications::where('required_id',$certificate)->where('user_id',$admin_id)->where('type','certification')->where('notification_type','new_certifications')->get()->count();
						if($admin_notifications == 0){
							$this->save_admin_notification($certificate,'certification','new_certifications',$message,$admin_id);
						}
					}
					}elseif($notification_type == 'both'){
						if(($admin->certifications_notify != '' || $admin->certifications_notify != null) && $admin->certifications_notify != 'off'){
						$admin_notifications 	= 	AdminNotifications::where('required_id',$certificate)->where('user_id',$admin_id)->where('type','certification')->where('notification_type','new_certifications')->get()->count();
							if($admin_notifications == 0){
								$this->save_admin_notification($certificate,'certification','new_certifications',$message,$admin_id);
							}
							// email sending process starts
							$type 					=	'new_certifications';
							$check_status			=	$this->CheckMailSentStatus($certificate,$admin->id,$type);
							if($check_status == 0){
								$subject_replace		=   array($admin->first_name.' '.$admin->last_name,$provider_name);
								$replace_variables =   array($admin->first_name.' '.$admin->last_name,$provider_name,$provider_email,$file_path);
								$email_send    =   $this->mail_send('new_certifications',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_variables,null,$certificate,$admin->id,$type);
							}	
							// email sending process ends
						}
					}elseif($notification_type == 'none'){
						// no notifications goes nothing
					}
				}
		}
				if($provider_id == ""){
					return redirect()->route('certifications');
				}else{
					return redirect()->route('view-certificates',$provider_id);
				}
			}
			else{
			  Toast::error('Only jpg png pdf & doc file are allowed to upload');
			  return Redirect::back()->withInput();
			}
		}
  }else{
      $providers = ProvidersModel::where('role_id',0)->orderBy('id','desc')->get();
      return View::make('admin.certifications.add',compact('providers','provider_id'));
     }
 }
 /**
  * Function to edit certificates
  *
  * @param user id (default null )
  *
  * @return certificates list page
  */
  public function edit($id=0){
   if(Input::isMethod('post')){
      $rules = array(
        'subject'  		           => 'required',
      );
    $validator = Validator::make(Input::all(),$rules);
     if ($validator->fails()) {
      $messages = $validator->messages();
      return Redirect::back()->withErrors($validator)->withInput();
      } else {
        if(Input::file('file')){
          $file = Input::file('file');
          $filename =   time().$file->getClientOriginalName();
          $file->move(public_path('uploads/certificates/') , $filename);
          $fileExtension = $file->getClientOriginalExtension();
          }
        else{
          $certificate_data			=	CertificationModel::where('certificate_id',$id)->first();  // getting certificate data by id.
          $filename	=	$certificate_data->file;
          $fileExtension = $certificate_data->type;
        }
        $UpdateCertificate = CertificationModel::UpdateCertificate($filename,$fileExtension,$id);    // calls function for update provider data.
        if($UpdateCertificate){
           Toast::success('Certificate successfully updated');
          return redirect()->route('certifications');
        }else{
           Toast::error('Technical error');
          return redirect()->route('certifications');
        }
          }
     }else{
            $certificate = CertificationModel::where('certificate_id',$id)->first();
            $providers = ProvidersModel::where('role_id',0)->orderBy('id','desc')->get();
            return view('admin.certifications.edit', compact('certificate','providers'));
     }
  }
  /**
     * Function for delete certificate
     *
     * @param null
     *
     * @return view page.
     */
     public function delete($id = ''){
       if($id){
         // Delete row
         CertificationModel::where('certificate_id',$id)->delete();
         Toast::success('Certificate successfully deleted');
         return Redirect::back();
       }else{
           $checkboxdata = Input::get('chk_ids');
           $ids = explode(",", $checkboxdata);
            $success = CertificationModel::whereIn('certificate_id',$ids)->delete();
           if($success != null){
             echo 1;
           }
           else{
             echo 0;
           }
          }
     }
		/**
        * Function for download certificates
        *
        * @param null
        *
        * @return view page.
        */
        public function download(){
		if(Input::get('chk_ids') != ""){
			$checkid = Input::get('chk_ids');
			$certificate_id = explode(',',$checkid);
          //$files = CertificationModel::whereIn('certificate_id',$certificate_id)->get()->toArray();
          $certifications =   DB::table('certifications')
                                ->join('users', function ($join) {
                                    $join->on('users.id', '=', 'certifications.user_id');
                                })
                                ->whereIn('certificate_id',$certificate_id)
                                ->get()->toArray();
		}else if(Input::get('provider_id') != ""){
			$provider_id = Input::get('provider_id');
			$certifications =   DB::table('certifications')
                                ->join('users', function ($join) {
                                    $join->on('users.id', '=', 'certifications.user_id');
                                })
                                ->where('user_id',$provider_id)
                                ->get()->toArray();
		}else{
			$certifications =   DB::table('certifications')
                                ->join('users', function ($join) {
                                    $join->on('users.id', '=', 'certifications.user_id');
                                })
                                ->get()->toArray();
		}
		   $folder_name = time().'_wellness_certificates';
		   if (!file_exists(public_path('/uploads/temp/'.$folder_name))) {
				File::makeDirectory(public_path('/uploads/temp/'.$folder_name,$mode=0777,true));
			}
		if(!empty($certifications)){
			foreach ($certifications as $certificate) {
				if(!File::exists(public_path('/uploads/temp/'.$folder_name.'/'.$certificate->first_name))) {
							   File::makeDirectory(public_path('/uploads/temp/'.$folder_name.'/'.$certificate->first_name,$mode=0777,true));
					 }
				  $sourceFilePath   = 	public_path()."/uploads/certificates/".$certificate->file;
				  $destinationPath  = 	public_path()."/uploads/temp/".$folder_name.'/'.$certificate->first_name.'/'.$certificate->file;
				  $success          = 	File::copy($sourceFilePath,$destinationPath);
			  }
				$zipper 			= 	new \Chumper\Zipper\Zipper;
				$folder 			= 	glob(public_path('/uploads/temp/'.$folder_name));
				$zipper->make('public/uploads/temp/'.$folder_name.'.zip')->add($folder)->close();
				$zipper->close();
				$pathtoFile 		= 	public_path('uploads/temp/'.$folder_name.'.zip');
				$zip_file_url 		= 	WEBSITE_UPLOADS_URL.'temp/'.$folder_name.'.zip';
				echo $zip_file_url;
		}else{
			echo 0;
		}
		die;
    }
     // function to display certificate data in databale using ajax
       /**
      * Function for get users data use in ajax datatable pagination.
      *
      * @param null
      *
      * @return users ajax list page.
      */
      public function ajaxloadcerificate(){
        $columns = Input::get('columns');
        $length								= 	Input::get('length');
        $start								  = 	Input::get('start');
        $totaldata 						= 	CertificationModel::count();
        $total_filtered_data	  =		$totaldata;
        $order							    =		Input::get('order');
        $column_id						  =		$order[0]['column'];
        $column_order					=		$order[0]['dir'];
        $searchdata                  = $columns[1]['search']['value'];
        //prd($search);
        $search =   ltrim($searchdata, ',');
        $search_ids = explode(",", $search);
        if($search){
            if(in_array('all',$search_ids)){
              $totaldata 						= 	CertificationModel::count();
              $total_filtered_data	  =		$totaldata;
            }
            else{
              $totaldata 						= 	CertificationModel::whereIn('user_id',$search_ids)->count();
              $total_filtered_data	  =		$totaldata;
            }
          $certificates		=		CertificationModel::Get_Filtered_Certificates($search,$start,$length,$column_id,$column_order);
        }
        else{
          $certificates			=	CertificationModel::Get_Filtered_Certificates($search="",$start,$length,$column_id,$column_order);
        }
        $table_data		=	array();
        return view('admin.certifications.indexajax', ['certificates' => $certificates,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
      }

      public function get_filter_provider(){
        $provider_id  = Input::get('userId');
        $providers = ProvidersModel::where('role_id',0)->orderBy('id','desc')->get();
        $certifications =   DB::table('certifications')
                              ->join('users', function ($join) {
                                  $join->on('users.id', '=', 'certifications.user_id');
                              })
                              ->where('certifications.user_id',$provider_id)
                              ->get();
          return  View::make('admin.certifications.filtered_provider_index',compact('certifications','providers'));
      }
      public function delete_simple_table_data(){
            $checkboxdata = Input::get('chk_ids');

             $success = CertificationModel::whereIn('certificate_id',$checkboxdata)->delete();
             Toast::success('Certificates successfully deleted');
             return Redirect::back();
      }
}
