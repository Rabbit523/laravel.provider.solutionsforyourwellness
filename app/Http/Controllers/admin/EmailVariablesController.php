<?php
	namespace App\Http\Controllers\admin;
	use App\Http\Controllers\BaseController;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\admin\MailVariables;
	use App\Model\admin\AdminEmailTemplate;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class EmailVariablesController extends BaseController {

		/* Function to display Email templates list */
		 function VariablesList(){
			if(!Auth::check()){
				Return Redirect::to('login');
			}
				$variables = MailVariables::GetVariables();
				return view('admin.pages.emailvariables.index', ['variables' => $variables]);
		 }

	public function Create(){
		Input::replace($this->arrayStripTags(Input::all()));
		if(Input::isMethod('post')){
			$rules = array(
			'variable_name'  => 'required',
			'description'  		=> 'required',
			);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()) {
					$messages = $validator->messages();
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
						MailVariables::Create(); //calls function for create email template.
						Session::flash('flash_success', trans("Email template successfully created."));
						return redirect()->route('email_variables_list');
					}
		}else{
			return View::make('admin.pages.emailvariables.add');
		  }
	}
	public function Edit($id){
		Input::replace($this->arrayStripTags(Input::all()));
		if(Input::isMethod('post')){
			$rules = array(
			'variable_name'  => 'required',
			'description'  		=> 'required',
			);
			$validator = Validator::make(Input::all(),$rules);
				if ($validator->fails()) {
					$messages = $validator->messages();
					return Redirect::back()->withErrors($validator)->withInput();
				}else{
						MailVariables::UpdateVariable($id);  //calls function for create email template.
						Session::flash('flash_success', trans("Email template successfully updated."));
						return redirect()->route('email_variables_list');
					}
		}else{
			$variable = MailVariables::GetVariableById($id); // calls function for get action variables by id.
			return View::make('admin.pages.emailvariables.edit',['variable' => $variable]);
		  }
	}
}
