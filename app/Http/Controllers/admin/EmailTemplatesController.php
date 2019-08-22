<?php
	namespace App\Http\Controllers\admin;
	use App\Http\Controllers\BaseController;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\admin\MailVariables;
	use App\Model\admin\AdminEmailTemplate;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator,Toast;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class EmailTemplatesController extends BaseController {

		/* Function to display Email templates list. */
		 function EmailTemplatesList(){
			if(!Auth::check()){
				Return Redirect::to('login');
			}
				$emailtemplates = AdminEmailTemplate::GetTemplates();
				return view('admin.pages.emailtemplates.index', ['templates' => $emailtemplates]);
		 }

	public function Create(){
		Input::replace($this->arrayStripTags(Input::all()));
		if(Input::isMethod('post')){
			$rules = array(
			'templatename'  => 'required',
			'subject'  		=> 'required',
			'body'      	=> 'required',
			);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()) {
					$messages = $validator->messages();
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
						$constants	=	$this->GetEmailConstants(Input::get('body'));
						$template 	= 	AdminEmailTemplate::Create(implode(',',$constants)); //calls function for create email template.
						Session::flash('flash_message_success', trans("Email template successfully created."));
						return redirect()->route('emailtemplateslist');
					}
		}else{
			$obj 		= 	new MailVariables;
			$constants 	= 	$obj->GetConstants(); // calls function for get all action variables.
			return View::make('admin.pages.emailtemplates.add',['options' => $constants]);
		  }
	}
	public function Edit($id){
		Input::replace($this->arrayStripTags(Input::all()));
		if(Input::isMethod('post')){
			$rules = array(
			'templatename'  => 'required',
			'subject'  		=> 'required',
			'body'      	=> 'required',
			);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()) {
					$messages = $validator->messages();
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
						$constants	=	$this->GetEmailConstants(Input::get('body'));
						AdminEmailTemplate::UpdateTemplate($id,implode(',',$constants));  //calls function for create email template.
						Toast::success('Email successfully updated');
						//Session::flash('flash_message_success', trans("Email template successfully updated."));
						return redirect()->route('emailtemplateslist');
					}
		}else{
			$obj 		= 	new MailVariables;
			$constants 	= 	$obj->GetConstants(); // calls function for get all action variables.
			$template	=	AdminEmailTemplate::GetTemplateById($id);
			return View::make('admin.pages.emailtemplates.edit',['options' => $constants,'template'=>$template]);
		  }
	}
}
