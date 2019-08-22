<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\PushNotificationsController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\AnnouncementModel;
use App\Model\admin\ProvidersModel;
use App\Model\admin\CitiesModel;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use App\Model\Notifications;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast,Image;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AnnouncementController extends BaseController
{
  public function index(){
    $announcement = AnnouncementModel::orderBy('id','desc')->get();
    return  View::make('admin.announcement.index',compact('announcement'));
  }
  // function to display user data in databale using ajax
    /**
   * Function for get users data use in ajax datatable pagination.
   *
   * @param null
   *
   * @return users ajax list page.
   */
    public function ajaxloadannouncement(){
      $length				= 	Input::get('length');
      $start				= 	Input::get('start');
      $search				= 	Input::get('search');
	$totaldata 			= 	AnnouncementModel::count();
      $total_filtered_data	=	$totaldata;
      $search				=	$search['value'];
      $order				=	Input::get('order');
      $column_id			=	$order[0]['column'];
      $column_order			=	$order[0]['dir'];
      if($search != null){
        $announcements		=	AnnouncementModel::GetAnnouncement($search,$start,$length,$column_id,$column_order);
      }else{
        $announcements		=	AnnouncementModel::GetAnnouncement($search="",$start,$length,$column_id,$column_order);
      }
      $table_data			=	array();
      return view('admin.announcement.indexajax', ['announcements' => $announcements,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
    }
  public function add(){
   Input::replace($this->arrayStripTags(Input::all()));
    if(Input::isMethod('post')){
      $rules = array(
      'title'    	    => 'required',
      'description'  	=> 'required',
      );
    $validator = Validator::make(Input::all(),$rules);
      if ($validator->fails()){
        $messages = $validator->messages();
         return Redirect::back()->withErrors($validator)->withInput();
      }else{
          $image			=		Input::file('image');
          $title			=		Input::get('title');
          $description		=		Input::get('description');
          if($image){
			  /* image uploading process start */
			  $filename  		= 	time().'.'.$image->getClientOriginalExtension();
              $fileExtension 	= 	$image->getClientOriginalExtension();
              $AllowedExts 		= 	array("jpeg", "jpg", "JPG", "png");
              if(in_array($fileExtension,$AllowedExts)){
				$path 		= 	public_path('uploads/announcement/'.$filename);
				$image_path = 	WEBSITE_UPLOADS_URL.'announcement/'.$filename;
				Image::make($image->getRealPath())->save($path);
				$id 	= 	AnnouncementModel::SaveAnnouncement($filename);
				if($id){
					/* email sending process starts */
					if(!empty(Input::get('visible_providers'))){
						$providers = Input::get('visible_providers');
					}else{
						$providers = ProvidersModel::getallproviders();
					}
					if(Input::get('email_alert') == 1 ){  // if email alert is on then send email to all user.
						foreach($providers as 	$provider){
							$user_data 		=	ProvidersModel::GetUserById($provider);
							$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
							$subject_replace=   array($user_name);
							$replace_array 	=   array($user_name,$title,$image_path,$description);
							$email_send    	=   $this->mail_send('new_announcement',$user_data->email,$user_name,$subject_replace,$replace_array);
						}
					}elseif(!empty(Input::get('email_alert'))){
						if(!empty($providers)){
							foreach($providers as $provider){   // if email alert is off then send email to user only when settings found.
							$user_data 			=	ProvidersModel::GetUserById($provider);
							if($user_data->email_notification == 1){
								$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
								$subject_replace=   array($user_name);
								$replace_array 	=   array($user_name,$image_path,$title,$description);
								$email_send    	=   $this->mail_send('new_announcement',$user_data->email,$user_name,$subject_replace,$replace_array);
							}
						}
					  }
					}
					/* email sending process ends */
				}
              // Start Insert Notification
                $announcement = AnnouncementModel::where('id',$id)->first();
                if($announcement->notification_alert == 1){
                  $type = 'instant';
                }else{
                  $type = 'app_setting';
                }
				if(($announcement->visible_providers != null) && ($announcement->visible_cities == null)){
					$exp = explode(",", $announcement->visible_providers);
				}elseif(($announcement->visible_providers == null) && ($announcement->visible_cities != null)){
					$exploded_data = explode(",", $announcement->visible_cities);
					$users = ProvidersModel::select('id')->whereIn('city_name',$exploded_data)->get()->toArray();
					foreach($users as $user){
						$exp[] = $user['id'];
					}
				}elseif(($announcement->visible_cities != null) && ($announcement->visible_providers != null)){
					$users_with_city 	= ProvidersModel::select('id')->whereIn('city_name',explode(",",$announcement->visible_cities))->get()->toArray();
					   foreach($users_with_city as $users_city){
						 $users_citys[] = $users_city['id'];
						}
					  $exploded_providers = explode(",", $announcement->visible_providers);
						$exp = array_unique(array_merge($users_citys,$exploded_providers), SORT_REGULAR);
				}else{
					$users_arrays = ProvidersModel::select('id')->where('status',1)->get()->toArray(); 
						 foreach($users_arrays as $users_array){
							 $exp[] = $users_array['id'];
						 }
				}
                
                foreach ($exp as $key => $value) {
                  $notification = Notifications::where('type','announcement')->where('user_id',$value)->where('required_id',$id)->first();
                  if(empty($notification)){
                    $model = new Notifications();
                    $model->user_id = $value;
                    $model->required_id = $id;
                    $model->announcement_type = $type;
                    $model->type = 'announcement';
                    $model->status = 'not_sent';
                    $model->save();
                  }
                }
                if($announcement->notification_alert == 1){
                  $this->InstantAnnouncementNotification($exp,$id);
                }
                // End Insert Notification


               Toast::success('Announcement successfully added');
             }
             else{
                 Toast::error('Only jpg png file are allowed to upload');
                 return Redirect::back()->withInput();
             }
							 /* image uploading process end */
						}
            else{
              $filename = null;
              $id 	= 	AnnouncementModel::SaveAnnouncement($filename);
			  
			  /* email sending process starts */
					if(!empty(Input::get('visible_providers'))){
						$providers = Input::get('visible_providers');
					}else{
						$providers = ProvidersModel::getallproviders();
					}
					if(Input::get('email_alert') == 1){  // if email alert is on then send email to all user.
						foreach($providers as 	$provider){
							$user_data 		=	ProvidersModel::GetUserById($provider);
							$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
							$subject_replace=   array($user_name);
							$replace_array 	=   array($user_name,$title,$description);
							$email_send    	=   $this->mail_send('new_announcement',$user_data->email,$user_name,$replace_array,$replace_array);
						}
					}else{
						if(!empty($providers)){
							foreach($providers as $provider){   // if email alert is off then send email to user only when settings found.
							$user_data 			=	ProvidersModel::GetUserById($provider);
							if($user_data->email_notification == 1){
								$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
								$subject_replace=   array($user_name);
								$replace_array 	=   array($user_name,$title,$description);
								$email_send    	=   $this->mail_send('new_announcement',$user_data->email,$user_name,$replace_array,$replace_array);
							}
						}
					  }
					}
					/* email sending process ends */

              // Start Insert Notification
                $announcement = AnnouncementModel::where('id',$id)->first();
                if($announcement->notification_alert == 1){
                  $type = 'instant';
                }else{
                  $type = 'app_setting';
                }
				if(($announcement->visible_providers != null) && ($announcement->visible_cities == null)){
					$exp = explode(",", $announcement->visible_providers);
				}elseif(($announcement->visible_providers == null) && ($announcement->visible_cities != null)){
					$exploded_data = explode(",", $announcement->visible_cities);
					$users = ProvidersModel::select('id')->whereIn('city_name',$exploded_data)->get()->toArray();
					foreach($users as $user){
						$exp[] = $user['id'];
					}
				}elseif(($announcement->visible_cities != null) && ($announcement->visible_providers != null)){
					$users_with_city 	= ProvidersModel::select('id')->whereIn('city_name',explode(",",$announcement->visible_cities))->get()->toArray();
					   foreach($users_with_city as $users_city){
						 $users_citys[] = $users_city['id'];
						}
					  $exploded_providers = explode(",", $announcement->visible_providers);
						$exp = array_unique(array_merge($users_citys,$exploded_providers), SORT_REGULAR);
				}else{
					$users_arrays = ProvidersModel::select('id')->where('status',1)->get()->toArray(); 
						 foreach($users_arrays as $users_array){
							 $exp[] = $users_array['id'];
						 }
				}
				
                foreach ($exp as $key => $value) {
                  $notification = Notifications::where('type','announcement')->where('user_id',$value)->where('required_id',$id)->first();
                  if(empty($notification)){
                    $model = new Notifications();
                    $model->user_id = $value;
                    $model->required_id = $id;
                    $model->announcement_type = $type;
                    $model->type = 'announcement';
                    $model->status = 'not_sent';
                    $model->save();
                  }
                }
                if($announcement->notification_alert == 1){
                  $this->InstantAnnouncementNotification($exp,$id);
                }
                // End Insert Notification

               Toast::success('Announcement successfully added');
            }

          return redirect()->route('announcement');
        }
  }else{
      $cities    	= ProvidersModel::select('city_name')->where('role_id',0)->distinct()->get();
	  $providers    = ProvidersModel::where('role_id',0)->get();
        return  View::make('admin.announcement.add',compact('providers','cities'));
     }
 }
 /**
* Function to edit admin
*
* @param user id (default null )
*
* @return admins list page
*/
public function edit($id=0){
 if(Input::isMethod('post')){
    $rules = array(
      'title'    	       => 'required',
      'description'  		 => 'required',
    );
  $validator = Validator::make(Input::all(),$rules);
   if ($validator->fails()) {
    $messages = $validator->messages();
    return Redirect::back()->withErrors($validator)->withInput();
    } else {
            if(Input::file('image')){
              $fileExtension = Input::file('image')->getClientOriginalExtension();
              $AllowedExts = array("jpeg", "jpg", "JPG", "png");
                if(in_array($fileExtension,$AllowedExts)){
                  $filename  = 	$this->ImageUpload(Input::file('image'),$folder='announcement',$resize=false);
                }
                else{
                    Toast::error('Only jpg png file are allowed to upload');
                    return Redirect::back()->withInput();
                }
              }else{
                $announcement	=	AnnouncementModel::where('id',$id)->first();  // getting announcement data by id.
                $filename		=	$announcement->image;
              }
            $UpdateProvider = AnnouncementModel::UpdateAnnouncement($filename,$id);
			if($UpdateProvider){
				// Start Insert Notification
                      $announcement = AnnouncementModel::where('id',$id)->first();
                      if($announcement->notification_alert == 1){
                        $type = 'instant';
                      }else{
                        $type = 'app_setting';
                      }
                      if(($announcement->visible_providers != null) && ($announcement->visible_cities == null)){
							$exp = explode(",", $announcement->visible_providers);
						}elseif(($announcement->visible_providers == null) && ($announcement->visible_cities != null)){
							$exploded_data = explode(",", $announcement->visible_cities);
							$users = ProvidersModel::select('id')->whereIn('city_name',$exploded_data)->get()->toArray();
							foreach($users as $user){
								$exp[] = $user['id'];
							}
						}elseif(($announcement->visible_cities != null) && ($announcement->visible_providers != null)){
							$users_with_city 	= ProvidersModel::select('id')->whereIn('city_name',explode(",",$announcement->visible_cities))->get()->toArray();
							   foreach($users_with_city as $users_city){
								 $users_citys[] = $users_city['id'];
								}
							  $exploded_providers = explode(",", $announcement->visible_providers);
								$exp = array_unique(array_merge($users_citys,$exploded_providers), SORT_REGULAR);
						}else{
							$users_arrays = ProvidersModel::select('id')->where('status',1)->get()->toArray(); 
								 foreach($users_arrays as $users_array){
									 $exp[] = $users_array['id'];
								 }
						}
                      foreach ($exp as $key => $value) {
                        $notification = Notifications::where('type','announcement')->where('user_id',$value)->where('required_id',$id)->first();
                        if(empty($notification)){
                          $model = new Notifications();
                          $model->user_id = $value;
                          $model->required_id = $id;
                          $model->announcement_type = $type;
                          $model->type = 'announcement';
                          $model->status = 'not_sent';
                          $model->save();
                        }
                      }
                      if($announcement->notification_alert == 1){
                        $this->InstantAnnouncementNotification($exp,$id);
                      }
                      // End Insert Notification
					  
					/* email sending process starts */
					if(!empty(Input::get('visible_providers'))){
						$providers = Input::get('visible_providers');
					}else{
						$providers = ProvidersModel::getallproviders();
					}
					$image_path = 	WEBSITE_UPLOADS_URL.'announcement/'.$announcement->image;
					if(Input::get('email_alert') == 1){  // if email alert is on then send email to all user.
						foreach($providers as 	$provider){
							$title     		=   Input::get('title');
							$description    =   Input::get('description');
							$user_data 		=	ProvidersModel::GetUserById($provider);
							$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
							$subject_replace=   array($user_name);
							$replace_array 	=   array($user_name,$title,$image_path,$description);
							$email_send    	=   $this->mail_send('announcement_updated',$user_data->email,$user_name,$subject_replace,$replace_array);
						}
					}else{
						if(!empty($providers)){
							foreach($providers as $provider){   // if email alert is off then send email to user only when settings found.
							$user_data 			=	ProvidersModel::GetUserById($provider);
							if($user_data->email_notification == 1){
								$title     		=   Input::get('title');
								$description    =   Input::get('description');
								$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
								$subject_replace=   array($user_name);
								$replace_array 	=   array($user_name,$title,$image_path,$description);
								$email_send    	=   $this->mail_send('announcement_updated',$user_data->email,$user_name,$subject_replace,$replace_array);
							}
						}
					  }
					}
					/* email sending process ends */
			}
			
              if($UpdateProvider){
                 Toast::success('Announcement successfully updated');
                return redirect()->route('announcement');
              }else{
                 Toast::error('Technical error');
                return redirect()->route('announcement');
              }
        }
   }else{
		$providers    	= ProvidersModel::where('role_id',0)->get();
        $cities    		= ProvidersModel::select('city_name')->where('role_id',0)->distinct()->get();
        $announcement = AnnouncementModel::where('id',$id)->first();
        return view('admin.announcement.edit', compact('announcement','providers','cities'));
   }
}
	public function active_status($id=0){
    $category = AnnouncementModel::where('id',$id)->first();
      if($category->status == '1'){
        AnnouncementModel::where('id',$id)->update(['status' => '0']);
        Toast::success('Announcement successfully deactivated');
        return redirect()->route('announcement');
      }else{
        AnnouncementModel::where('id',$id)->update(['status' => '1']);
        Toast::success('Announcement successfully activated');
        return redirect()->route('announcement');
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
        AnnouncementModel::where('id',$id)->delete();
        Toast::success('Announcement successfully deleted');
        return Redirect::back();
      }else{
          $checkboxdata = Input::get('chk_ids');
          AnnouncementModel::whereIn('id', $checkboxdata)->delete();
          Toast::success('Announcements successfully deleted');
          return Redirect::back();
         }
    }
    /**
   * Function to edit announcement setting
   *
   * @param user id (default null )
   *
   * @return announcement setting page
   */
     public function setting($id=0){
       if(Input::isMethod('post')){
          $rules = array();
        $validator = Validator::make(Input::all(),$rules);
         if ($validator->fails()) {
          $messages = $validator->messages();
          return Redirect::back()->withErrors($validator)->withInput();
          } else {
                  $UpdateSetting = AnnouncementModel::UpdateAnnouncementSetting($id);
                    if($UpdateSetting){

                      // Start Insert Notification
                      $announcement = AnnouncementModel::where('id',$id)->first();
					  $title     		=   $announcement->title;
					  $description    	=   $announcement->description;
                      if($announcement->notification_alert == 1){
                        $type = 'instant';
                      }else{
                        $type = 'app_setting';
                      }
                      if(($announcement->visible_providers != null) && ($announcement->visible_cities == null)){
							$exp = explode(",", $announcement->visible_providers);
						}elseif(($announcement->visible_providers == null) && ($announcement->visible_cities != null)){
							$exploded_data = explode(",", $announcement->visible_cities);
							$users = ProvidersModel::select('id')->whereIn('city_name',$exploded_data)->get()->toArray();
							foreach($users as $user){
								$exp[] = $user['id'];
							}
						}elseif(($announcement->visible_cities != null) && ($announcement->visible_providers != null)){
							$users_with_city 	= ProvidersModel::select('id')->whereIn('city_name',explode(",",$announcement->visible_cities))->get()->toArray();
							   foreach($users_with_city as $users_city){
								 $users_citys[] = $users_city['id'];
								}
							  $exploded_providers = explode(",", $announcement->visible_providers);
								$exp = array_unique(array_merge($users_citys,$exploded_providers), SORT_REGULAR);
						}else{
							$users_arrays = ProvidersModel::select('id')->where('status',1)->get()->toArray(); 
								 foreach($users_arrays as $users_array){
									 $exp[] = $users_array['id'];
								 }
						}
                      foreach ($exp as $key => $value) {
                        $notification = Notifications::where('type','announcement')->where('user_id',$value)->where('required_id',$id)->first();
                        if(empty($notification)){
                          $model = new Notifications();
                          $model->user_id = $value;
                          $model->required_id = $id;
                          $model->announcement_type = $type;
                          $model->type = 'announcement';
                          $model->status = 'not_sent';
                          $model->save();
                        }
                      }
                      if($announcement->notification_alert == 1){
                        $this->InstantAnnouncementNotification($exp,$id);
                      }
                      // End Insert Notification
					  
					  /* email sending process starts */
					if(!empty(Input::get('visible_providers'))){
						$providers = Input::get('visible_providers');
					}else{
						$providers = ProvidersModel::getallproviders();
					}
					$image_path = 	WEBSITE_UPLOADS_URL.'announcement/'.$announcement->image;
					if(Input::get('email_alert') == 1){  // if email alert is on then send email to all user.
						foreach($providers as 	$provider){
							$user_data 		=	ProvidersModel::GetUserById($provider);
							$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
							$subject_replace=   array($user_name);
							$replace_array 	=   array($user_name,$title,$image_path,$description);
							$email_send    	=   $this->mail_send('announcement_updated',$user_data->email,$user_name,$subject_replace,$replace_array);
						}
					}else{
						if(!empty($providers)){
							foreach($providers as $provider){   // if email alert is off then send email to user only when settings found.
							$user_data 			=	ProvidersModel::GetUserById($provider);
							if($user_data->email_notification == 1){
								$user_name     	=   $user_data->first_name ." ". $user_data->last_name;
								$subject_replace=   array($user_name);
								$replace_array 	=   array($user_name,$title,$image_path,$description);
								$email_send    	=   $this->mail_send('announcement_updated',$user_data->email,$user_name,$subject_replace,$replace_array);
							}
						}
					  }
					}
					/* email sending process ends */

                      Toast::success('Settings successfully updated');
                      return redirect()->route('announcement');
                    }else{
                       Toast::error('Technical error');
                      return redirect()->route('announcement');
                    }
              }
         }else{
           $providers    	= ProvidersModel::where('role_id',0)->get();
           $cities    		= ProvidersModel::select('city_name')->where('role_id',0)->distinct()->get();
           $announcement 	= AnnouncementModel::where('id',$id)->first();
                return view('admin.announcement.setting', compact('announcement','providers','cities'));
         }
     }
}
