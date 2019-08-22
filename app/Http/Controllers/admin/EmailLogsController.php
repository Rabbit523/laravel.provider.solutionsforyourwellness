<?php
	namespace App\Http\Controllers\admin;
	use App\Http\Controllers\BaseController;
	//use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\DB;
	use App\Model\EmailLog;
	use Auth,Blade,Config,Cache,Cookie,File,Input,Html,Mail,mongoDate,Redirect,Response,Session,URL,View,Validator;
	use Illuminate\Http\Request;
	use Illuminate\Foundation\Bus\DispatchesJobs;
	use Illuminate\Foundation\Validation\ValidatesRequests;
	use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

	class EmailLogsController extends BaseController {

		/* Function to display Email templates list */
		 function LogsList(){
			 	$logs 	=		Emaillog::get();
				return view('admin.pages.emaillogs.index',compact('logs'));
		 }
}
