<?php
	namespace App\Http\Controllers\admin;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\admin\AdminSettings;
	use App\Model\EmailAction;
	use App\Model\EmailTemplate;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Routing\Controller as BaseController;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class AdminSettingsController extends BaseController {
    public function updatelogo(){
		$logo_data = AdminSettings::where('id',1)->first();
		if(Input::isMethod('post')){
			$rules = array(
			'description'   => 'required',
			);
			$validator = Validator::make(Input::all(),$rules);
			 if ($validator->fails()) {
				$messages = $validator->messages();
				return Redirect::back()->withErrors($validator)->withInput();
			  } else {
						$fieldname		=	'site_logo';
						$description	=	Input::get('description');
						$picture		=	Input::file('logo_image');
						if($picture){
						$image = Input::file('logo_image');
						$filename  = time() . '.' . $image->getClientOriginalExtension();
						$path = public_path('uploads/' . $filename);
						\Image::make($image->getRealPath())->resize(178, 24)->save($path);

						AdminSettings::where('id',1)->update(array('field_name'=>$fieldname,'image'=>$filename,'description'=>$description));
						Session::flash('flash_message_success', trans("Logo successfully updated."));
						return Redirect::to('admin/dashboard');
						}else{
							AdminSettings::where('id',1)->update(array('field_name'=>$fieldname,'description'=>$description,));
							Session::flash('flash_message_success', trans("Logo successfully updated."));
							return Redirect::to('admin/dashboard');
						}
					}
			}else{
				return View::make('admin.pages.settings.updatelogo', ['logo_data' => $logo_data]);
			}
		}
		public function UpdateCopyright(){
 			if(Input::isMethod('post')){
 				$rules = array(
 				'copyright_value' => 'required',
 				);
				AdminSettings::updatecopyright();
				Session::flash('flash_success', trans("Copyright successfully updated."));
				return redirect()->route('dashboard');
		}else{
				$field_name	=	'copyright_text';
				$copyright	=	AdminSettings::GetSettingsByFieldname($field_name);
				return view('admin.pages.settings.copyright', ['copyright' => $copyright]);
		}
	}
}
