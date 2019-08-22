<?php
namespace App\Model\admin;
use Eloquent,Input;

/**
 * EmailTemplate Model
 */

class AdminEmailTemplate extends Eloquent {


/**
 * The database table used by the model.
 */
	protected $table = 'email_templates';
	/**
	* Function for add new email template.
	*
	* @null
	* @return response true on success and false on failure
	*/
	public static function Create($constants=""){
		$model					=	new AdminEmailTemplate;
		$model->name			=	Input::get('templatename');
		$model->subject			=	Input::get('subject');
		$model->action			=	Input::get('action');
		$model->constants		=	$constants;
		$model->body			=	Input::get('body');
		$saved 					= 	$model->save();
		if(!$saved){
			return false;
		}
		return true;
	}
	/**
	* Function for edit email template.
	*
	* @param template id
	* @return response true on success and false on failure
	*/
	public static function GetTemplates(){
		$template = AdminEmailTemplate::orderBy('id','desc')->get();
		if(empty($template)){
			return false;
		}
		return $template;
	}
  /**
	* Function for edit email template.
	*
	* @param template id
	* @return response true on success and false on failure
	*/
	public static function GetTemplateById($id){
		$template = AdminEmailTemplate::where('id',$id)->first();
		if(empty($template)){
			return false;
		}
		return $template;
	}
	/**
	* Function for edit email template.
	*
	* @param template id
	* @return response true on success and false on failure
	*/
	public static function UpdateTemplate($id,$constants=""){
		$model					=	AdminEmailTemplate::find($id);
		$model->name			=	Input::get('templatename');
		$model->subject			=	Input::get('subject');
		//$model->action			=	Input::get('action');
		$model->constants		=	$constants;
		$model->body			=	Input::get('body');
		$saved 					= 	$model->save();
		if(!$saved){
			return false;
		}
		return true;
	}

}// end EmailTemplate class
