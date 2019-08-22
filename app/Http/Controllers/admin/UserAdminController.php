<?php

namespace App\Http\Controllers\admin;
//use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Model\admin\UserAdminModel;
use App\Model\admin\AdminNotifications;
use App\Model\AdminOrderType;
use App\Model\AdminFieldTypes;
use Storage;
use League\Flysystem\Filesystem;
use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Excel,Toast;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserAdminController extends BaseController
{
  public function index(){
	$admins = UserAdminModel::where('role_id',1)->orderBy('id','desc')->get();
	return  View::make('admin.admins.index',compact('admins'));
  }
  // function to display user data in databale using ajax
    /**
   * Function for get users data use in ajax datatable pagination.
   *
   * @param null
   *
   * @return users ajax list page.
   */
    public function ajaxloadadmin(){
      $length					= 	Input::get('length');
      $start					= 	Input::get('start');
      $search					= 	Input::get('search');
      $totaldata 				= 	UserAdminModel::where('role_id','=',1)->count();
      $total_filtered_data	  	=		$totaldata;
      $search					=		$search['value'];
      $order					=		Input::get('order');
      $column_id				=		$order[0]['column'];
      $column_order				=		$order[0]['dir'];
      if($search != null){
        $userdata		=		UserAdminModel::GetAdmin($search,$start,$length,$column_id,$column_order);
      }else{
        $userdata		=	UserAdminModel::GetAdmin($search="",$start,$length,$column_id,$column_order);
      }
      $table_data		=	array();
      return view('admin.admins.indexajax', ['userdata' => $userdata,'total_data' => $totaldata ,'total_filtered_data' => $total_filtered_data ]);
    }
  public function add(){
	   Input::replace($this->arrayStripTags(Input::all()));
		if(Input::isMethod('post')){
		  $rules = array(
		  'first_name'    	       => 'required',
		  'last_name'  		       => 'required',
		  'email'      		       => 'required|email|unique:users,email',
		  'password'   		       => 'required',
		  'confirm_password'       => 'required|same:password',
		  'phone'  			       => 'required',
		  'security_pin'           => 'required|numeric|digits:4',
		  );
		$validator = Validator::make(Input::all(),$rules);
		  if ($validator->fails()){
			$messages = $validator->messages();
			 return Redirect::back()->withErrors($validator)->withInput();
		  }else{
			  $insert_id 	= 	UserAdminModel::SaveAdmin();  //calls function for create provider.
			  
			   Toast::success('Admin successfully created');
			   if($insert_id){
               $name          =   Input::get('first_name').' '.Input::get('last_name');
               $password      =   Input::get('password');
               $email      =   Input::get('email');
               $message       =   'Addition of new admin'.' '.$name;
			   $all_admins   = DB::table('users')->where('role_id',1)->where('status',1)->where('id','!=',$insert_id)->get();
				foreach($all_admins as $admin){
					$admin_id     = $admin->id;
					$notification_type = $this->GetAdminNotificationTypeSettings($admin->id);
					if($notification_type == 'email'){
					// email sending process starts
					$type 					=	'new_admin_added';
					$check_status			=	$this->CheckMailSentStatus($insert_id,$admin->id,$type);
					if($check_status == 0){
						$subject_replace		=   array($admin->first_name.' '.$admin->last_name);
						$replace_variables =   array($admin->first_name.' '.$admin->last_name,$email,$password);
						$email_send    =   $this->mail_send('new_admin_added',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_variables,null,$insert_id,$admin->id,$type);
					}	
					// email sending process ends
					}elseif($notification_type == 'push'){
						if(($admin->admin_added_notify != '' || $admin->admin_added_notify != null) && $admin->admin_added_notify != 'off'){
						$admin_notifications 	= 	AdminNotifications::where('required_id',$insert_id)->where('user_id',$admin_id)->where('type','admin')->where('notification_type','new_admin')->get()->count();
						if($admin_notifications == 0){
							$this->save_admin_notification($insert_id,'admin','new_admin',$message,$admin_id);
						}
					}
					}elseif($notification_type == 'both'){
						if(($admin->admin_added_notify != '' || $admin->admin_added_notify != null) && $admin->admin_added_notify != 'off'){
						$admin_notifications 	= 	AdminNotifications::where('required_id',$insert_id)->where('user_id',$admin_id)->where('type','admin')->where('notification_type','new_admin')->get()->count();
							if($admin_notifications == 0){
								$this->save_admin_notification($insert_id,'admin','new_admin',$message,$admin_id);
							}
							// email sending process starts
							$type 					=	'new_admin_added';
							$check_status			=	$this->CheckMailSentStatus($insert_id,$admin->id,$type);
							if($check_status == 0){
								$subject_replace		=   array($admin->first_name.' '.$admin->last_name);
								$replace_variables =   array($admin->first_name.' '.$admin->last_name,$email,$password);
								$email_send    =   $this->mail_send('new_admin_added',$admin->email,$admin->first_name.' '.$admin->last_name,$subject_replace,$replace_variables,null,$insert_id,$admin->id,$type);
							}	
							// email sending process ends
						}	
					}elseif($notification_type == 'none'){
						// no notifications goes nothing
					}		
				}
             }
			  return redirect()->route('admins');
			}
	  }else{
		  return View::make('admin.admins.add');
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
	   if(Input::get('security_pin')!=null){
		 $security_number_validation = 'required|digits:4';
		 }else{
		   $security_number_validation = '';
		 }
		$rules = array(
		  'first_name'    	       => 'required',
		  'last_name'  		         => 'required',
		  'email'      		         => 'required|email|unique:users,id',
		  'phone'  			           => 'required',
		  'security_pin'  			   => $security_number_validation,
		);
	  $validator = Validator::make(Input::all(),$rules);
		   if ($validator->fails()) {
			$messages = $validator->messages();
			return Redirect::back()->withErrors($validator)->withInput();
			} else {
					$UpdateProvider = UserAdminModel::UpdateAdmin($id);
					  if($UpdateProvider){
						 Toast::success('Admin successfully updated');
						return redirect()->route('admins');
					  }else{
						 Toast::error('Technical error');
						return redirect()->route('admins');
					  }
				}
		   }else{
				  $admin = UserAdminModel::where('id',$id)->first();
				  return view('admin.admins.edit', compact('admin'));
		   }
}
public function active_status($id=0){
    $category = UserAdminModel::where('id',$id)->first();
      if($category->status == '1'){
        UserAdminModel::where('id',$id)->update(['status' => '0']);
        Toast::success('Admin successfully deactivated');
        return redirect()->route('admins');
      }else{
        UserAdminModel::where('id',$id)->update(['status' => '1']);
        Toast::success('Admin successfully activated');
        return redirect()->route('admins');
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
        UserAdminModel::where('id',$id)->delete();
        Toast::success('Admin successfully deleted');
        return Redirect::back();
      }else{
          $checkboxdata = Input::get('chk_ids');
          UserAdminModel::whereIn('id', $checkboxdata)->delete();
          Toast::success('Admins successfully deleted');
          return Redirect::back();
         }
    }
}
