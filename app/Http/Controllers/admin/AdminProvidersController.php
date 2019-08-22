<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\ProvidersModel;
use App\Model\admin\CertificationModel;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use App\Model\admin\AdminNotifications;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast,Carbon,Image;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminProvidersController extends BaseController
{
    public function index(){
		$providers = ProvidersModel::where('role_id',0)->orderBy('id','desc')->get();
      return  View::make('admin.providers.index',compact('providers'));
    }
    // function to display user data in databale using ajax
      /**
     * Function for get users data use in ajax datatable pagination.
     *
     * @param null
     *
     * @return users ajax list page.
     */
     public function ajaxloadprovider(){
       $length					= 	Input::get('length');
       $start					= 	Input::get('start');
       $search					= 	Input::get('search');
	   $totaldata 				= 	ProvidersModel::where('role_id',0)->get()->count();
       //$totaldata 				= 	ProvidersModel::CountProviders(); // function counts records in provider table.
       $total_filtered_data	  	=	$totaldata;
       $search					=	$search['value'];
       $order					=	Input::get('order');
       $column_id				=	$order[0]['column'];
       $column_order			=	$order[0]['dir'];
       if($search != null){
         $providerdetails		=		ProvidersModel::GetProviders($search,$start,$length,$column_id,$column_order);   // calls function for getting users data.
       }else{
         $providerdetails			=	ProvidersModel::GetProviders($search="",$start,$length,$column_id,$column_order);
       }
       $table_data		=	array();
       return view('admin.providers.indexajax', ['providerdetails' => $providerdetails,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
     }
    public function add(){
		 Input::replace($this->arrayStripTags(Input::all()));
			if(Input::isMethod('post')){
				$rules = array(
				'first_name'    	      => 'required',
				'last_name'  		      => 'required',
				'email'      		      => 'required|email|unique:users,email',
				'password'   		      => 'required',
				'phone'  			      => 'required',
				'location'  			  => 'required',
				'social_security_number'  => 'required|numeric|digits:4',
				'provider_type'  		  => 'required',
				'hourly_rate'  			  => 'required',
				);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()){
					$messages = $validator->messages();
					 return Redirect::back()->withErrors($validator)->withInput();
				}else{
					$cityname = $this->GetCityNameFromLatLng(Input::get('lat'),Input::get('lng'));
					$image			=		Input::file('image');
					  if(!empty($image)){
						  /* image uploading process start */
						  $filename  		= 	time().'.'.$image->getClientOriginalExtension();
						  $fileExtension 	= 	$image->getClientOriginalExtension();
						  $AllowedExts 		= 	array("jpeg", "jpg", "JPG", "png");
						  if(in_array($fileExtension,$AllowedExts)){
							$path 	= 	public_path('uploads/users/'.$filename);
							Image::make($image->getRealPath())->save($path);
						  }else{
							  Toast::error('Only jpg png allowed');
							  redirect::back()->withInput();
						  }
					  }
					 if(isset($filename)){
						 $userimg  = $filename;
					 }else{
						 $userimg  = null;
					 }
					 if(isset($cityname)){
						 $cityname  = $cityname;
					 }else{
						 $cityname  = null;
					 }
					
					$provider_id 	= 	ProvidersModel::SaveProvider($cityname,$userimg);  //calls function for create provider.
					 Toast::success('Provider successfully added');
             if($provider_id){
               // collecting user information for sending email.
               $name          =   Input::get('first_name').' '.Input::get('last_name');
               $email_to      =   Input::get('email');
               $password      =   Input::get('password');
               $subject_array =   array($name);
               $replace_array =   array($name,$email_to,$password);
               $email_send    =   $this->mail_send('welcome_to_wellness',$email_to,$name,$subject_array,$replace_array);
               // process to send email to all the admin according to admin settings starts here//
               $all_admins   = DB::table('users')->where('role_id',1)->get();
				foreach($all_admins as $admin){
					$admin_id     = $admin->id;
					$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
					if($notification_type == 'email'){
					// email sending process starts
					$type 					=	'new_provider_added';
					$check_status			=	$this->CheckMailSentStatus($provider_id,$admin->id,$type);
					if($check_status == 0){
						$replace_variables =   array($admin->first_name.' '.$admin->last_name,$email_to,$password);
						$subject_variables =   array($admin->first_name.' '.$admin->last_name,$name);
						$email_send    =   $this->mail_send('new_provider_added',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_variables,$replace_variables,null,$provider_id,$admin->id,$type);
					}	
					// email sending process ends
					}elseif($notification_type == 'push'){
						$message       =   'Addition of new user'.' '.$name;
						$all_admins   = DB::table('users')->where('role_id',1)->where('status',1)->get();
						foreach($all_admins as $admin){
							$admin_id     = $admin->id;
							if(($admin->user_added_notify != '' || $admin->user_added_notify != null) && $admin->user_added_notify != 'off'){
								$admin_notifications 	= 	AdminNotifications::where('required_id',$provider_id)->where('user_id',$admin_id)->where('type','provider')->where('notification_type','new_user')->get()->count();
								if($admin_notifications == 0){
									$this->save_admin_notification($provider_id,'provider','new_user',$message,$admin_id);
								}
							}
						}
					}elseif($notification_type == 'both'){
						$message       =   'Addition of new user'.' '.$name;
						$all_admins   = DB::table('users')->where('role_id',1)->where('status',1)->get();
						foreach($all_admins as $admin){
							$admin_id     = $admin->id;
							if(($admin->user_added_notify != '' || $admin->user_added_notify != null) && $admin->user_added_notify != 'off'){
								$admin_notifications 	= 	AdminNotifications::where('required_id',$provider_id)->where('user_id',$admin_id)->where('type','provider')->where('notification_type','new_user')->get()->count();
								if($admin_notifications == 0){
									$this->save_admin_notification($provider_id,'provider','new_user',$message,$admin_id);
								}
								// email sending process starts
								$type 					=	'new_provider_added';
								$check_status			=	$this->CheckMailSentStatus($provider_id,$admin->id,$type);
								if($check_status == 0){
									$replace_variables =   array($admin->first_name.' '.$admin->last_name,$email_to,$password);
									$subject_variables =   array($admin->first_name.' '.$admin->last_name,$name);
									$email_send    =   $this->mail_send('new_provider_added',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_variables,$replace_variables,null,$provider_id,$admin->id,$type);
								}	
								// email sending process ends
							}
						}
						
					}elseif($notification_type == 'none'){
						// no notifications goes nothing
					}
				 }   
             }
				return redirect()->route('providers');
			}
		}else{
				$timezones = DB::table('timezone')->orderBy('timezone_name','ASC')->get();
				return View::make('admin.providers.add',compact('timezones'));
			 }
	 }
   /**
* Function to edit provider
*
* @param user id (default null )
*
* @return providers list page
*/
	public function edit($id=0){
	 if(Input::isMethod('post')){
     if(Input::get('social_security_number')!=null){
       $security_number_validation = 'required|digits:4';
       }else{
         $security_number_validation = '';
       }
			$rules = array(
        'first_name'    	     	=> 'required',
        'last_name'  		        => 'required',
        'email'      		        => 'required|email|unique:users,id',
        'phone'  			        => 'required',
        'location'  			    => 'required',
        'social_security_number' 	=> $security_number_validation,
        'provider_type'  			=> 'required',
        'hourly_rate'  			    => 'required',
			);
		$validator = Validator::make(Input::all(),$rules);
		 if ($validator->fails()) {
			$messages = $validator->messages();
			return Redirect::back()->withErrors($validator)->withInput();
		  } else {
					$cityname = $this->GetCityNameFromLatLng(Input::get('lat'),Input::get('lng'));
					if(Input::file('image')){
					  $fileExtension = Input::file('image')->getClientOriginalExtension();
					  $AllowedExts = array("jpeg", "jpg", "JPG", "png");
						if(in_array($fileExtension,$AllowedExts)){
						  $filename  = 	$this->ImageUpload(Input::file('image'),$folder='users',$resize=false);
						}
						else{
							Toast::error('Only jpg png file are allowed to upload');
							return Redirect::back()->withInput();
						}
					  }else{
						$user			=	ProvidersModel::where('id',$id)->first();  // getting announcement data by id.
						$filename		=	$user->image;
					  }	
						 if(isset($filename)){
							 $userimg  = $filename;
						 }else{
							 $userimg  = null;
						 }
						 if(isset($cityname)){
							 $cityname  = $cityname;
						 }else{
							 $cityname  = null;
						 }
					$UpdateProvider = ProvidersModel::UpdateProvider($id,$cityname,$userimg);// calls function for update provider data.
						if($UpdateProvider){
							 Toast::success('Provider successfully updated');
							return redirect()->route('providers');
						}else{
							 Toast::error('Technical error');
							return redirect()->route('providers');
						}
					}
	   }else{
		   $timezones = DB::table('timezone')->orderBy('timezone_name','ASC')->get();
            $provider = ProvidersModel::where('id',$id)->first();
						return view('admin.providers.edit', compact('provider','timezones'));
	   }
	}
  public function active_status($id=0){
      $category = ProvidersModel::where('id',$id)->first();
  			if($category->status == '1'){
  				ProvidersModel::where('id',$id)->update(['status' => '0']);
  				Toast::success('Provider successfully deactivated');
  				return redirect()->route('providers');
  			}else{
  				ProvidersModel::where('id',$id)->update(['status' => '1']);
  				Toast::success('Provider successfully activated');
  				return redirect()->route('providers');
  			}
   }
   public function active_status_provider($id=0){
      $category = ProvidersModel::where('id',$id)->first();
  			if($category->status == '1'){
  				ProvidersModel::where('id',$id)->update(['status' => '0']);
  				Toast::success('Provider successfully deactivated');
  				return redirect()->back();
  			}else{
  				ProvidersModel::where('id',$id)->update(['status' => '1']);
  				Toast::success('Provider successfully activated');
  				return redirect()->back();
  			}
   }
   /**
			* Function for delete provider
			*
			* @param null
			*
			* @return view page.
			*/
			public function delete($id = ''){
				if($id){
					// Delete row
					ProvidersModel::where('id',$id)->delete();
					CertificationModel::where('user_id',$id)->delete();
					Toast::success('Provider successfully deleted');
					return redirect()->route('providers');
				}else{
						$checkboxdata = Input::get('chk_ids');
            //prd($checkboxdata);
            //$exploded = explode(',',$checkboxdata);
						ProvidersModel::whereIn('id', $checkboxdata)->delete();
            CertificationModel::whereIn('user_id',$checkboxdata)->delete();
						Toast::success('Provider successfully deleted');
						return Redirect::back();
					 }
			}
      /**
   * Function to edit provider detail
   *
   * @param user id (default null )
   *
   * @return providers detail page
   */
     public function setting($id=0){
               $provider = ProvidersModel::where('id',$id)->first();
               return view('admin.providers.provider-details', compact('provider'));
     }
     public function change_provider_rate(){
       $provider_id = Input::get('provider_id');
       $rate = Input::get('rate');
        $updaterate = ProvidersModel::where('id', $provider_id)->update(array('rate' => $rate));
        if($updaterate){
          echo 1;
        }else{
          echo 0;
        }
     }
     public function edit_provider_calender(){
              $provider_id	   =		Input::get('provider_id');
    					$mileage_info	   =		Input::get('mileage_info');
    					$drive_time			 =	  Input::get('drive_time');
    					$time_card	     =	  Input::get('time_card');

     					$result = ProvidersModel::where('id', $provider_id)->update(array('mileage_info' => $mileage_info,'drive_time' => $drive_time,'time_card' => $time_card));
              if($result){
                echo 1;
              }
              else {
                echo 0;
              }
    	 }
       public function edit_provider_clockout(){
                $clockout_time	   =		Input::get('clockout_time');
      					$clockout_distance	   =		Input::get('clockout_distance');
      					$provider_id			 =	  Input::get('provider_id');

       					$result = ProvidersModel::where('id', $provider_id)->update(array('clockout_time' => $clockout_time,'clockout_distance' => $clockout_distance));
                if($result){
                  echo 1;
                }
                else {
                  echo 0;
                }
      	 }
         public function view_security_pin(){
                  $provider_id	   =		Input::get('provider_id');
                  $password	   =		Input::get('password');
                  if(Hash::check($password,Auth::user()->password)){
                    $provider_details = ProvidersModel::where('id', $provider_id)->get()->toArray();
                    $social_security_pin = $provider_details[0]['social_security_number'];
                      echo $social_security_pin;
                  }
                  else{
                    echo 0;
                  }

        	 }
       /**
   			 * Function for Provider Data Export
   			 *
   			 * @param null
   			 *
   			 * @return file
   			 */
   			public function downloadExcel(Request $request, $id = '')
   			{
				$type = 'csv';
   				$data	= ProvidersModel::select('first_name','last_name','email','phone','address','provider_type','hourly_rate','max_hours','mileage_info','drive_time','time_card','created_at')
				  ->where('id',$id)
				  ->orderBy('id','DESC')->get()->toArray();
   				return Excel::create('provider_details', function($excel) use ($data) {
   					$excel->sheet('mySheet', function($sheet) use ($data)
   					{
   						$sheet->fromArray($data);
   					});
   				})->download($type);
   			}
        public function provider_calender_view($id=0){
                //  $provider = ProvidersModel::where('id',$id)->first();
                  $tasks = DB::table('clinics')->join('clinic_status', 'clinic_status.clinic_id', '=', 'clinics.id')->where('clinic_status.provider_id',$id)->where('clinic_status.status',1)->whereNotNull('clock_in')->whereNotNull('clock_out')->get();
                  return view('admin.providers.provider-calender',compact('tasks'));
        }
        public function provider_finance_view($id=0){
                  $finance_reports = DB::table('clinics')->join('clinic_status', 'clinic_status.clinic_id', '=', 'clinics.id')->where('clinic_status.provider_id',$id)->where('clinic_status.status',1)->whereNotNull('clinic_status.clock_in')->whereNotNull('clinic_status.clock_out')->get();
                  //prd($finance_reports);
                  return view('admin.providers.provider-finance-report',compact('finance_reports'));
        }
}
