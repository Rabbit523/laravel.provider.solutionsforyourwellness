<?php
namespace App\Model\admin;
use Eloquent,Input;
/**
 * EmailAction Model
 */
class MailVariables extends Eloquent {


/**
 * The database table used by the model.
 *
 * @var string
 */
	protected $table = 'mail_variables';
	public $timestamps = false;
	/**
	* Function for add new email template.
	*
	* @null
	* @return response true on success and false on failure
	*/
	public static function Create(){
		$model															=		new MailVariables;
		$model->variable_name								=		Input::get('variable_name');
		$model->variable_description				=		Input::get('description');
		$saved 															= 	$model->save();
		if(!$saved){
			return false;
		}
		return true;
	}
	/**
	* Function for add new email template.
	*
	* @null
	* @return response true on success and false on failure
	*/
	public static function GetVariables(){
		return $variables = MailVariables::orderBy('id','desc')->get();
	}
	/**
	* Function for add new email template.
	*
	* @null
	* @return response true on success and false on failure
	*/
	public static function GetVariableById($id){
		return $variables = MailVariables::where('id',$id)->first();
	}
	/**
	* Function for edit email actions.
	*
	* @null
	* @return response true on success and false on failure
	*/
	public static function UpdateVariable($id){
		$model												=		MailVariables::find($id);
		$model->variable_name					=		Input::get('variable_name');
		$model->variable_description	=		Input::get('description');
		$saved 												= 	$model->save();
		if(!$saved){
			return false;
		}
		return true;
	}
	/**
	* Function for add new email template.
	*
	* @null
	* @return response true on success and false on failure
	*/
	public static function GetConstants(){
		$actions = MailVariables::select('variable_name')->get()->toArray();
		if(!$actions){
			return false;
		}
		//prd($actions);
		foreach($actions as $key=>$value){
			$data[$value['variable_name']] = $value['variable_name'];
		}
		return $data;
	}

}// end EmailAction class
